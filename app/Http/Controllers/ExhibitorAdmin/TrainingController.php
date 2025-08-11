<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\Drive;
use Illuminate\Support\Facades\Storage;


class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trainings = Training::with('material')->orderBy('id', 'desc')->paginate(10);
         if ($request->ajax()) {
        return view('company.branding.partials.training-table', compact('trainings'))->render();
    }
        return view('company.branding.training-index', compact('trainings'));
  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company.branding.training-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
          $request->validate([
            'material_name' => 'required|string',
            'material_description' => 'required|string',
            'youtube_link' => [
            'nullable',
            'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/'
        ]
        ]);
 
        $training = new Training();
        $training->material_name = $request->material_name;
        $training->material_description = $request->material_description;
        $training->youtube_link = $request->youtube_link;
        $training->save();
        
        if(!empty($request->file("file"))){
             $this->imageUpload($request->file("file"),"training_material",$training->id,'trainings','training_material');
         }
 
       
      
    return redirect()->route('trainings.index')->with('success', 'Training material uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $training = Training::with('material')->findOrFail($id);
        return view('company.branding.training-show', compact('training'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $training = Training::with('material')->findOrFail($id);
        return view('company.branding.training-edit', compact('training'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $training =Training::find($id);
        if(!$training){
            return ;
        }
 
          $request->validate([
            'material_name' => 'required|string',
            'material_description' => 'required|string',
            'youtube_link' => [
            'nullable',
            'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/'
        ]
        ]);
 
        
        $training->material_name = $request->material_name;
        $training->material_description = $request->material_description;
        $training->youtube_link = $request->youtube_link;
        $training->save();
         if(!empty($request->file("file"))){
              $this->imageUpload($request->file("file"),"training_material",$training->id,'trainings','training_material',$idForUpdate=$training->id);
         }
 
        

    return redirect()->route('trainings.index')->with('success', 'Training material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
         $training = Training::findOrFail($id);

    // Delete the file from storage
    $file = Drive::where([
        'table_id'   => $training->id,
        'table_type' => 'trainings',
        'file_type'  => 'training_material',
    ])->first();

    if ($file) {
        Storage::disk('public')->delete($file->file_name);
        $file->delete();
    }

    $training->delete();

    return redirect()->route('trainings.index')->with('success', 'Training material deleted successfully.');
    }
}
