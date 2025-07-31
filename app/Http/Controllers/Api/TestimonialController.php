<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Drive;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index() {
        $testimonials = Testimonial::with(['photo'])
            ->where('status', 'success')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => $testimonials,
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'photo' => 'nullable|file|image|mimes:jpeg,jpg,png|max:2048',
            'rating' => 'required|numeric|integer|min:1|max:5',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $testimonial = new Testimonial();
        $testimonial->name = $request->name;
        $testimonial->rating = $request->rating;
        $testimonial->message = $request->message;
        $testimonial->save();

        if ($request->hasFile('photo')) {
            $path = 'testimonials';
            if (!Storage::disk('public')->exists($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            }

            $drive = Drive::where('table_id', $testimonial->id)
                ->where('table_type', $path)
                ->where('file_type', 'photo')
                ->first();
            if ($drive) {
                if (Storage::disk('public')->exists($drive->file_name)) {
                    Storage::disk('public')->delete($drive->file_name);
                }
                $drive->delete();
            }

            $file = $request->file('photo');
            $realPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $fileName = 'photo-' . date('Ymd-His') . '-' . abs(crc32(uniqid())) . '.' . $extension;
            $fullPath = $path . '/' . $fileName;
            if (Storage::disk('public')->exists($fullPath)) {
                Storage::disk('public')->delete($fullPath);
            }

            // $resize_width = 720;
            // $resize_height = 480;
            // $image = Image::make($realPath)
            //     ->resize($resize_width, $resize_height, function (Constraint $constraint) {
            //         $constraint->aspectRatio(); // auto height
            //         $constraint->upsize(); // prevent possible upsizing
            //     })
            //     ->encode($extension); // encode image format
            // Storage::disk('public')->put($fullPath, $image, 'public');

            // Use Laravel's `get` method to retrieve the file's contents
            Storage::disk('public')->put($fullPath, $file->get(), 'public');

            $drive = new Drive;
            $drive->table_id = $testimonial->id;
            $drive->table_type = $path;
            $drive->file_type = 'photo';
            $drive->file_name = $fileName;
            $drive->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => $testimonial,
        ]);
    }
}
