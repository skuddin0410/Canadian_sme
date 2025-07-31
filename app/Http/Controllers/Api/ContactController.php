<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;

class ContactController extends Controller
{
    public function index() {
        $settings = Setting::query()
            ->whereIn('key', [
                'contact_email',
                'contact_phone',
                'contact_whatsapp',
                'contact_address',
                'profile_referral',
            ])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('settings'),
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'sometimes|nullable|numeric|integer|digits:10',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $mailData['subject'] = 'Contact: ' . $request->subject;
        $mailData['email'] = env('MAIL_FROM_ADDRESS',);
        $mailData['name'] = env('MAIL_FROM_NAME', 'SME');
        $mailData['body'] = "";
        if ($request->name) {
            $mailData['body'] .= "Name: {$request->name}<br/>";
        }
        if ($request->email) {
            $mailData['body'] .= "Email: {$request->email}<br/>";
        }
        if ($request->mobile) {
            $mailData['body'] .= "Mobile: {$request->mobile}<br/>";
        }
        if ($request->message) {
            $mailData['body'] .= "Message:<br/>
                                {$request->message}";
        }
        // $mailData['url'] = url('login');
        // $mailData['button'] = 'Login';
        // $mailData['subbody'] = "You can also use your registered email & mobile as username.";
        $mailData['info'] = 'Note: don\'t share your login credentials & keep it confedential.';

        \Mail::send('emails.template', $mailData, function ($message) use ($mailData) {
            $message->subject($mailData['subject'])
            ->to($mailData['email'], $mailData['name'])
            // ->cc(User::role('admin')->first()->email)
            ->bcc('chandan.webappssol@gmail.com');
        });

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => $mailData,
        ]);
    }
}
