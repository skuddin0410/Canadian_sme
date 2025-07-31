<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Drive;
use App\Models\Bank;

class KycController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|image|mimes:jpeg,jpg,png|max:2048',
            'background' => 'sometimes|file|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        if ($request->hasFile('photo')) {
            $path = 'users';
            if (!Storage::disk('public')->exists($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            }

            $drive = Drive::where('table_id', auth()->guard('api')->id())
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
            $drive->table_id = auth()->guard('api')->id();
            $drive->table_type = $path;
            $drive->file_type = 'photo';
            $drive->file_name = $fileName;
            $drive->save();
        }

        if ($request->hasFile('background')) {
            $path = 'users';
            if (!Storage::disk('public')->exists($path)) {
                Storage::makeDirectory($path, 0777, true, true);
            }

            $drive2 = Drive::where('table_id', auth()->guard('api')->id())
                ->where('table_type', $path)
                ->where('file_type', 'background')
                ->first();
            if ($drive2) {
                if (Storage::disk('public')->exists($drive2->file_name)) {
                    Storage::disk('public')->delete($drive2->file_name);
                }
                $drive2->delete();
            }

            $file = $request->file('background');
            $realPath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            $fileName = 'background-' . date('Ymd-His') . '-' . abs(crc32(uniqid())) . '.' . $extension;
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

            $drive2 = new Drive;
            $drive2->table_id = auth()->guard('api')->id();
            $drive2->table_type = $path;
            $drive2->file_type = 'background';
            $drive2->file_name = $fileName;
            $drive2->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => [
                'photo' => $drive,
                'background' => $drive2
            ]
        ]);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'upi' => 'required_without:account|nullable|string|max:255',
            'account' => 'required_without:upi|nullable|numeric|digits_between:9,18',
            'ifsc' => 'required_with:account|nullable|string|size:11',
            'holder' => 'required_with:account|nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $bank = Bank::firstOrNew([
            'user_id' => auth()->guard('api')->id()
        ]);
        $bank->upi = $request->upi;
        $bank->account = $request->account;
        $bank->ifsc = $request->ifsc;
        $bank->holder = $request->holder;
        $bank->is_default = 1;
        $bank->save();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => $bank,
        ]);
    }
}
