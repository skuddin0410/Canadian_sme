<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use Illuminate\Pagination\LengthAwarePaginator;


class NewsletterSubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        //  $subscribers = NewsletterSubscriber::latest()->paginate(10);
        // return view('users.newsletters.subscriber.index', compact('subscribers'));
    $perPage = (int) $request->input('perPage', 10);
    $pageNo = (int) $request->input('page', 1);
    $offset = $perPage * ($pageNo - 1);

    $subscribersQuery = NewsletterSubscriber::query()->latest();

    // Search by email or name
    if ($request->search) {
        $subscribersQuery->where(function($query) use($request) {
            $query->where('email', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('name', 'LIKE', '%' . $request->search . '%');
        });
    }

    $totalRecords = (clone $subscribersQuery)->count();

    $subscribers = $subscribersQuery->offset($offset)
                        ->limit($perPage)
                        ->get();

    $subscribersPaginator = new LengthAwarePaginator(
        $subscribers,
        $totalRecords,
        $perPage,
        $pageNo,
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    if ($request->ajax() && $request->ajax_request == true) {
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $data['html'] = view('users.newsletters.subscriber.table', compact('subscribersPaginator', 'perPage'))
                          ->with('i', $offset)
                          ->render();
        return response()->json($data);
    }

    return view('users.newsletters.subscriber.index', [
        'subscribersPaginator' => $subscribersPaginator
    ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         return view('users.newsletters.subscriber.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'required|string|max:255',
            'preferences' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'required|in:subscribed,unsubscribed',
            'subscription_source' => 'nullable|string|max:255'
        ]);

        if ($data['status'] === 'subscribed') {
            $data['subscribed_at'] = now();
        }

        NewsletterSubscriber::create($data);

        return redirect()->route('newsletter-subscribers.index')
            ->with('success', 'Subscriber added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsletterSubscriber $newsletterSubscriber)
    {
        //
        return view('users.newsletters.subscriber.show', compact('newsletterSubscriber'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewsletterSubscriber $newsletterSubscriber)
    {
        //
         return view('users.newsletters.subscriber.edit', compact('newsletterSubscriber'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsletterSubscriber $newsletterSubscriber)
    {
        //
        $data = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email,' . $newsletterSubscriber->id,
            'name' => 'required|string|max:255',
            'preferences' => 'nullable|string',
            'tags' => 'nullable|string',
            'status' => 'required|in:subscribed,unsubscribed',
            'subscription_source' => 'nullable|string|max:255'
        ]);
        // Convert comma-separated string to array
if(!empty($data['tags'])){
    $data['tags'] = array_map('trim', explode(',', $data['tags']));
} else {
    $data['tags'] = [];
}
if(!empty($data['preferences'])){
    $data['preferences'] = array_map('trim', explode(',', $data['preferences']));
} else {
    $data['preferences'] = [];
}

        if ($data['status'] === 'subscribed') {
            $data['subscribed_at'] = now();
            $data['unsubscribed_at'] = null;
        } elseif ($data['status'] === 'unsubscribed') {
            $data['unsubscribed_at'] = now();
        }

        $newsletterSubscriber->update($data);

        return redirect()->route('newsletter-subscribers.index')
            ->with('success', 'Subscriber updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        //
        $newsletterSubscriber->delete();

        return redirect()->route('newsletter-subscribers.index')
            ->with('success', 'Subscriber deleted successfully.');
    }
}
