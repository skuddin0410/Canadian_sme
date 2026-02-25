<?php

namespace App\Http\Controllers;

use App\Models\LandingDemoText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingDemoTextController extends Controller
{
    public function index()
    {
        $demoText = LandingDemoText::first();
        return view('admin.home-page.demo-text', compact('demoText'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'subtitle1' => 'nullable|string|max:255',
            'subtitle2' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $demoText = LandingDemoText::first();
        if (!$demoText) {
            $demoText = LandingDemoText::create($request->only(['heading', 'subtitle1', 'subtitle2']));
        } else {
            $demoText->update($request->only(['heading', 'subtitle1', 'subtitle2']));
        }

        return redirect()->back()->withSuccess('Demo text updated successfully!');
    }
}
