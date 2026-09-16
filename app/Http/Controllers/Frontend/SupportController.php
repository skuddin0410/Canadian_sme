<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactQuery;
use App\Services\AdminInquiryAlertService;
use Illuminate\Contracts\Encryption\DecryptException;

class SupportController extends Controller
{
    public function index()
    {
        return view('new_contact_us');
    }

    public function store(Request $request, AdminInquiryAlertService $alerts)
    {
        // Honeypot filled => treat as spam, fake success so bots don't adapt
        if ($request->filled('website')) {
            return redirect()->back()->with('success', 'Your request has been submitted successfully!');
        }

        // Reject instant bot submits / expired or tampered tokens
        if (!$this->isValidFormTiming($request->input('_form_started'))) {
            return redirect()->back()
                ->withInput($request->except(['website', '_form_started', 'g-recaptcha-response']))
                ->withErrors(['form' => 'Your session expired. Please refresh the page and try again.']);
        }

        if (!$this->verifyRecaptcha($request)) {
            return redirect()->back()
                ->withInput($request->except(['website', '_form_started', 'g-recaptcha-response']))
                ->withErrors(['g-recaptcha-response' => 'Please complete the CAPTCHA and try again.']);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
            'location'    => 'required|string|max:255',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        // Block messages that look like link spam
        if ($this->looksLikeLinkSpam($validated['description'] . ' ' . $validated['subject'])) {
            return redirect()->back()->with('success', 'Your request has been submitted successfully!');
        }

        $support = Support::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'subject'     => $validated['subject'],
            'description' => $validated['description'],
            'status'      => 'pending',
        ]);

        Mail::to($support->email)->send(new ContactQuery($support));

        $alerts->notifySupportRequest($support);

        return redirect()->back()->with('success', 'Your request has been submitted successfully!');
    }

    /**
     * Verify Google reCAPTCHA v2 response with Google's siteverify API.
     */
    private function verifyRecaptcha(Request $request): bool
    {
        $secret = config('services.recaptcha.secret_key');

        // Keys not set yet — keep existing honeypot/rate-limit protection only
        if (empty($secret)) {
            return !app()->environment('production');
        }

        $token = $request->input('g-recaptcha-response');

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return (bool) data_get($response->json(), 'success', false);
    }

    /**
     * Form must be open at least 3 seconds and no more than 2 hours.
     */
    private function isValidFormTiming(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        try {
            $started = (int) decrypt($token);
        } catch (DecryptException $e) {
            return false;
        }

        $elapsed = time() - $started;

        return $elapsed >= 3 && $elapsed <= 7200;
    }

    /**
     * Flag content with many URLs (common spam pattern).
     */
    private function looksLikeLinkSpam(string $text): bool
    {
        $urlCount = preg_match_all('/https?:\/\/|www\./i', $text);

        return $urlCount >= 3;
    }
}
