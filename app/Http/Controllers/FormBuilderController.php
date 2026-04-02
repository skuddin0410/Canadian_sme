<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FormSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\TicketType;
use App\Models\TicketPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\UserTicket;
use App\Models\Event;


class FormBuilderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $form = Form::find(1);
        return view('formbuilder.index', compact('form'));
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
    public function store(Request $request): JsonResponse
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
    public function update(Request $request, $id): JsonResponse
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
    public function destroy($id): JsonResponse
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
        $eventId = session('event_id');
        if (!$eventId) {
            return back()->with('error', 'Invalid access. Please start from event page.');
        }
        $form = Form::findOrFail($id);


        $data = $request->all();

        /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
        $rules = [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'mobile'            => 'nullable|string|max:20',
            'designation'       => 'nullable|string|max:255',
            'company'           => 'nullable|string|max:255',
            'bio'               => 'nullable|string|max:500',
            'password'          => 'required',
            'registration_type' => 'required|in:free,paid',
            'selected_ticket_id' => [
                'nullable',
                'required_if:registration_type,paid',
                Rule::exists('ticket_types', 'id')->where(function ($q) use ($eventId) {
                    $q->where('event_id', $eventId);
                }),
            ],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
      

        if ($data['registration_type'] === 'paid') {
            $ticket = TicketType::findOrFail($data['selected_ticket_id']);
            $eventId = $ticket->event_id;
        }

        // Save form submission data
        FormSubmission::create([
            'form_id'         => $form->id,
            'submission_data' => $data,
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
        ]);

        // If registration is FREE → create user immediately
        if ($data['registration_type'] === 'free') {
            DB::beginTransaction();
            try {
                $user = User::create([
                    'name'        => $data['first_name'],
                    'lastname'    => $data['last_name'],
                    'email'       => $data['email'],
                    'mobile'      => $data['mobile'] ?? null,
                    'designation' => $data['designation'] ?? null,
                    'company'     => $data['company'] ?? null,
                    'bio'         => $data['bio'] ?? null,
                    'password'    => Hash::make($data['password']),
                ]);

                $user->assignRole('Attendee');

                DB::commit();

                notification($user->id);

                if (qrCode($user->id)) {
                    sendNotification("Welcome Email", $user);
                }
                return redirect()
                    ->route('event.user.login', ['event' => $eventId])
                    ->with('success', 'Form submitted successfully and attendee created!');

                // return back()->with('success', 'Form submitted successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', $e->getMessage());
            }
        }

        // If registration is PAID → redirect to payment gateway
        if ($data['registration_type'] === 'paid') {

            DB::beginTransaction();
            try {
                // 1. Create user
                $user = User::create([
                    'name'        => $data['first_name'],
                    'lastname'    => $data['last_name'],
                    'email'       => $data['email'],
                    'mobile'      => $data['mobile'] ?? null,
                    'designation' => $data['designation'] ?? null,
                    'company'     => $data['company'] ?? null,
                    'bio'         => $data['bio'] ?? null,
                    'password'    => Hash::make($data['password']),
                ]);

                $user->assignRole('Attendee');

                // 2. Get ticket & price from base_price
                $ticket = TicketType::findOrFail($data['selected_ticket_id']);
                $amount = $ticket->base_price;

                // 3. Create TicketPurchase
                $ticketPurchase = TicketPurchase::create([
                    'user_id'        => $user->id,
                    'ticket_type_id' => $ticket->id,
                    'event_id'       => $eventId,
                    'amount'         => $amount,
                    'status'         => 'pending_payment',
                ]);

                DB::commit();

                // 4. Redirect to Stripe checkout
                return redirect('/payment/checkout?ticket_purchase_id=' . $ticketPurchase->id);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', $e->getMessage());
            }
        }
    }
    // public function submitForm(Request $request, $id)
    // {
    //     $form = Form::findOrFail($id);

    //     $data = $request->all();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Validation
    //     |--------------------------------------------------------------------------
    //     */
    //     $rules = [
    //         'first_name'   => 'required|string|max:255',
    //         'last_name'    => 'required|string|max:255',
    //         'email'        => 'required|email|unique:users,email',
    //         'mobile'       => 'nullable|string|max:20',
    //         'designation'  => 'nullable|string|max:255',
    //         'company'      => 'nullable|string|max:255',
    //         'bio'          => 'nullable|string|max:500',
    //         'password'     => 'required',
    //         'registration_type'   => 'required|in:free,paid',
    //         'selected_ticket_id' => 'required_if:registration_type,paid|exists:ticket_types,id',
    //     ];

    //     $validator = Validator::make($data, $rules);

    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     DB::beginTransaction();

    //     try {

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Save form submission
    //         |--------------------------------------------------------------------------
    //         */
    //         FormSubmission::create([
    //             'form_id'         => $form->id,
    //             'submission_data' => $data,
    //             'ip_address'      => $request->ip(),
    //             'user_agent'      => $request->userAgent(),
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create User
    //         |--------------------------------------------------------------------------
    //         */
    //         $user = User::create([
    //             'name'        => $data['first_name'],
    //             'lastname'    => $data['last_name'],
    //             'email'       => $data['email'],
    //             'mobile'      => $data['mobile'] ?? null,
    //             'designation' => $data['designation'] ?? null,
    //             'company'     => $data['company'] ?? null,
    //             'bio'         => $data['bio'] ?? null,
    //             'password'    => Hash::make($data['password']),
    //         ]);

    //         $user->assignRole('Attendee');

    //         /*
    //         |--------------------------------------------------------------------------
    //         | If PAID → create ticket purchase entry
    //         |--------------------------------------------------------------------------
    //         */
    //         if ($data['registration_type'] === 'paid') {

    //             $ticket = TicketType::find($data['selected_ticket_id']);

    //             $amount = $ticket->pricingRules->first()->price ?? 0;

    //             TicketPurchase::create([
    //                 'user_id'        => $user->id,
    //                 'ticket_type_id' => $ticket->id,
    //                 'event_id'       => $ticket->event_id, // important
    //                 'amount'         => $amount,
    //                 'request'        => null,
    //                 'response'       => null,
    //                 'status'         => 'pending_payment',
    //             ]);
    //         }

    //         DB::commit();

    //         notification($user->id);

    //         if (qrCode($user->id)) {
    //             sendNotification("Welcome Email", $user);
    //         }

    //         return back()->with('success', 'Form submitted successfully!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', $e->getMessage());
    //     }
    // }

    // public function submitForm(Request $request, $id)
    // {
    //     $form = Form::findOrFail($id);

    //     // Detect request type (web fetch with JSON or API client)
    //     $data = $request->expectsJson()
    //         ? $request->json()->all()
    //         : $request->all();

    //     // Dynamic validation from form config (fallback if none set)
    //     $rules = $form->validation_rules ?? [
    //         'first_name'   => 'required|string|max:255',
    //         'last_name'    => 'required|string|max:255',
    //         'email'        => 'required|email|unique:users,email',
    //         'mobile' => 'null|string|max:20',
    //         'designation'  => 'nullable|string|max:255',
    //         'company'      => 'nullable|string|max:255',
    //         'bio'      => 'nullable|string|max:500',
    //         'password'      => 'required', //new addition by joydeep
    //         'registration_type' => 'required|in:free,paid',
    //         'selected_ticket_id' => 'required_if:registration_type,paid|exists:ticket_types,id',
    //     ];
    // //     $rules['registration_type'] = 'required|in:free,paid';
    // // $rules['selected_ticket_id'] = 'required_if:registration_type,paid|exists:ticket_types,id';

    //     $validator = Validator::make($data, $rules);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     // Apply conditional logic (if any)
    //     $filteredData = $this->applyConditionalLogic($data, $form->conditional_logic ?? []);
    //     $filteredData = is_array($filteredData) ? $filteredData : [];

    //     // Save submission in form_submissions table
    //     $submission = FormSubmission::create([
    //         'form_id'         => $form->id,
    //         'submission_data' => $filteredData,
    //         'ip_address'      => $request->ip(),
    //         'user_agent'      => $request->userAgent(),
    //     ]);

    //     // Create user with role "attendee"
    //     $user = new User();
    //     $user->name        = $data['first_name'];
    //     $user->lastname    = $data['last_name'];
    //     $user->email       = $data['email'];
    //     // $user->password = Hash::make('password'); // default or random if needed
    //     $user->mobile      = $data['mobile'] ?? null;
    //     $user->designation = $data['designation'] ?? null;
    //     $user->company     = $data['company'] ?? null;
    //     $user->bio         = $data['bio'] ?? null;
    //     $user->password         = Hash::make($data['password']); //new addition by joydeep
    //     $user->save();
    //     $user->assignRole('Attendee');
    //     if ($data['registration_type'] === 'paid') {
    //     UserTicket::create([
    //         'user_id' => $user->id,
    //         'ticket_type_id' => $data['selected_ticket_id'],
    //         'status' => 'pending_payment'
    //     ]);
    // }
    //     notification($user->id);
    //     if(qrCode($user->id)){
    //      $user = User::where('id',$user->id)->first();   
    //      sendNotification("Welcome Email",$user);
    //     }
    //     return redirect()
    //         ->back()
    //         ->with('success', 'Form submitted successfully and attendee created!');
    // }


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
                return str_contains(strtolower((string) $sourceValue), strtolower((string) $value));
            case 'not_contains':
                return !str_contains(strtolower((string) $sourceValue), strtolower((string) $value));
            default:
                return false;
        }
    }


    public function showFrontendForm()
    {
        if (!session()->has('event_id')) {
            return redirect()->back()->with('error', 'Please access registration from event page.');
        }

        $eventId = session('event_id');

        $form = Form::where('is_active', true)->firstOrFail();

        $tickets = TicketType::where('event_id', $eventId)->get();
        return view('formbuilder.showform', compact('form', 'tickets'));
    }

    public function available(Event $event)
    {
        $tickets = TicketType::where('event_id', $event->id)
            ->where('is_active', 1)
            ->get();

        return response()->json($tickets);
    }
}
