<?php

namespace App\Http\Controllers\SupportStaff;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelpdeskUserController extends Controller
{
    //
   public function unblock(User $user)
{
    $currentUser = auth()->user();

    // Only Support Staff or Helpdesk can use this method
    if (!$currentUser->hasAnyRole(['Support Staff Or Helpdesk'])) {
        return back()->withErrors('You do not have permission to unblock users.');
    }

    // Only unblock if currently blocked
    if ($user->is_block) {
        $user->is_block = false;
        $user->save();
        return back()->with('success', 'User unblocked successfully');
    }

    return back()->withErrors('User is not currently blocked.');
}


}
