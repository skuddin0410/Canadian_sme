<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Drive;
use Illuminate\Support\Facades\Storage;


class CompanyController extends Controller
{


    public function details()
    {
        $company = Company::with('certificationFile', 'mediaGallery', 'logoFile', 'videos')
            ->where('user_id', Auth::id())
            ->first();
        return view('company.details', compact('company'));
    }


    public function index(Request $request)
    {
        if ($request->ajax() && $request->ajax_request == true) {
            $companies = Company::where('user_id', Auth::id())->orderBy('id', 'DESC');

            if ($request->search) {
                $companies = $companies->where('name', 'like', '%' . $request->search . '%');
            }

            $companies = $companies->paginate($request->get('perPage', 10));

            $data['html'] = view('company.table', compact('companies'))->render();
            return response($data);
        }

        return view('company.index');
    }

    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'industry'      => 'required|string|max:255',
            'size'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'description'   => 'required|string',
            'website'       => 'required|url',
            'linkedin'      => 'required|url',
            'twitter'       => 'required|url',
            'facebook'      => 'required|url',
            'certifications' => 'required|string',
            'certification_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company = new Company();
        $company->user_id = Auth::id();
        $company->fill($request->only(['name', 'industry', 'size', 'location', 'email', 'phone', 'description', 'website',  'linkedin', 'twitter', 'facebook', 'certifications']));

        if ($request->file("certification_image")) {
            $uploadPath = 'certifications';
            $this->imageUpload($request->file("certification_image"), $uploadPath, $company->id, 'companies', 'certifications');
        }

        if ($request->file("logo")) {
            $this->imageUpload($request->file("logo"), 'logo', $company->id, 'companies', 'logo');
        }

        $company->save();

        return redirect()->back()->with('success', 'Company created.');
    }


    public function edit(Company $company)
    {
        //
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }
        return view('company.edit', compact('company'));
    }


    public function destroy(Company $company)
    {
        //
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }

        $company->delete();

        return redirect()->route('company.index')->with('success', 'Company deleted successfully.');
    }
    public function editDescription()
    {
        $company = Company::where('user_id', auth()->id())->firstOrFail();
        return view('company.description', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'industry'      => 'required|string|max:255',
            'size'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'description'   => 'required|string',
            'website'       => 'required|url',
            'linkedin'      => 'required|url',
            'twitter'       => 'nullable|url',
            'facebook'      => 'nullable|url',
            'certifications' => 'required|string',
            'certification_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company->update($request->only([
            'name',
            'industry',
            'size',
            'location',
            'email',
            'phone',
            'description',
            'website',
            'linkedin',
            'twitter',
            'facebook',
            'certifications'
        ]));

        // Handle image upload
        if ($request->file("certification_image")) {
            $uploadPath = 'certifications';
            $this->imageUpload($request->file("certification_image"), $uploadPath, $company->id, 'companies', 'certifications', $idForUpdate = $company->id);
        }

        if ($request->file("logo")) {
            $this->imageUpload($request->file("logo"), 'logo', $company->id, 'companies', 'logo', $idForUpdate = $company->id);
        }


        return redirect()->back()->with('success', 'Company details has been updated successfully.');
    }



    public function mediaGallery()
    {
        $company = Company::with('mediaGallery')
            ->where('user_id', auth()->id())
            ->first();
        if (empty($company)) {
            return redirect()->route('company.details')->with('success', 'Update Company details first.');
        }

        return view('company.branding.media-gallery', compact('company'));
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'media_files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $company = Company::where('user_id', auth()->id())->first();
        if (empty($company)) {
            return redirect()->route('company.details')->with('success', 'Update Company details first.');
        }

        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('companies/media', $filename, 'public');

                Drive::create([
                    'table_type' => 'companies',
                    'table_id' => $company->id,
                    'file_type' => 'media_gallery',
                    'file_name' => $filePath,
                ]);
            }
        }

        return redirect()->route('company.media.gallery')->with('success', 'Media uploaded successfully.');
    }

    public function deleteMedia($id)
    {
        $media = Drive::where('id', $id)
            ->where('table_type', 'companies')
            ->where('file_type', 'media_gallery')
            ->first();

        if (!$media) {
            return redirect()->back()->with('error', 'Image not found or invalid.');
        }

        if ($media->file_name && Storage::disk('public')->exists($media->file_name)) {
            Storage::disk('public')->delete($media->file_name);
        }

        $media->delete();

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }

    public function videoGallery()
    {
        $company = Company::with('videos')->where('user_id', auth()->id())->first();
        if (empty($company)) {
            return redirect()->route('company.details')->with('success', 'Update Company details first.');
        }

        return view('company.branding.video-gallery', compact('company'));
    }
    public function uploadVideo(Request $request)
    {
        $request->validate([
            'videos'   => 'required',
            'videos.*' => 'mimes:mp4,webm,ogg|max:51200', // 50 MB
        ]);

        $company = Company::where('user_id', auth()->id())->first();

        if (empty($company)) {
            return redirect()->route('company.details')->with('success', 'Update Company details first.');
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $filename = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
                $path = $video->storeAs('companies/videos', $filename, 'public');

                Drive::create([
                    'table_id'   => $company->id,
                    'table_type' => 'companies',
                    'file_type'  => 'company_video',
                    'file_name'  => $path,
                ]);
            }
        }

        return back()->with('success', 'Videos uploaded successfully.');
    }
    public function deleteVideo($id)
    {
        $video = Drive::where('id', $id)
            ->where('table_type', 'companies')
            ->where('file_type', 'company_video')
            ->first();

        if (!$video) {
            return back()->with('error', 'Video not found or already deleted.');
        }

        // Delete file from storage
        if ($video->file_name && \Storage::disk('public')->exists($video->file_name)) {
            \Storage::disk('public')->delete($video->file_name);
        }

        $video->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
