<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketCategoryController extends Controller
{
    public function index()
    {
        $categories = TicketCategory::ordered()->paginate(15);
        return view('tickets.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('tickets.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $category = TicketCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.ticket-categories.index')
                        ->with('success', 'Ticket category created successfully.');
    }

    public function show(TicketCategory $ticketCategory)
    {
        $ticketCategory->load('ticketTypes');
        return view('tickets.categories.show', compact('ticketCategory'));
    }

    public function edit(TicketCategory $ticketCategory)
    {
        return view('tickets.categories.edit', compact('ticketCategory'));
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $ticketCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.ticket-categories.index')
                        ->with('success', 'Ticket category updated successfully.');
    }

    public function destroy(TicketCategory $ticketCategory)
    {
        if ($ticketCategory->ticketTypes()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete category that has associated ticket types.');
        }

        $ticketCategory->delete();
        
        return redirect()->route('admin.ticket-categories.index')
                        ->with('success', 'Ticket category deleted successfully.');
    }
}