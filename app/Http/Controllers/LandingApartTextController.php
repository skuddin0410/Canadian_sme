<?php

namespace App\Http\Controllers;

use App\Models\LandingApartText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingApartTextController extends Controller
{
    public function index()
    {
        $text = LandingApartText::first();
        return view('admin.home-page.apart.text', compact('text'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $text = LandingApartText::first();
        if (!$text) {
            $text = LandingApartText::create($request->only(['heading', 'sub_heading']));
        } else {
            $text->update($request->only(['heading', 'sub_heading']));
        }

        return redirect()->back()->withSuccess('Apart section text updated successfully!');
    }
}
