<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;
use App\Models\Order;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');

        if ($request->ajax() && $request->ajax_request == true) {
            $roles = Role::orderBy('created_at', 'DESC');

            $rolesCount = clone $roles;
            $totalRecords = $rolesCount->count(DB::raw('DISTINCT(roles.id)'));

            $roles = $roles->offset($offset)->limit($perPage)->get();

            $roles = new LengthAwarePaginator($roles, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);

            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $roles->setPath(route('usergroup.index'));

            $data['html'] = view('users.usergroup.table', compact('roles', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.usergroup.index');
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.usergroup.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
        'name' => 'required|unique:roles,name|max:255',
      ]);

    Role::create([
        'name' => $request->name,
        'guard_name' => 'web' // Always web
    ]);

    return redirect()->route('usergroup.index')->with('success', 'Role created successfully.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('users')->findOrFail($id);

       
        return view('users.usergroup.view', compact('role'));
    
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('users.usergroup.edit', compact('role'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        return redirect()->route('usergroup.index')->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        $role->users()->detach();

        $role->delete();

       

        return redirect()->route('usergroup.index')->with('success', 'Role deleted successfully.');
    
    }
}
