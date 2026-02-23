<?php

namespace App\Http\Controllers;

use App\Models\LandingApartCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingApartCardController extends Controller
{
    public function index()
    {
        $cards = LandingApartCard::orderBy('order_by', 'asc')->get();
        return view('admin.home-page.apart.cards.index', compact('cards'));
    }

    public function create()
    {
        return view('admin.home-page.apart.cards.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'description' => 'nullable|string',
            'text' => 'nullable|string',
            'status' => 'required|boolean',
            'order_by' => 'required|integer',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $card = LandingApartCard::create($request->only(['heading', 'description', 'text', 'status', 'order_by']));

        if ($request->hasFile('icon')) {
            $this->imageUpload($request->file("icon"), "landing_apart_cards", $card->id, 'landing_apart_cards', 'icon', $card->id);
        }

        return redirect(route('admin.home-page.apart.cards.index'))->withSuccess('Apart card created successfully!');
    }

    public function edit($id)
    {
        $card = LandingApartCard::findOrFail($id);
        return view('admin.home-page.apart.cards.edit', compact('card'));
    }

    public function update(Request $request, $id)
    {
        $card = LandingApartCard::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'description' => 'nullable|string',
            'text' => 'nullable|string',
            'status' => 'required|boolean',
            'order_by' => 'required|integer',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $card->update($request->only(['heading', 'description', 'text', 'status', 'order_by']));

        if ($request->hasFile('icon')) {
            $this->imageUpload($request->file("icon"), "landing_apart_cards", $card->id, 'landing_apart_cards', 'icon', $card->id);
        }

        return redirect(route('admin.home-page.apart.cards.index'))->withSuccess('Apart card updated successfully!');
    }

    public function destroy($id)
    {
        $card = LandingApartCard::findOrFail($id);
        static::deleteFile($card->id, 'landing_apart_cards', 'icon');
        $card->delete();

        return redirect(route('admin.home-page.apart.cards.index'))->withSuccess('Apart card deleted successfully!');
    }
}
