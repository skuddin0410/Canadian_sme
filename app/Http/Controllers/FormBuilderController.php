<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FormBuilderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $form = Form::find(1);
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
        $form = Form::updateOrCreate(
           ['id' => 1], // The condition to check if the record exists (in this case, the form with id = 1)
           $request->all() // The data to update or insert
        );

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

    // Dynamic validation from form config (fallback if none set)
    $rules = $form->validation_rules ?? [
        'first_name'   => 'required|string|max:255',
        'last_name'    => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email',
        'mobile' => 'null|string|max:20',
        'designation'  => 'nullable|string|max:255',
        'company'      => 'nullable|string|max:255',
        'bio'      => 'nullable|string|max:500',
        'password'      => 'required', //new addition by joydeep
    ];

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Apply conditional logic (if any)
    $filteredData = $this->applyConditionalLogic($data, $form->conditional_logic ?? []);
    $filteredData = is_array($filteredData) ? $filteredData : [];

    // Save submission in form_submissions table
    $submission = FormSubmission::create([
        'form_id'         => $form->id,
        'submission_data' => $filteredData,
        'ip_address'      => $request->ip(),
        'user_agent'      => $request->userAgent(),
    ]);

    // Create user with role "attendee"
    $user = new User();
    $user->name        = $data['first_name'];
    $user->lastname    = $data['last_name'];
    $user->email       = $data['email'];
    // $user->password = Hash::make('password'); // default or random if needed
    $user->mobile      = $data['mobile'] ?? null;
    $user->designation = $data['designation'] ?? null;
    $user->company     = $data['company'] ?? null;
    $user->bio         = $data['bio'] ?? null;
    $user->password         = Hash::make($data['password']); //new addition by joydeep
    $user->save();
    $user->assignRole('Attendee');
    notification($user->id);
    if(qrCode($user->id)){
     $user = User::where('id',$user->id)->first();   
     sendNotification("Welcome Email",$user);
    }
    return redirect()
        ->back()
        ->with('success', 'Form submitted successfully and attendee created!');
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
