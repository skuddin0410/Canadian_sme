<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Drive;
// use AWS\CRT\Log;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    // public function home(Request $request)
    // {
    //     $user = auth()->user();

    //     // load relations like API
    //     $user->load(['photo', 'usercompany']);

    //     $availableTags = ['Speaker','Exhibitor','Sponsor','VIP','Organizer','Visitor'];

    //     // Build the same "view model" structure like your API response
    //     $profile = [
    //         'id' => $user->id,
    //         'comet_chat_id' => $user->cometchat_id,
    //         'name' => $user->full_name ?? '',
    //         'first_name' => $user->name ?? '',
    //         'last_name' => $user->lastname ?? '',
    //         'email' => $user->email ?? '',
    //         'phone' => $user->mobile ?? '',
    //         'imageUrl' => !empty($user->photo) ? $user->photo->mobile_path : asset('images/default.png'),

    //         'company_about_page' => config('app.url') . 'app/page/about',
    //         'company_location_page' => config('app.url') . 'app/page/location',
    //         'company_privacy_policy_page' => config('app.url') . 'app/page/privacy',
    //         'company_terms_of_service_page' => config('app.url') . 'app/page/terms',

    //         'designation' => $user->designation,
    //         'bio' => $user->bio,
    //         'tag' => !empty($user->tags) ? explode(',', $user->tags) : [],
    //         'my_qr_code' => !empty($user->qr_code) ? asset($user->qr_code) : null,

    //         'company_name' => !empty($user->usercompany) ? $user->usercompany->name : $user->company,
    //         'company_email' => !empty($user->usercompany) ? $user->usercompany->email : $user->email,
    //         'company_phone' => !empty($user->usercompany) ? $user->usercompany->phone : $user->mobile,
    //         'company_website' => !empty($user->usercompany) ? $user->usercompany->website : $user->website_url,

    //         // If you already have groups($user) helper you can use it here too
    //         // 'roles' => groups($user),

    //         'is_speaker_id' => (int) ($user->access_speaker_ids ?? 0),
    //         'is_exhibitor_id' => (int) ($user->access_exhibitor_ids ?? 0),
    //         'is_sponsor_id' => (int) ($user->access_sponsor_ids ?? 0),
    //     ];

    //     return view('user.home', compact('profile', 'user', 'availableTags'));
    // }

    
    
    public function edit()
    {
        $user = auth()->user();

        // $availableTags = ['Speaker','Exhibitor','Sponsor','VIP','Organizer','Visitor'];

        $availableTags = Category::whereIn('type', ['tags', 'connections'])->pluck('name')
            ->filter()
            ->flatMap(function ($tagString) {
                if (is_array($tagString)) {
                    return $tagString;
                }

                // try decode JSON first
                $jsonDecoded = json_decode($tagString, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonDecoded)) {
                    return $jsonDecoded;
                }

                // fallback: split by comma
                return explode(',', $tagString);
            })
            ->map(fn($tag) => trim($tag)) // clean spaces
            ->filter() // remove empty after trim
            ->unique()
            ->values();

        $exhibitorId = $user->access_exhibitor_ids;

        if (is_string($exhibitorId) && str_contains($exhibitorId, ',')) {
            $exhibitorId = trim(explode(',', $exhibitorId)[0]);
        }

        // 3) Fetch company (same as API)
        $exhibitor = null;
        $companyData = null;

        if (!empty($exhibitorId)) {
            $exhibitor = Company::with(['contentIconFile', 'quickLinkIconFile', 'Docs'])
                ->find($exhibitorId);

            if ($exhibitor) {
                $companyData = [
                    'id'       => $exhibitor->id,
                    'name'     => $exhibitor->name ?? '',
                    'word_no'  => $exhibitor->booth ?? '',
                    'avatar'   => $exhibitor->contentIconFile?->mobile_path ?? asset('images/default.png'),
                    'banner'   => $exhibitor->quickLinkIconFile?->file_path ?? asset('images/eventify-banner.jpg'),
                    'location' => $exhibitor->location ?? ($exhibitor->booth ?? ''),
                    'email'    => $exhibitor->email ?? '',
                    'phone'    => $exhibitor->phone ?? '',
                    'website'  => $exhibitor->website ?? '',
                    'social_links' => [
                        ['name' => 'linkedin',  'url' => $exhibitor->linkedin  ?? ''],
                        ['name' => 'facebook',  'url' => $exhibitor->facebook  ?? ''],
                        ['name' => 'instagram', 'url' => $exhibitor->instagram ?? ''],
                        ['name' => 'twitter',   'url' => $exhibitor->twitter   ?? ''],
                    ],
                    'bio' => $exhibitor->description ?? '',
                    'uploaded_files' => $exhibitor->Docs->map(fn($doc) => [
                        'fileID' => $doc->id,
                        'name'   => $doc->file_name,
                        'url'    => $doc->file_path,
                    ])->values()->toArray(),
                ];
            }
        }

        // 4) Convert social links array into map for your blade inputs (linkedin, facebook..)
        $companySocialMap = [];
        if (!empty($companyData['social_links'])) {
            foreach ($companyData['social_links'] as $s) {
                $companySocialMap[$s['name']] = $s['url'] ?? '';
            }
        }

        // 5) Existing files for blade
        $companyFiles = $companyData['uploaded_files'] ?? [];

        // ✅ Pass $companyData / $exhibitor for company card
        return view('user.home', compact(
            'user',
            'availableTags',
            'companyData',
            'companySocialMap',
            'companyFiles'
        ));
    }


    public function updateProfile(Request $request)
    {
        // dd($request->all());

        $user = auth()->user();

        $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:100'],
            'last_name'  => ['required', 'string', 'min:1', 'max:100'],
            'designation' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:200'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ]);

        $user->name = $request->first_name;
        $user->lastname  = $request->last_name;
        $user->designation = $request->designation;
        // $user->company_name = $request->company_name;
        // $user->company_website = $request->company_website;
        $user->email = $request->email;
        $user->mobile = $request->phone;
        $user->bio = $request->bio;

        // Store tags as comma string (change to JSON if you want)
        $user->tags = !empty($request->tags) ? implode(',', $request->tags) : null;

        $user->save();

        return redirect()->route('user.home')->with('success', 'Profile updated successfully!');
    }


    // public function updateCompanyDetails(Request $request)
    // {
    //     dd($request->all());

    //     $user = Auth::user();

    //     $request->validate([
    //         'exhibitor_name' => ['required', 'string', 'min:2', 'max:200'],
    //         'booth_no' => ['required', 'string', 'min:1', 'max:50'],
    //         'company_emailid' => ['required', 'email', 'max:255'],
    //         'company_phone' => ['required', 'string', 'min:10', 'max:20'],
    //         'company_address' => ['required', 'string', 'max:500'],
    //         'company_website2' => ['nullable', 'url', 'max:255'],
    //         'company_about' => ['required', 'string', 'min:10', 'max:3000'],

    //         'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'], // 10MB
    //         'company_files.*' => ['nullable', 'file', 'max:10240'], // 10MB each
    //     ]);

    //     // Save text fields
    //     $user->exhibitor_name = $request->exhibitor_name;
    //     $user->booth_no = $request->booth_no;

    //     $user->company_emailid = $request->company_emailid;
    //     $user->company_phone = $request->company_phone;
    //     $user->company_address = $request->company_address;
    //     $user->company_website2 = $request->company_website2;

    //     $user->company_about = $request->company_about;

    //     // Social links JSON (store as array)
    //     $social = [
    //         ['name' => 'linkedin',  'url' => $request->input('linkedin')],
    //         ['name' => 'facebook',  'url' => $request->input('facebook')],
    //         ['name' => 'instagram', 'url' => $request->input('instagram')],
    //         ['name' => 'twitter',   'url' => $request->input('twitter')],
    //     ];
    //     $social = array_values(array_filter($social, fn($x) => !empty($x['url'])));
    //     $user->company_social_links = $social; // if column is json or text, both ok

    //     // Banner upload
    //     if ($request->hasFile('banner')) {
    //         if (!empty($user->company_banner_path)) {
    //             Storage::disk('public')->delete($user->company_banner_path);
    //         }
    //         $path = $request->file('banner')->store('company/banner', 'public');
    //         $user->company_banner_path = $path;
    //     }

    //     // Multiple files upload
    //     $existingFiles = $user->company_uploaded_files ?? [];
    //     if (is_string($existingFiles)) {
    //         $existingFiles = json_decode($existingFiles, true) ?: [];
    //     }

    //     if ($request->hasFile('company_files')) {
    //         foreach ($request->file('company_files') as $file) {
    //             if (!$file) continue;

    //             $path = $file->store('company/files', 'public');
    //             $existingFiles[] = [
    //                 'fileID' => (string) uniqid(),
    //                 'name' => $file->getClientOriginalName(),
    //                 'url'  => Storage::url($path),
    //                 'path' => $path,
    //             ];
    //         }
    //     }

    //     $user->company_uploaded_files = $existingFiles;
    //     $user->save();

    //     return redirect()->route('user.home')->with('success', 'Company details updated successfully!');
    // }



    public function updateCompanyDetails(Request $request)
    {
        // try {
            // dd($request->all());
        
        $user = auth()->user();

        // if (!$user->can('Edit Company')) {
        //     return back()->withErrors(['permission' => 'You are not allowed to edit company.'])->withInput();
        // }

        $exhibitorId = $request->input('company_id') ?? $user->access_exhibitor_ids;

        if (is_string($exhibitorId) && str_contains($exhibitorId, ',')) {
            $exhibitorId = trim(explode(',', $exhibitorId)[0]);
        }

        // NOTE: your current strict check breaks when access_exhibitor_ids has comma list.
        // So check containment instead:
        $allowedIds = is_string($user->access_exhibitor_ids)
            ? array_map('trim', explode(',', $user->access_exhibitor_ids))
            : [(string)$user->access_exhibitor_ids];

        if (empty($exhibitorId) || !in_array((string)$exhibitorId, $allowedIds, true)) {
            return back()->withErrors(['company' => 'Invalid company access.'])->withInput();
        }

        // ✅ validate using your form input names
        $request->validate([
            'exhibitor_name'  => ['required', 'string', 'min:2', 'max:200'],
            'booth_no'        => ['required', 'string', 'min:1', 'max:50'],

            'company_emailid' => ['required', 'email', 'max:255'],
            'company_phone'   => ['required', 'string', 'min:10', 'max:25'],
            'company_address' => ['required', 'string', 'max:500'],
            'company_website2' => ['nullable', 'max:255'],

            'linkedin'        => ['nullable', 'max:255'],
            'facebook'        => ['nullable', 'max:255'],
            'instagram'       => ['nullable', 'max:255'],
            'twitter'         => ['nullable', 'max:255'],

            'company_about'   => ['required', 'string', 'min:10', 'max:5000'],

            'banner'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10240'],
            'company_files.*' => ['nullable', 'file', 'max:10240'],
        ]);
        

        $company = \App\Models\Company::with(['contentIconFile', 'quickLinkIconFile', 'Docs'])
            ->findOrFail($exhibitorId);

        // ✅ map correct request keys to company fields
        $company->name        = $request->exhibitor_name;
        $company->booth       = $request->booth_no;

        $company->email       = $request->company_emailid;
        $company->phone       = $request->company_phone;
        $company->location    = $request->company_address;   // if column exists
        $company->website     = $request->company_website2;

        $company->linkedin    = $request->linkedin;
        $company->facebook    = $request->facebook;
        $company->instagram   = $request->instagram;
        $company->twitter     = $request->twitter;

        $company->description = $request->company_about;

        $company->save();

        // if ($request->hasFile('banner')) {
        //     $this->imageUpload(
        //         $request->file('banner'),
        //         'companies',
        //         $company->id,
        //         'companies',
        //         'private_docs'
        //     );

        //     // Optional: if you want banner to specifically replace quickLinkIconFile instead of Docs/Drive,
        //     // keep your previous banner logic. Otherwise this matches your API's Drive upload flow.
        // }


        // if ($request->hasFile('company_files')) {
        //     foreach ($request->file('company_files') as $file) {
        //         if (!$file) continue;

        //         $this->imageUpload(
        //             $file,
        //             'companies',
        //             $company->id,
        //             'companies',
        //             'private_docs'
        //         );
        //     }
        // }


        if (!empty($request->company_files)) {

            foreach ($request->company_files as $val) {

                $this->imageUpload(
                    $val,
                    'companies',
                    $company->id,
                    'companies',
                    'private_docs'
                );
            }
        }

        // if ($request->avatar) {

        //     $this->imageUpload(
        //         $request->avatar,
        //         'content_icon',
        //         $company->id,
        //         'companies',
        //         'content_icon',
        //         $company->id,
        //     );
        // }


        if ($request->banner) {

            $this->imageUpload(
                $request->banner,
                'quick_link_icon',
                $company->id,
                'companies',
                'quick_link_icon',
                $company->id,
            );
        }

        return redirect()->back()->with('success', 'Company details updated successfully.');

        // } 
        // catch (\Exception $e) {
        //     \Log::error($e);

        //     return redirect()->back()
        //         ->withInput()
        //         ->withErrors([
        //             'general' => 'Something went wrong while updating company details. Please try again.'
        //             // or: $e->getMessage() (only in dev)
        //         ]);
        // }
    }


    // public function deleteCompanyFile(Request $request)
    // {
    //     $user = Auth::user();

    //     $request->validate([
    //         'fileId' => ['required', 'string'],
    //     ]);

    //     $files = $user->company_uploaded_files ?? [];
    //     if (is_string($files)) {
    //         $files = json_decode($files, true) ?: [];
    //     }

    //     $newFiles = [];
    //     $deleted = false;

    //     foreach ($files as $f) {
    //         if (($f['fileID'] ?? '') === $request->fileId) {
    //             $deleted = true;
    //             if (!empty($f['path'])) {
    //                 Storage::disk('public')->delete($f['path']);
    //             }
    //             continue;
    //         }
    //         $newFiles[] = $f;
    //     }

    //     $user->company_uploaded_files = $newFiles;
    //     $user->save();

    //     return response()->json([
    //         'success' => $deleted,
    //         'message' => $deleted ? 'File deleted' : 'File not found',
    //     ]);
    // }

    

    public function deleteCompanyFile(Request $request)
    {
        // dd(1);
        // dd($request->all());

        $user = auth()->user();

        // if (!$user->can('Edit Company')) {
        //     return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        // }

        $request->validate([
            'company_id' => ['required'],
            'fileId' => ['required'],
            'type' => ['required'], // expect "exhibitor"
        ]);

        $companyId = $request->company_id;

        // ensure user can delete only their company file
        $allowedIds = is_string($user->access_exhibitor_ids)
            ? array_map('trim', explode(',', $user->access_exhibitor_ids))
            : [(string)$user->access_exhibitor_ids];

        if (!in_array((string)$companyId, $allowedIds, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid company access'], 403);
        }

        if ($request->type !== 'exhibitor' && $request->type !== 'sponsor') {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 422);
        }

        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Exhibitor not found!'], 404);
        }

        $file = Drive::where('id', $request->fileId)
            ->where('table_id', $company->id)
            ->where('table_type', 'companies')
            ->where('file_type', 'private_docs')
            ->first();

        if (!$file) {
            return response()->json(['success' => false, 'message' => 'File not found!'], 404);
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
            'file_id' => $request->fileId,
        ]);
    }
}
