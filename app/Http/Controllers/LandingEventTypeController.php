<?php

namespace App\Http\Controllers;

use App\Models\LandingEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingEventTypeController extends Controller
{
    public function index()
    {
        $types = LandingEventType::orderBy('order', 'asc')->get();
        return view('admin.home-page.events.types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.home-page.events.types.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'text' => 'required|string',
            'status' => 'required|boolean',
            'order' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $type = LandingEventType::create($request->only(['heading', 'text', 'status', 'order']));

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_event_types", $type->id, 'landing_event_types', 'type_image', $type->id);
        }

        return redirect(route('admin.home-page.events.types.index'))->withSuccess('Event type created successfully!');
    }

    public function edit($id)
    {
        $type = LandingEventType::findOrFail($id);
        return view('admin.home-page.events.types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = LandingEventType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'text' => 'required|string',
            'status' => 'required|boolean',
            'order' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $type->update($request->only(['heading', 'text', 'status', 'order']));

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_event_types", $type->id, 'landing_event_types', 'type_image', $type->id);
        }

        return redirect(route('admin.home-page.events.types.index'))->withSuccess('Event type updated successfully!');
    }

    public function destroy($id)
    {
        $type = LandingEventType::findOrFail($id);
        static::deleteFile($type->id, 'landing_event_types', 'type_image');
        $type->delete();

        return redirect(route('admin.home-page.events.types.index'))->withSuccess('Event type deleted successfully!');
    }
}
