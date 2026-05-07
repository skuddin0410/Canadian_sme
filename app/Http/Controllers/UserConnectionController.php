<?php

namespace App\Http\Controllers;

use App\Models\UserConnection;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Response;

class UserConnectionController extends Controller
{
    public function index(Request $request)
    {
        $query = User::join('user_connections', 'users.id', '=', 'user_connections.user_id')
            ->select('users.*','user_connections.id as connection_id', DB::raw('COUNT(user_connections.id) as total_connections'))
            ->groupBy('user_connections.user_id', 'users.id');

        if (!isSuperAdmin()) {
            $query->whereIn('user_connections.event_id', getEventIds());
        }

        if ($request->filled('event_id')) {
            $query->where('user_connections.event_id', $request->event_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.lastname', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $connections = $query->paginate(10)->withQueryString();
        
        $events = isSuperAdmin() 
            ? \App\Models\Event::orderBy('title')->get(['id', 'title']) 
            : \App\Models\Event::whereIn('id', getEventIds())->orderBy('title')->get(['id', 'title']);

        return view('user_connections.index', compact('connections', 'events'));

    }

    public function show(UserConnection $userConnection)
    {   
        $user = User::findOrFail($userConnection->user_id);
        $query = UserConnection::with('connection')->where('user_id', $user->id);
        if (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }
        $connections = $query->get();
        return view('user_connections.show', compact('user', 'connections'));
    }

    public function export($user_id)
    {
    $user = User::findOrFail($user_id);
    $query = UserConnection::with('connection')->where('user_id', $user->id);
    if (!isSuperAdmin()) {
        $query->whereIn('event_id', getEventIds());
    }
    $connections = $query->get();

    $filename = 'connections_' . $user->id . '.csv';

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$filename}",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['Connection Name', 'Email', 'Company', 'Designation', 'Rating', 'Note'];

    $callback = function() use ($connections, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($connections as $connection) {

            if(!empty($connection->connection)){
            fputcsv($file, [
                $connection->connection->full_name ?? '',
                $connection->connection->email ?? '',
                $connection->connection->company ?? '',
                $connection->connection->designation ?? '',
                $connection->rating ?? '',
                $connection->note ?? '',
            ]);
            }
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
   }

}
