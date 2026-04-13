<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Form;
use App\Models\TicketType;
use Illuminate\Support\Facades\Session;


class EventUserAuthController extends Controller
{
    public function showLogin(Event $event)
    {
        Session::put('event_id', $event->id);
        // dd($event);

        return view('auth.login', compact('event'));
    }

    public function showRegister(Event $event)
    {
        Session::put('event_id', $event->id);

        $form = Form::where('is_active', true)->first();
        $tickets = TicketType::where('event_id', $event->id)->get();

        return view('formbuilder.showform', compact('event', 'form', 'tickets'));
    }
}
