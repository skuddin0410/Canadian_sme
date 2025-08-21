<?php

use Illuminate\Http\Request;

use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NewsletterSubscriberController;

// Admin routes for newsletter management
Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {
    Route::resource('newsletters', NewsletterController::class);
    Route::post('newsletters/{newsletter}/send', [NewsletterController::class, 'send'])->name('newsletters.send');
    Route::post('newsletters/{newsletter}/schedule', [NewsletterController::class, 'schedule'])->name('newsletters.schedule');
    Route::get('newsletters/{newsletter}/preview', [NewsletterController::class, 'preview'])->name('newsletters.preview');
    Route::post('newsletters/{newsletter}/test-send', [NewsletterController::class, 'testSend'])->name('newsletters.test-send');
Route::resource('newsletter-subscribers', NewsletterSubscriberController::class);

    // Route::get('newsletter-subscribers', [NewsletterController::class, 'subscribers'])->name('newsletters.subscribers');
    Route::get('newsletter-subscribers/export', [NewsletterController::class, 'exportSubscribers'])->name('newsletters.subscribers.export');
    Route::get('/mail-test', function() {
    try {
        \Mail::raw('Test email body', function($message) {
            $message->to('you@example.com')->subject('Test Mail');
        });
        return 'Mail sent';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
    // Route::resource('newsletter-subscribers', NewsletterSubscriberController::class);


 

});

});
Route::get('/email/track', function(Request $request) {
    ActivityTrackingService::trackEmailOpen($request->email, $request->campaign);
    
    // Return a 1x1 transparent pixel
    return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
        ->header('Content-Type', 'image/gif')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
})->name('email.track');


Route::group(['middleware' => ['webauth', 'role:Admin|Admin']], function () {
    Route::get('/newsletters/{newsletter}/preview-popup', function($newsletter) {
        $newsletter = \App\Models\Newsletter::findOrFail($newsletter);
        return view('users.newsletters.preview-popup', compact('newsletter'));
    })->name('newsletters.preview-popup');
});

// Public routes for subscription and tracking
Route::get('/subscribes/to-our-newsletter', function () {
            return view('newsletters.subscribe');
})->name('subscribe-form');
// Route::get('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribers');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/{newsletter}/track-open', [NewsletterController::class, 'trackOpen'])->name('newsletter.track-open');
Route::get('/newsletter/{newsletter}/track-click', [NewsletterController::class, 'trackClick'])->name('newsletter.track-click');
