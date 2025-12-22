<?php

namespace App\Http\Controllers;

use App\Models\NewBadge;
use Illuminate\Http\Request;

class NewBadgeController extends Controller
{
    public function index()
    {
        $badges = NewBadge::all();
        return view('DragAndDropBadge.index', compact('badges'));
    }

    public function store(Request $request)
    {   
        $validated = $request->validate([
            'badge_name' => 'required|string|max:255',
            'target'     => 'required|string|max:255',
            'printer'    => 'required|string|max:255',
            'width'      => 'required|numeric',
            'height'     => 'required|numeric',
        ]);
       
         NewBadge::create($validated);
         return redirect()->route('newbadges.index');
    }

    public function show(NewBadge $newbadge)
    {   
        return view('DragAndDropBadge.show', compact('newbadge'));
        //return $newbadge;
    }

    public function update(Request $request, NewBadge $newbadge)
    {
        $validated = $request->validate([
            'badge_name' => 'required|string|max:255',
            'target'     => 'required|string|max:255',
            'printer'    => 'required|string|max:255',
            'width'      => 'required|numeric',
            'height'     => 'required|numeric',
        ]);

        $newbadge->update($validated);

        return $newbadge;
    }

    public function destroy(NewBadge $newbadge)
    {
        $newbadge->delete();

        return response()->json([
            'message' => 'Badge deleted successfully'
        ]);
    }


    public function saveLayout(Request $request, $id){
       $badge = NewBadge::where('id',$id)->first();
       $badge->layout = json_encode($request->elements);
       $badge->save();

    }
}
