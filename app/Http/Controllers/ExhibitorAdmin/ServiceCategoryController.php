<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Drive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;



class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

        $query = ServiceCategory::orderBy('created_at', 'DESC');

        // Filter by search (name, description)
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // Filter by is_active
        if ($request->has('is_active') && in_array($request->is_active, ['0', '1'])) {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords = $query->count();

        $categories = $query->offset($offset)->limit($perPage)->get();

        $paginated = new LengthAwarePaginator(
            $categories,
            $totalRecords,
            $perPage,
            $pageNo,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax() && $request->ajax_request == true) {
            $paginated->setPath(route('service-categories.index'));

            $data['html'] = view('company.service-categories.table', [
                'categories' => $paginated,
            ])->render();

            return response()->json($data);
        }

        return view('company.service-categories.index', compact('paginated'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         
        return view('company.service-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('service_categories', 'public');
            $validated['image_url'] = $path;
        } else {
            $validated['image_url'] = null;
        }

        $category = ServiceCategory::create($validated);

        return redirect()->route('service-categories.index')
                        ->with('success', 'Service category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCategory $serviceCategory)
    {
        
        return view('company.service-categories.show', ['category' => $serviceCategory]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        //
      

        return view('company.service-categories.edit', compact('serviceCategory'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validated['name'] !== $serviceCategory->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('service_categories', 'public');
            $validated['image_url'] =  $path;
        }

        $serviceCategory->update($validated);

        return redirect()->route('service-categories.index')
            ->with('success', 'Service category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        //
           if ($serviceCategory->services()->exists()) {
            return back()->with('error', 'Cannot delete category with existing services.');
        }

        $serviceCategory->delete();
        
        return redirect()->route('service-categories.index')
                        ->with('success', 'Service category deleted successfully.');
    }
}
