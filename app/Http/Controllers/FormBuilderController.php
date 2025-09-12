<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FormBuilderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $form = Form::find(1);;
        return view('formbuilder.index',compact('form'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse
    {   

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'form_data' => 'required|array',
            'validation_rules' => 'nullable|array',
            'conditional_logic' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $form = Form::create($request->all());

        return response()->json(['form' => $form, 'message' => 'Form created successfully']);
    
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $form = Form::findOrFail($id);
        return view('formbuilder.show', compact('form'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id) : JsonResponse
    {
        $form = Form::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'form_data' => 'required|array',
            'validation_rules' => 'nullable|array',
            'conditional_logic' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $form->update($request->all());

        return response()->json(['form' => $form, 'message' => 'Form updated successfully']);
    
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
    {
        $form = Form::findOrFail($id);
        $form->delete();

        return response()->json(['message' => 'Form deleted successfully']);
 
        
    }
      public function getForms(): JsonResponse
    {
        $forms = Form::where('is_active', true)->get();
        return response()->json(['forms' => $forms]);
    }

public function submitForm(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        // Detect request type (web fetch with JSON or API client)
        $data = $request->expectsJson()
            ? $request->json()->all()
            : $request->all();

        // Dynamic validation from form config
        $rules = $form->validation_rules ?? [];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Apply conditional logic (if any)
        $filteredData = $this->applyConditionalLogic($data, $form->conditional_logic ?? []);
        $filteredData = is_array($filteredData) ? $filteredData : [];

        // Save submission
        $submission = FormSubmission::create([
            'form_id'       => $form->id,
            'submission_data' => $filteredData,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);
         return redirect()
        ->back()
        ->with('success', 'Form submitted successfully!');

        // return response()->json([
        //     'message' => 'Form submitted successfully',
        //     'submission' => $submission,
        // ], 201);
    }

    private function applyConditionalLogic(array $data, array $logic = [])
    {
        // For now, just return raw data
        // TODO: add logic processing
        return $data;
    }


private function evaluateCondition($sourceValue, string $operator, $value): bool
{
    // Handle array values (checkbox, multi-select)
    if (is_array($sourceValue)) {
        if ($operator === 'contains') {
            return in_array($value, $sourceValue);
        }
        if ($operator === 'not_contains') {
            return !in_array($value, $sourceValue);
        }
        $sourceValue = implode(',', $sourceValue);
    }

    switch ($operator) {
        case '==':  return $sourceValue == $value;
        case '!=':  return $sourceValue != $value;
        case '>':   return $sourceValue > $value;
        case '<':   return $sourceValue < $value;
        case '>=':  return $sourceValue >= $value;
        case '<=':  return $sourceValue <= $value;
        case 'contains':     return str_contains(strtolower((string) $sourceValue), strtolower((string) $value));
        case 'not_contains': return !str_contains(strtolower((string) $sourceValue), strtolower((string) $value));
        default:    return false;
    }
}


public function showFrontendForm()
{
    $form = Form::where('is_active', true)->firstOrFail(); 
    return view('formbuilder.showform', compact('form'));
}


   

}
