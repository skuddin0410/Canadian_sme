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
use App\Models\PendingRegistration;
use App\Models\PromoCodeRedemption;
use App\Models\EventWaitlist;
use App\Models\Subscription;
use App\Mail\RegistrationCredentialsMail;
use App\Services\PromoCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Models\UserTicket;
use App\Models\Event;
use App\Models\EventAndEntityLink;
use Illuminate\Support\Str;



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
            return back()->withInput()->with('error', 'Invalid access. Please start from event page.');
        }
        $event = Event::findOrFail($eventId);
        $form = Form::findOrFail($id);


        $data = $request->all();
        $registrationMode = $data['registration_mode'] ?? 'single';
        $coordinatorAttending = $request->boolean('coordinator_attending');
        $submissionAction = $data['submission_action'] ?? 'register';
        $isWaitlistSubmission = $submissionAction === 'waitlist';

        /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
        $rules = [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => [
                'required',
                'email',
                Rule::when(
                    ($registrationMode === 'single' || $coordinatorAttending) && !$isWaitlistSubmission,
                    ['unique:users,email']
                ),
            ],
            'mobile'            => 'nullable|string|max:20',
            'designation'       => 'nullable|string|max:255',
            'company'           => 'nullable|string|max:255',
            'bio'               => 'nullable|string|max:500',
            'password'          => 'nullable|string|max:255',
            'submission_action' => 'nullable|in:register,waitlist',
            'registration_mode' => 'required|in:single,team',
            'coordinator_attending' => 'nullable|boolean',
            'registration_type' => 'required|in:free,paid',
            'promo_code' => 'nullable|string|max:100',
            'selected_ticket_id' => [
                'nullable',
                Rule::requiredIf(fn () => ($data['registration_type'] ?? null) === 'paid' && !$isWaitlistSubmission),
                Rule::exists('ticket_types', 'id')->where(function ($q) use ($eventId) {
                    $q->where('event_id', $eventId);
                }),
            ],
            'team_members' => 'nullable|array|min:1',
        ];

        $rules = array_merge($rules, $this->buildTeamMemberRules($form));

        $validator = Validator::make($data, $rules);

        $validator->after(function ($validator) use ($data, $registrationMode, $coordinatorAttending, $eventId, $event, $isWaitlistSubmission) {
            $submittedTeamMembers = $this->extractFilledTeamMembers($data['team_members'] ?? []);
            $teamMembers = $submittedTeamMembers
                ->filter(fn ($member) => filled($member['email'] ?? null))
                ->values();

            if ($registrationMode === 'team' && $submittedTeamMembers->isEmpty()) {
                $validator->errors()->add('team_members', 'Please add at least one team member for team registration.');
            }

            foreach ($submittedTeamMembers as $index => $member) {
                if (blank($member['email'] ?? null)) {
                    $validator->errors()->add("team_members.$index.email", 'Email is required for each team member.');
                }
            }

            $primaryEmail = Str::lower(trim((string) ($data['email'] ?? '')));
            $memberEmails = $teamMembers
                ->map(fn ($member) => Str::lower(trim((string) ($member['email'] ?? ''))))
                ->filter();

            if ($primaryEmail !== '' && $memberEmails->contains($primaryEmail)) {
                $validator->errors()->add('team_members', 'Primary attendee email cannot be reused for a team member.');
            }

            if ($registrationMode === 'team' && !$event->enable_team_registration) {
                $validator->errors()->add('registration_mode', 'Team registration is disabled for this event.');
            }

            if (($data['registration_type'] ?? 'free') === 'free' && !$event->enable_free_registration) {
                $validator->errors()->add('registration_type', 'Free registration is disabled for this event.');
            }

            if (($data['registration_type'] ?? null) === 'paid' && !$event->enable_paid_registration) {
                $validator->errors()->add('registration_type', 'Paid registration is disabled for this event.');
            }

            $attendeeCountForCapacity = $registrationMode === 'team'
                ? $teamMembers->count() + ($coordinatorAttending ? 1 : 0)
                : 1;

            if ($isWaitlistSubmission) {
                if (!$this->canJoinWaitlist($event, $attendeeCountForCapacity)) {
                    $validator->errors()->add('registration_type', 'Registration capacity is currently available. Please submit registration instead.');
                }

                return;
            }

            if (!$this->registrationCanFitSubscription($event, $attendeeCountForCapacity)) {
                $validator->errors()->add('registration_type', 'Attendee limit has been reached for this event. Please join the waitlist.');
                return;
            }

            if (($data['registration_type'] ?? null) === 'paid' && !empty($data['selected_ticket_id'])) {
                $ticket = TicketType::where('event_id', $eventId)->find($data['selected_ticket_id']);
                $attendeeCount = $registrationMode === 'team'
                    ? $teamMembers->count() + ($coordinatorAttending ? 1 : 0)
                    : 1;

                if (!$ticket || !$ticket->isSaleOpen()) {
                    $validator->errors()->add('selected_ticket_id', 'The selected ticket is not available for purchase.');
                    return;
                }

                if ($attendeeCount < (int) $ticket->min_quantity_per_order) {
                    $validator->errors()->add('selected_ticket_id', 'This ticket requires at least ' . $ticket->min_quantity_per_order . ' attendee(s).');
                }

                if ($ticket->max_quantity_per_order && $attendeeCount > (int) $ticket->max_quantity_per_order) {
                    $validator->errors()->add('selected_ticket_id', 'This ticket allows a maximum of ' . $ticket->max_quantity_per_order . ' attendee(s) per registration.');
                }

                if ($ticket->available_quantity < $attendeeCount) {
                    $validator->errors()->add('selected_ticket_id', 'Not enough ticket quantity is available for this registration.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $teamMembers = $registrationMode === 'team'
            ? $this->extractFilledTeamMembers($data['team_members'] ?? [])
                ->filter(fn ($member) => filled($member['email'] ?? null))
                ->values()
                ->all()
            : [];
        $attendeeCount = $registrationMode === 'team'
            ? count($teamMembers) + ($coordinatorAttending ? 1 : 0)
            : 1;

        if ($isWaitlistSubmission) {
            FormSubmission::create([
                'form_id'         => $form->id,
                'submission_data' => $data,
                'ip_address'      => client_ip($request),
                'user_agent'      => $request->userAgent(),
            ]);

            EventWaitlist::create([
                'event_id' => $eventId,
                'ticket_type_id' => $data['selected_ticket_id'] ?? null,
                'form_id' => $form->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => Str::lower(trim((string) $data['email'])),
                'mobile' => $data['mobile'] ?? null,
                'company' => $data['company'] ?? null,
                'designation' => $data['designation'] ?? null,
                'registration_mode' => $registrationMode,
                'attendee_count' => $attendeeCount,
                'coordinator_attending' => $coordinatorAttending,
                'team_members' => $teamMembers,
                'request' => $data,
                'status' => 'waiting',
                'joined_at' => now(),
            ]);

            return back()->with('success', 'You have joined the waitlist. We will contact you if a spot becomes available.');
        }

        $pricingSummary = null;

        $appliedPromoCode = null;

        if ($data['registration_type'] === 'paid') {
            $ticket = TicketType::findOrFail($data['selected_ticket_id']);
            $eventId = $ticket->event_id;
            try {
                $promoPricing = app(PromoCodeService::class)->applyToTicket(
                    $ticket,
                    $attendeeCount,
                    $data['promo_code'] ?? null,
                    $data['email'] ?? null
                );
            } catch (\RuntimeException $e) {
                return back()->withInput()->withErrors([
                    'promo_code' => $e->getMessage(),
                ]);
            }

            $pricingSummary = $promoPricing['pricing'];
            $appliedPromoCode = $promoPricing['promo_code'];
        }

        // Save form submission data
        FormSubmission::create([
            'form_id'         => $form->id,
            'submission_data' => $data,
            'ip_address'      => client_ip($request),
            'user_agent'      => $request->userAgent(),
        ]);

        // If registration is FREE → create user immediately
        if ($data['registration_type'] === 'free') {
            DB::beginTransaction();
            try {
                $createdUsers = collect();
                $registeredAttendees = [];

                if ($registrationMode === 'single' || $coordinatorAttending) {
                    $user = User::create([
                        'name'        => $data['first_name'],
                        'lastname'    => $data['last_name'],
                        'email'       => $data['email'],
                        'mobile'      => $data['mobile'] ?? null,
                        'designation' => $data['designation'] ?? null,
                        'company'     => $data['company'] ?? null,
                        'bio'         => $data['bio'] ?? null,
                        'password'    => Hash::make(Str::random(32)),
                    ]);

                    $user->assignRole('Attendee');
                    EventAndEntityLink::create([
                        'event_id'    => $eventId,
                        'entity_type' => 'users',
                        'entity_id'   => $user->id,
                    ]);

                    $createdUsers->push($user);
                    $registeredAttendees[] = [
                        'name' => trim($user->name . ' ' . $user->lastname),
                        'email' => $user->email,
                    ];
                }

                foreach ($teamMembers as $member) {
                    $teamUser = User::create([
                        'name'        => $member['first_name'],
                        'lastname'    => $member['last_name'],
                        'email'       => $member['email'],
                        'mobile'      => $member['mobile'] ?? null,
                        'designation' => $member['designation'] ?? null,
                        'company'     => $member['company'] ?? ($data['company'] ?? null),
                        'bio'         => $member['bio'] ?? null,
                        'password'    => Hash::make(Str::random(32)),
                    ]);

                    $teamUser->assignRole('Attendee');
                    EventAndEntityLink::create([
                        'event_id'    => $eventId,
                        'entity_type' => 'users',
                        'entity_id'   => $teamUser->id,
                    ]);

                    $createdUsers->push($teamUser);
                    $registeredAttendees[] = [
                        'name' => trim($teamUser->name . ' ' . $teamUser->lastname),
                        'email' => $teamUser->email,
                    ];
                }

                DB::commit();
                $event = Event::find($eventId);
                $createdUserIds = $createdUsers->pluck('id')->all();
                $totalRegistrations = $createdUsers->count();
                $coordinatorName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

                $response = redirect()
                    ->route(($registrationMode === 'single' || $coordinatorAttending) ? 'event.user.login' : 'event.user.register', ['event' => Event::findOrFail($eventId)->slug])
                    ->with('success', ($registrationMode === 'single' || $coordinatorAttending)
                        ? 'Registration successful.'
                        : 'Team registration successful.');

                $isTeamRegistration = $registrationMode === 'team';
                $mailRecipientEmail = $isTeamRegistration ? ($data['email'] ?? null) : ($registeredAttendees[0]['email'] ?? null);
                $mailRecipientName = $isTeamRegistration ? $coordinatorName : ($registeredAttendees[0]['name'] ?? '');
                $loginUrl = route('event.user.login', ['event' => $event->slug]);

                dispatch(function () use ($createdUserIds, $event, $totalRegistrations, $coordinatorName, $mailRecipientEmail, $mailRecipientName, $registeredAttendees, $isTeamRegistration, $loginUrl) {
                    $users = User::whereIn('id', $createdUserIds)->get();

                    foreach ($users as $createdUser) {
                        try {
                            if (qrCode($createdUser->id)) {
                                sendNotification("Welcome Email", $createdUser);
                            }
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    }

                    if ($mailRecipientEmail && !empty($registeredAttendees)) {
                        try {
                            Mail::to($mailRecipientEmail)->send(
                                new RegistrationCredentialsMail(
                                    $mailRecipientName ?: 'Participant',
                                    $event,
                                    $loginUrl,
                                    $registeredAttendees,
                                    $isTeamRegistration
                                )
                            );
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    }

                    if ($event) {
                        $superAdminId = 1;
                        $eventAdminId = $event->created_by;

                        $notificationData = [
                            'title' => 'New Self-Registration',
                            'body' => $totalRegistrations > 1
                                ? $totalRegistrations . ' attendees have been registered for the event "' . $event->title . '" by coordinator "' . ($coordinatorName ?: 'Unknown') . '"'
                                : 'A new attendee has self-registered for the event "' . $event->title . '"',
                            'related_type' => 'event',
                            'related_id' => $event->id,
                            'is_read' => 0
                        ];

                        \App\Models\GeneralNotification::create(array_merge($notificationData, ['user_id' => $superAdminId]));

                        if ($eventAdminId && $eventAdminId != $superAdminId) {
                            \App\Models\GeneralNotification::create(array_merge($notificationData, ['user_id' => $eventAdminId]));
                        }
                    }
                })->afterResponse();

                return $response;

                // return back()->with('success', 'Form submitted successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                report($e);
                return back()->withInput()->with('error', app()->environment('local')
                    ? 'Registration failed: ' . $e->getMessage()
                    : 'Registration failed due to a server error. Please try again.');
            }
        }

        // If registration is PAID → redirect to payment gateway
        if ($data['registration_type'] === 'paid') {

            DB::beginTransaction();
            try {
                // 1. Use the promo-adjusted pricing already validated above.
                $ticket = $ticket ?? TicketType::findOrFail($data['selected_ticket_id']);
                $amount = (float) ($pricingSummary['total'] ?? 0);

                // 2. Store pending registration payload until payment succeeds
                $pendingRegistration = PendingRegistration::create([
                    'ticket_type_id' => $ticket->id,
                    'event_id'       => $eventId,
                    'amount'         => $amount,
                    'request'        => [
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'email' => $data['email'],
                        'mobile' => $data['mobile'] ?? null,
                        'designation' => $data['designation'] ?? null,
                        'company' => $data['company'] ?? null,
                        'bio' => $data['bio'] ?? null,
                        'registration_mode' => $data['registration_mode'],
                        'coordinator_attending' => $coordinatorAttending,
                        'registration_type' => $data['registration_type'],
                        'promo_code' => $appliedPromoCode?->code,
                        'promo_code_id' => $appliedPromoCode?->id,
                        'selected_ticket_id' => $ticket->id,
                        'team_members' => $teamMembers,
                        'attendee_count' => $attendeeCount,
                        'pricing_summary' => $pricingSummary,
                        'form_id' => $form->id,
                    ],
                    'status'         => 'pending_payment',
                ]);

                if ($appliedPromoCode && (float) ($pricingSummary['promo_discount'] ?? 0) > 0) {
                    PromoCodeRedemption::create([
                        'promo_code_id' => $appliedPromoCode->id,
                        'event_id' => $eventId,
                        'ticket_type_id' => $ticket->id,
                        'pending_registration_id' => $pendingRegistration->id,
                        'email' => Str::lower(trim((string) ($data['email'] ?? ''))),
                        'code' => $appliedPromoCode->code,
                        'attendee_count' => $attendeeCount,
                        'discount_amount' => $pricingSummary['promo_discount'] ?? 0,
                        'final_total' => $pricingSummary['total'] ?? $amount,
                        'status' => 'pending',
                    ]);
                }

                DB::commit();

                // 3. Redirect to Stripe checkout
                return redirect()->route('payment.checkout', [
                    'pending_registration_id' => $pendingRegistration->id,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                report($e);
                return back()->withInput()->with('error', app()->environment('local')
                    ? 'Registration failed: ' . $e->getMessage()
                    : 'Registration failed due to a server error. Please try again.');
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

    private function buildTeamMemberRules(Form $form): array
    {
        $rules = [];

        foreach (($form->form_data ?? []) as $field) {
            $type = $field['type'] ?? 'text';
            $label = $field['label'] ?? ucfirst($type);
            $name = Str::slug($label, '_');

            if ($name === 'password') {
                continue;
            }

            $isRequired = in_array('required', $field['validation'] ?? []);
            $baseKey = "team_members.*.$name";
            $options = array_values($field['options'] ?? []);
            $ruleSet = [];

            switch ($type) {
                case 'email':
                    $ruleSet[] = 'nullable';
                    $ruleSet[] = 'email';
                    $ruleSet[] = 'distinct';
                    $ruleSet[] = 'unique:users,email';
                    break;

                case 'number':
                    $ruleSet[] = $isRequired ? 'required_with:team_members.*.email' : 'nullable';
                    $ruleSet[] = 'numeric';
                    if (isset($field['min']) && $field['min'] !== '') {
                        $ruleSet[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max']) && $field['max'] !== '') {
                        $ruleSet[] = 'max:' . $field['max'];
                    }
                    break;

                case 'date':
                    $ruleSet[] = $isRequired ? 'required_with:team_members.*.email' : 'nullable';
                    $ruleSet[] = 'date';
                    break;

                case 'select':
                case 'radio':
                    $ruleSet[] = $isRequired ? 'required_with:team_members.*.email' : 'nullable';
                    $ruleSet[] = 'string';
                    if (!empty($options)) {
                        $ruleSet[] = Rule::in($options);
                    }
                    break;

                case 'checkbox':
                    $ruleSet[] = $isRequired ? 'required_with:team_members.*.email' : 'nullable';
                    $ruleSet[] = 'array';
                    $rules[$baseKey] = $ruleSet;
                    if (!empty($options)) {
                        $rules[$baseKey . '.*'] = [Rule::in($options)];
                    }
                    continue 2;

                case 'textarea':
                case 'text':
                default:
                    $ruleSet[] = $isRequired ? 'required_with:team_members.*.email' : 'nullable';
                    $ruleSet[] = 'string';
                    if (isset($field['min']) && $field['min'] !== '') {
                        $ruleSet[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max']) && $field['max'] !== '') {
                        $ruleSet[] = 'max:' . $field['max'];
                    }
                    break;
            }

            $rules[$baseKey] = $ruleSet;
        }

        return $rules;
    }

    private function extractFilledTeamMembers(array $members)
    {
        return collect($members)
            ->filter(function ($member) {
                if (!is_array($member)) {
                    return false;
                }

                foreach ($member as $value) {
                    if (is_array($value) && collect($value)->filter(fn ($item) => filled($item))->isNotEmpty()) {
                        return true;
                    }

                    if (!is_array($value) && filled($value)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
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
        $event = Event::findOrFail($eventId);

        if (!$event->enable_free_registration && !$event->enable_paid_registration) {
            return redirect()->back()->with('error', 'Registration is currently disabled for this event.');
        }

        $form = Form::where('is_active', true)->firstOrFail();

        $tickets = TicketType::where('event_id', $eventId)
            ->where('is_active', 1)
            ->get();

        return view('formbuilder.showform', compact('form', 'tickets', 'event'));
    }

    public function available(Event $event)
    {
        $attendeeCount = max((int) request('attendee_count', 1), 1);

        if (!$this->registrationCanFitSubscription($event, $attendeeCount)) {
            return response()->json([]);
        }

        $tickets = TicketType::where('event_id', $event->id)
            ->where('is_active', 1)
            ->get();

        return response()->json(
            $tickets
                ->filter(fn ($ticket) => $ticket->isSaleOpen())
                ->filter(function ($ticket) use ($attendeeCount) {
                    if ($attendeeCount < (int) $ticket->min_quantity_per_order) {
                        return false;
                    }

                    if ($ticket->max_quantity_per_order && $attendeeCount > (int) $ticket->max_quantity_per_order) {
                        return false;
                    }

                    return $ticket->available_quantity >= $attendeeCount;
                })
                ->map(function ($ticket) use ($attendeeCount) {
                    $pricing = $ticket->getRegistrationPricing($attendeeCount);

                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->name,
                        'description' => $ticket->description,
                        'base_price' => (float) $ticket->base_price,
                        'available_quantity' => (int) $ticket->available_quantity,
                        'pricing' => $pricing,
                        'formatted_total' => number_format($pricing['total'], 2, '.', ''),
                    ];
                })
                ->values()
        );
    }

    public function pricingSummary(Request $request, Event $event)
    {
        $request->validate([
            'ticket_id' => [
                'required',
                Rule::exists('ticket_types', 'id')->where(function ($query) use ($event) {
                    $query->where('event_id', $event->id);
                }),
            ],
            'attendee_count' => 'required|integer|min:1',
            'promo_code' => 'nullable|string|max:100',
            'email' => 'nullable|email',
        ]);

        $ticket = TicketType::findOrFail($request->ticket_id);
        try {
            $result = app(PromoCodeService::class)->applyToTicket(
                $ticket,
                max((int) $request->attendee_count, 1),
                $request->promo_code,
                $request->email
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'ticket_id' => $ticket->id,
            'ticket_name' => $ticket->name,
            'pricing' => $result['pricing'],
            'promo_code' => $result['promo_code']?->only(['id', 'code', 'discount_type', 'discount_value']),
        ]);
    }

    public function registrationCapacity(Request $request, Event $event)
    {
        $request->validate([
            'attendee_count' => 'required|integer|min:1',
        ]);

        return response()->json($this->getSubscriptionCapacity($event, (int) $request->attendee_count));
    }

    protected function canJoinWaitlist(Event $event, int $attendeeCount): bool
    {
        return !$this->registrationCanFitSubscription($event, $attendeeCount);
    }

    protected function registrationCanFitSubscription(Event $event, int $attendeeCount): bool
    {
        return (bool) $this->getSubscriptionCapacity($event, $attendeeCount)['can_register'];
    }

    protected function getSubscriptionCapacity(Event $event, int $attendeeCount): array
    {
        $attendeeCount = max($attendeeCount, 1);

        if ((int) $event->created_by === 1) {
            return [
                'can_register' => true,
                'is_unlimited' => true,
                'limit' => null,
                'used' => null,
                'remaining' => null,
                'requested' => $attendeeCount,
            ];
        }

        $subscription = $event->subscription_id
            ? Subscription::active()->whereKey($event->subscription_id)->first()
            : Subscription::active()->where('user_id', $event->created_by)->latest()->first();

        if (!$subscription) {
            return [
                'can_register' => false,
                'is_unlimited' => false,
                'limit' => 0,
                'used' => 0,
                'remaining' => 0,
                'requested' => $attendeeCount,
            ];
        }

        $subscriptionEventIds = Event::where('subscription_id', $subscription->id)->pluck('id');
        if ($subscriptionEventIds->isEmpty()) {
            $subscriptionEventIds = collect([$event->id]);
        }

        $used = EventAndEntityLink::where('entity_type', 'users')
            ->whereIn('event_id', $subscriptionEventIds)
            ->distinct('entity_id')
            ->count('entity_id');

        $limit = (int) $subscription->attendee_count;
        $remaining = max($limit - $used, 0);

        return [
            'can_register' => $remaining >= $attendeeCount,
            'is_unlimited' => false,
            'limit' => $limit,
            'used' => $used,
            'remaining' => $remaining,
            'requested' => $attendeeCount,
        ];
    }

    protected function ticketCanFitAttendeeCount(TicketType $ticket, int $attendeeCount): bool
    {
        if (!$ticket->isSaleOpen()) {
            return false;
        }

        if ($attendeeCount < (int) $ticket->min_quantity_per_order) {
            return false;
        }

        if ($ticket->max_quantity_per_order && $attendeeCount > (int) $ticket->max_quantity_per_order) {
            return false;
        }

        return (int) $ticket->available_quantity >= $attendeeCount;
    }
}
