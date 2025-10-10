<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\UserWelcome;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Mews\Purifier\Facades\Purifier;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(10);
        return view('email_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('email_templates.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'template_name' => 'required|unique:email_templates,template_name',
        'subject' => 'required',
        'type' => 'required|in:email,notifications',
        'message' => [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                // Strip tags if it's a notification before counting length
                $textValue = $request->type === 'notifications' ? strip_tags($value) : $value;
                $max = $request->type === 'notifications' ? 150 : 3000;

                if (strlen($textValue) > $max) {
                    $fail("The {$attribute} may not be greater than {$max} characters for {$request->type}.");
                }
            }
        ]
    ]);

    $data = $request->all();

    // Strip HTML tags for notification before saving
    if ($request->type === 'notifications') {
        $data['message'] = strip_tags($data['message']);
    }

    EmailTemplate::create($data);

    return redirect()->route('email-templates.index')
                     ->with('success', 'Email Template created successfully.');
}


    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email_templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'template_name' => 'required|unique:email_templates,template_name,' . $emailTemplate->id,
            'subject' => 'required',
            'type' => 'nullable|string',
            'message' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $max = $request->type === 'notifications' ? 150 : 3000;
                    if (strlen($value) > $max) {
                        $fail("The {$attribute} may not be greater than {$max} characters for {$request->type}.");
                    }
                }
            ]
        ]);

        $emailTemplate->update($request->all());

        return redirect()->route('email-templates.index')
                         ->with('success', 'Email Template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
                         ->with('success', 'Email Template deleted successfully.');
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'template' => 'required|exists:email_templates,id',
        ]);

        $emailTemplate = EmailTemplate::findOrFail($request->template);
        $subject = $emailTemplate->subject ?? '';
        $subject = str_replace('{{site_name}}', config('app.name'), $subject);
        $subject = str_replace('{{site_name}}', config('app.name'), $subject);

        $qr_code_url = asset("qrcode.png");
        $message = $emailTemplate->message ?? '';
        $message = str_replace('{{name}}', $request->email, $message);
        $message = str_replace('{{site_name}}', config('app.name'), $message);
        if (strpos($message, '{{qr_code}}') !== false) {
          $message = str_replace('{{qr_code}}', '<br><img src="' . $qr_code_url . '" alt="QR Code" />', $message);
        }

        if (strpos($message, '{{profile_update_link}}') !== false) {
              $updateUrl = route('update-user',  Crypt::encryptString($user->id));  
              $message = str_replace('{{profile_update_link}}', '<br><a href="' . $updateUrl . '">Update Profile</a>', $message);
        }
        $message = Purifier::clean($message, 'default');
        Mail::to($request->email)->send(new UserWelcome(null, $subject, $message));
        return back()->with('success', 'Email sent successfully!');
    }

}
