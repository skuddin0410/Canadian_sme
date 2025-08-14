<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use App\Models\NewsletterSend;
use App\Services\NewsletterService;
use Illuminate\Support\Facades\Log;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class NewsletterController extends Controller
{
     protected $newsletterService;

    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         $newsletters = Newsletter::with('creator')
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

        $stats = [
            'total_newsletters' => Newsletter::count(),
            'sent_newsletters' => Newsletter::sent()->count(),
            'total_subscribers' => NewsletterSubscriber::subscribed()->count(),
            'draft_newsletters' => Newsletter::draft()->count(),
        ];

        return view('users.newsletters.index', compact('newsletters', 'stats'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
          $templates = $this->newsletterService->createInvestorNewsletterTemplates();
        return view('users.newsletters.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_name' => 'required|string',
            'recipient_criteria' => 'nullable|array',
            'template_data' => 'nullable|array',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $newsletter = $this->newsletterService->createNewsletter($request->all());

        if ($request->boolean('send_immediately')) {
            $this->newsletterService->sendNewsletter($newsletter);
            $message = 'Newsletter created and queued for immediate sending!';
        } elseif ($request->filled('scheduled_at')) {
            $this->newsletterService->scheduleNewsletter($newsletter, $request->scheduled_at);
            $message = 'Newsletter scheduled successfully!';
        } else {
            $message = 'Newsletter saved as draft.';
        }

        return redirect()->route('newsletters.show', $newsletter)
                        ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Newsletter $newsletter)
    {
        //
         $newsletter->load(['creator', 'sends']);
        $analytics = $this->newsletterService->getAnalytics($newsletter);
        
        return view('users.newsletters.show', compact('newsletter', 'analytics'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Newsletter $newsletter)
    {
        //
          if (!$newsletter->canBeSent()) {
            return back()->with('error', 'Cannot edit newsletter in current status.');
        }

        $templates = $this->newsletterService->createInvestorNewsletterTemplates();
        return view('users.newsletters.edit', compact('newsletter', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        //
          if (!$newsletter->canBeSent()) {
            return back()->with('error', 'Cannot edit newsletter in current status.');
        }
       
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_name' => 'required|string',
            'recipient_criteria' => 'nullable|array',
            'template_data' => 'nullable|array',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'send_option'=>'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if($request->send_option == 'send_now'){
           $request->request->add(['status' => 'sending' ]);
        }

        if($request->send_option == 'draft'){
           $request->request->add(['status' => 'draft' ]);
        }

        if($request->send_option == 'schedule'){
           $request->request->add(['status' => 'scheduled' ]);
        }
        
        $newsletter->update($request->only([
            'subject', 'content', 'template_name', 'recipient_criteria', 'template_data','status','scheduled_at'
        ]));

        return redirect()->route('newsletters.show', $newsletter)
                        ->with('success', 'Newsletter updated successfully!');
    }
      public function send(Newsletter $newsletter)
    {
        if (!$newsletter->canBeSent()) {
            return back()->with('error', 'Newsletter cannot be sent in current status.');
        }

        $this->newsletterService->sendNewsletter($newsletter);

        return back()->with('success', 'Newsletter queued for sending!');
    }

    /**
     * Schedule newsletter
     */
    public function schedule(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now'
        ]);

        if (!$newsletter->canBeSent()) {
            return back()->with('error', 'Newsletter cannot be scheduled in current status.');
        }

        $this->newsletterService->scheduleNewsletter($newsletter, $request->scheduled_at);

        return back()->with('success', 'Newsletter scheduled successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter)
    {
        //
          if ($newsletter->status === 'sending') {
            return back()->with('error', 'Cannot delete newsletter while sending.');
        }

        $newsletter->delete();

        return redirect()->route('newsletters.index')
                        ->with('success', 'Newsletter deleted successfully!');
   
    }
      public function preview(Newsletter $newsletter)
    {   
        //$newsletter = $this->populatePreviewData($newsletter);
        return view('users.newsletters.preview', compact('newsletter'));
    }

    /**
     * Test send newsletter
     */

public function testSend(Request $request, $newsletterId)
{
    $request->validate([
        'test_email' => 'required|email'
    ]);

    // Step 1: Get the newsletter
    $newsletter = Newsletter::findOrFail($newsletterId);

    // Step 2: Create a dummy NewsletterSend object
    $testSend = new NewsletterSend();
    $testSend->newsletter_id = $newsletter->id;
    $testSend->email = $request->test_email;
    $testSend->sent_at = now();

    try {
        // Step 3: Send email
        \Mail::to($request->test_email)
            ->send(new \App\Mail\NewsletterMail($newsletter, $testSend));
            return back()->with('success', 'Test email sent successfully!');

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Test email sent successfully!'
        // ]);
    } catch (\Exception $e) {
        // Log the actual error
        \Log::error('Test email sending failed: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send test email. Please check logs.'
        ], 500);
    }
}


    



    /**
     * Public subscription form
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'preferences' => 'nullable|array',
            'preferences.*' => 'string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $subscriber = $this->newsletterService->subscribe(
            $request->email,
            $request->name,
            $request->preferences ?? [],
            'website'
        );

        return back()->with('success', 'Successfully subscribed to our newsletter!');
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        $email = $request->get('email');
        
        if (!$email) {
            return view('newsletters.unsubscribe-form');
        }
        $success = $this->newsletterService->unsubscribe($email);
        // if ($success) {
        //     return view('newsletters.unsubscribed', ['email' => $email]);
        // } else {
             return view('newsletters.unsubscribed', ['email' => $email]);
        // }
    }

    /**
     * Track email open
     */
    public function trackOpen(Request $request, Newsletter $newsletter)
    {
        $email = $request->get('email');
        
        if ($email) {
            $this->newsletterService->trackOpen($newsletter, $email);
        }

        // Return 1x1 transparent pixel
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    /**
     * Track email click
     */
    public function trackClick(Request $request, Newsletter $newsletter)
    {
        $email = $request->get('email');
        $url = $request->get('url');
        
        if ($email && $url) {
            $this->newsletterService->trackClick($newsletter, $email, $url);
        }

        return redirect($url);
    }

    /**
     * Subscriber management
     */
    public function subscribers()
    {
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')
                                         ->paginate(50);

        $stats = [
            'total_subscribers' => NewsletterSubscriber::count(),
            'active_subscribers' => NewsletterSubscriber::subscribed()->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
            'bounced' => NewsletterSubscriber::where('status', 'bounced')->count(),
        ];

        return view('admin.newsletters.subscribers', compact('subscribers', 'stats'));
    }

    /**
     * Export subscribers
     */
    public function exportSubscribers()
    {
        $subscribers = NewsletterSubscriber::subscribed()->get();

        $filename = 'newsletter_subscribers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Email', 'Name', 'Subscribed At', 'Preferences', 'Tags']);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->subscribed_at?->format('Y-m-d H:i:s'),
                    implode(', ', $subscriber->preferences ?? []),
                    implode(', ', $subscriber->tags ?? [])
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function populatePreviewData(Newsletter $newsletter){
    // For preview purposes, we can add sample data for dynamic templates
    $templateData = $newsletter->template_data ?? [];
    
    switch ($newsletter->template_name) {
        case 'market_update':
            if (!isset($templateData['market_stats'])) {
                $templateData['market_stats'] = [
                    ['label' => 'Avg. Property Price', 'value' => '$485K', 'trend' => 'trend-up'],
                    ['label' => 'New Listings', 'value' => '342', 'trend' => 'trend-up'],
                    ['label' => 'Days on Market', 'value' => '28', 'trend' => 'trend-down'],
                    ['label' => 'Interest Rate', 'value' => '6.8%', 'trend' => 'trend-up']
                ];
            }
            
            if (!isset($templateData['featured_properties'])) {
                $templateData['featured_properties'] = [
                    [
                        'title' => 'Luxury Downtown Condo',
                        'location' => 'Downtown District',
                        'price' => 650000,
                        'description' => 'Modern 2-bedroom condo with city views and premium amenities.',
                        'url' => route('properties.show', 1)
                    ],
                    [
                        'title' => 'Suburban Family Home',
                        'location' => 'Maple Heights',
                        'price' => 425000,
                        'description' => 'Spacious 4-bedroom home perfect for growing families.',
                        'url' => route('properties.show', 2)
                    ]
                ];
            }
            break;
            
        case 'new_properties':
            if (!isset($templateData['new_properties'])) {
                $templateData['new_properties'] = [
                    [
                        'title' => 'Investment Opportunity - Duplex',
                        'location' => 'Riverside Area',
                        'price' => 320000,
                        'roi' => '8.5%',
                        'description' => 'Great rental income potential in growing neighborhood.',
                        'url' => route('properties.show', 3)
                    ],
                    [
                        'title' => 'Commercial Building',
                        'location' => 'Business District',
                        'price' => 850000,
                        'roi' => '7.2%',
                        'description' => 'Prime location commercial property with multiple tenants.',
                        'url' => route('properties.show', 4)
                    ]
                ];
            }
            break;
            
        case 'investment_tips':
            if (!isset($templateData['tip_of_week'])) {
                $templateData['tip_of_week'] = [
                    'title' => 'Understanding Cap Rates',
                    'summary' => 'Learn how to calculate and use capitalization rates to evaluate investment properties.',
                    'read_more_url' =>"#"
                ];
            }
            break;
    }
    
    // Set default CTA if not provided
    if (!isset($templateData['cta_url'])) {
        $templateData['cta_url'] = route('properties');
    }
    
    if (!isset($templateData['cta_text'])) {
        $templateData['cta_text'] = 'View Properties';
    }
    
    // Create a copy with preview data
    $previewNewsletter = $newsletter->replicate();
    $previewNewsletter->template_data = $templateData;
    
    return $previewNewsletter;
    }

}
