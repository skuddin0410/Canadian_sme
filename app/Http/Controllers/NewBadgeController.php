<?php

namespace App\Http\Controllers;

use App\Models\NewBadge;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

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

         return redirect()->route('newbadges.index');
    }


    public function saveLayout(Request $request, $id){
       $badge = NewBadge::where('id',$id)->first();
       $badge->layout = json_encode($request->elements);
       $badge->save();

    }


    public function generateBadgePdf(Request $request)
    {
        // dd($request->all());
        
        if($request->template_name)
            $badge = NewBadge::findOrFail($request->template_name);
            // dd($badge);
        else
            $badge = NewBadge::latest()->first();
            // dd($badge);

        $userIds = json_decode($request->user_ids, true);
        $users = User::whereIn('id', $userIds)->get();
        $layout = json_decode($badge->layout, true);

        // Inch → Point
        $widthPt  = $badge->width * 72;
        $heightPt = $badge->height * 72;

        $pdf = Pdf::loadView(
            'DragAndDropBadge.pdf',
            compact('badge', 'layout', 'users')
        )->setPaper([0, 0, $widthPt, $heightPt]);

        if(!$request->template_name)
            return $pdf->download('badges.pdf');
        else
            return $pdf->stream('badges.pdf');
    }

     public function generateBadgePdfPreview(Request $request)
    {
        $badge = NewBadge::findOrFail($request->template_name);
        $userIds = json_decode($request->user_ids, true);
        $users = User::whereIn('id', [6])->get();
        $layout = json_decode($badge->layout, true);

        // Inch → Point
        $widthPt  = $badge->width * 72;
        $heightPt = $badge->height * 72;

        $pdf = Pdf::loadView(
            'DragAndDropBadge.pdf',
            compact('badge', 'layout', 'users')
        )->setPaper([0, 0, $widthPt, $heightPt]);

        return $pdf->stream('badges.pdf');
    }

}
