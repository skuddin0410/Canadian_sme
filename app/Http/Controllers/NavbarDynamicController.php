<?php

namespace App\Http\Controllers;

use App\Models\NavbarDynamic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NavbarDynamicController extends Controller
{
    public function index()
    {
        $navbars = NavbarDynamic::orderBy('order_by', 'asc')->get();
        return view('admin.navbar-dynamic.index', compact('navbars'));
    }

    public function create()
    {
        $categories = NavbarDynamic::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.navbar-dynamic.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'order_by' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        NavbarDynamic::create($request->only(['title', 'status', 'order_by', 'category', 'content']));

        return redirect(route('admin.navbar-dynamic.index'))->withSuccess('Navbar item created successfully!');
    }

    public function edit($id)
    {
        $navbar = NavbarDynamic::findOrFail($id);
        $categories = NavbarDynamic::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.navbar-dynamic.edit', compact('navbar', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $navbar = NavbarDynamic::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'order_by' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $navbar->update($request->only(['title', 'status', 'order_by', 'category', 'content']));

        return redirect(route('admin.navbar-dynamic.index'))->withSuccess('Navbar item updated successfully!');
    }

    public function destroy($id)
    {
        $navbar = NavbarDynamic::findOrFail($id);
        $navbar->delete();

        return redirect(route('admin.navbar-dynamic.index'))->withSuccess('Navbar item deleted successfully!');
    }
}
