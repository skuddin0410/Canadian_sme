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
        $query = TicketCategory::with('creator')->ordered();
        if (!isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }
        $categories = $query->paginate(15);
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

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        while (TicketCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $category = TicketCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id()
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
        if (!isSuperAdmin() && $ticketCategory->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('tickets.categories.edit', compact('ticketCategory'));
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        if (!isSuperAdmin() && $ticketCategory->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        while (TicketCategory::where('slug', $slug)->where('id', '!=', $ticketCategory->id)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $ticketCategory->update([
            'name' => $request->name,
            'slug' => $slug,
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
        if (!isSuperAdmin() && $ticketCategory->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($ticketCategory->ticketTypes()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete category that has associated ticket types.');
        }

        $ticketCategory->delete();
        
        return redirect()->route('admin.ticket-categories.index')
                        ->with('success', 'Ticket category deleted successfully.');
    }
}