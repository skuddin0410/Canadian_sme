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
        //
          return view('formbuilder.index');
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
        return view('form-builder.show', compact('form'));

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

    public function submitForm(Request $request, $id): JsonResponse
    {
        $form = Form::findOrFail($id);
        
        // Dynamic validation based on form structure
        $rules = [];
        $messages = [];
        
        if ($form->validation_rules) {
            foreach ($form->validation_rules as $fieldName => $fieldRules) {
                $rules[$fieldName] = $fieldRules;
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Apply conditional logic validation
        $filteredData = $this->applyConditionalLogic($request->all(), $form->conditional_logic);

        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'submission_data' => $filteredData,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'message' => 'Form submitted successfully',
            'submission' => $submission
        ]);
    }

    private function applyConditionalLogic(array $data, ?array $conditionalLogic): array
    {
        if (!$conditionalLogic) {
            return $data;
        }

        foreach ($conditionalLogic as $rule) {
            $condition = $rule['condition'] ?? '';
            $targetField = $rule['target_field'] ?? '';
            $sourceField = $rule['source_field'] ?? '';
            $operator = $rule['operator'] ?? '==';
            $value = $rule['value'] ?? '';

            if (!isset($data[$sourceField])) {
                continue;
            }

            $sourceValue = $data[$sourceField];
            $conditionMet = $this->evaluateCondition($sourceValue, $operator, $value);

            if ($condition === 'show' && !$conditionMet) {
                unset($data[$targetField]);
            } elseif ($condition === 'hide' && $conditionMet) {
                unset($data[$targetField]);
            }
        }

        return $data;
    }

    private function evaluateCondition($sourceValue, string $operator, $value): bool
    {
        switch ($operator) {
            case '==':
                return $sourceValue == $value;
            case '!=':
                return $sourceValue != $value;
            case '>':
                return $sourceValue > $value;
            case '<':
                return $sourceValue < $value;
            case '>=':
                return $sourceValue >= $value;
            case '<=':
                return $sourceValue <= $value;
            case 'contains':
                return str_contains(strtolower($sourceValue), strtolower($value));
            case 'not_contains':
                return !str_contains(strtolower($sourceValue), strtolower($value));
            default:
                return false;
        }
        
    }
    public function showFrontendForm($id)
{
    $form = Form::where('is_active', true)->findOrFail($id);
    return view('formbuilder.showform', compact('form'));
}

}
