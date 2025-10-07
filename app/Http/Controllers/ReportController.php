<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function usersDrawsTicketCount()
    {
        // Step 1: Aggregate tickets per user per draw
        $ticketAggregates = Ticket::select('user_id','draw_id', DB::raw('COUNT(*) as tickets_count'))
            ->groupBy('user_id', 'draw_id')
        ->get();

        // Step 2: Get all relevant users and draws in one query each
        $userIds = $ticketAggregates->pluck('user_id')->unique();
        $drawIds = $ticketAggregates->pluck('draw_id')->unique();

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
        $draws = Draw::whereIn('id', $drawIds)->get()->keyBy('id');

        // Step 3: Format response
        $result = $ticketAggregates->groupBy('user_id') // group tickets by user
            ->map(function($tickets, $userId) use ($users, $draws) {
                $user = $users[$userId];
                $totalTickets = $tickets->sum('tickets_count');

                $drawsData = $tickets->map(function($t) use ($draws) {
                    $draw = $draws[$t->draw_id];
                    return [
                        'draw_id' => $draw->id,
                        'draw_name' => $draw->name,
                        'tickets_count' => $t->tickets_count,
                    ];
                });

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_tickets' => $totalTickets,
                'draws' => $drawsData,
            ];
        })
        ->sortByDesc('total_tickets') // sort by total tickets
        ->values();

        return response()->json($result);
    }

    public function usersTicketsByDraw(Draw $draw)
    {
        // Join tickets with users and count tickets per user for this draw
        $users = User::select('users.id', 'users.name', 'users.email', DB::raw('COUNT(tickets.id) as tickets_count'))
            ->join('tickets', 'users.id', '=', 'tickets.user_id')
            ->where('tickets.draw_id', $draw->id)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('tickets_count')
            ->get();

        return response()->json([
            'draw_id' => $draw->id,
            'draw_name' => $draw->name,
            'users' => $users
        ]);
    }
}

