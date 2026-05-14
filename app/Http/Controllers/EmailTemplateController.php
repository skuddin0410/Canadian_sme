<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Mail\UserWelcome;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Crypt;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $events = isSuperAdmin()
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::whereIn('id', getEventIds())->orderBy('title')->get(['id', 'title']);

        $templates = EmailTemplate::with('event')
            ->when(!isSuperAdmin(), function ($query) {
                $query->whereIn('event_id', getEventIds());
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('event_id'), function ($query) use ($request) {
                $query->where('event_id', $request->event_id);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('email_templates.index', compact('templates', 'events'));
    }

    public function create()
    {
        $events = isSuperAdmin()
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::whereIn('id', getEventIds())->orderBy('title')->get(['id', 'title']);

        return view('email_templates.create', compact('events'));
    }

  public function store(Request $request)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'template_name' => 'required|unique:email_templates,template_name,NULL,id,event_id,' . $request->event_id,
        'subject' => 'required',
        'type' => 'required|in:email,notifications',
        'message' => [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                // Strip tags if it's a notification before counting length
                $textValue = $request->type === 'notifications' ? strip_tags($value) : $value;
                $max = $request->type === 'notifications' ? 400 : 3000;

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
        $events = isSuperAdmin()
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::whereIn('id', getEventIds())->orderBy('title')->get(['id', 'title']);

        return view('email_templates.edit', compact('emailTemplate', 'events'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'template_name' => 'required|unique:email_templates,template_name,' . $emailTemplate->id . ',id,event_id,' . $request->event_id,
            'subject' => 'required',
            'type' => 'required|in:email,notifications',
            'message' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $textValue = $request->type === 'notifications' ? strip_tags($value) : $value;
                    $max = $request->type === 'notifications' ? 400 : 3000;
                    if (strlen($textValue) > $max) {
                        $fail("The {$attribute} may not be greater than {$max} characters for {$request->type}.");
                    }
                }
            ]
        ]);

        $data = $request->all();

        if ($request->type === 'notifications') {
            $data['message'] = strip_tags($data['message']);
        }

        $emailTemplate->update($data);

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

        $emailTemplate = EmailTemplate::with('event.eventLogo', 'event.photo')->findOrFail($request->template);
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
              $updateUrl = route('update-user',  Crypt::encryptString($request->email));  
              $message = str_replace('{{profile_update_link}}', '<br><a href="' . $updateUrl . '">Update Profile</a>', $message);
        }
        $message = Purifier::clean($message, 'default');
        Mail::to($request->email)->send(new UserWelcome(null, $subject, $message, null, $emailTemplate->event));
        return back()->with('success', 'Email sent successfully!');
    }

}
