<?php

namespace App\Http\Controllers;

use App\Models\UserConnection;
use Illuminate\Http\Request;
use App\Models\User;
use DB;

class UserConnectionController extends Controller
{
    public function index()
    {
        $connections = User::join('user_connections', 'users.id', '=', 'user_connections.user_id')
            ->select('users.*', DB::raw('COUNT(user_connections.id) as total_connections'))
            ->groupBy('user_connections.user_id', 'users.id')
            ->paginate(10);
        return view('user_connections.index', compact('connections'));

    }

    public function show(UserConnection $userConnection)
    {   

        $user = User::findOrFail($userConnection->user_id);
        $connections = UserConnection::with('connection')->where('user_id', $user->id)->get();
        return view('user_connections.show', compact('user', 'connections'));
    }

    public function export($user_id)
    {
        $user = User::findOrFail($user_id);
        $connections = UserConnection::with('connection')
        ->where('user_id', $user_id)
        ->get();

    $filename = 'connections_' . $user->id . '.csv';

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$filename}",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['Connection Name', 'Email', 'Company', 'Designation'];

    $callback = function() use ($connections, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($connections as $connection) {
            fputcsv($file, [
                $connection->connection->name ?? 'N/A',
                $connection->connection->email ?? 'N/A',
                $connection->connection->company ?? 'N/A',
                $connection->connection->designation ?? 'N/A',
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
   }

}
