<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrawController extends Controller
{
    // Create new draw
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date' => 'nullable|date'
        ]);

        $draw = Draw::create($validated);
        return response()->json($draw, 201);
    }

    // View draw details
    public function show(Draw $draw)
    {
        return response()->json($draw->load('tickets.user', 'winner'));
    }

    // Pick random weighted winner
    public function pickWinner(Draw $draw)
    {

        // if ($draw->status !== 'open') {
        //     return response()->json(['message' => 'Draw already closed or completed'], 400);
        // }

        // Aggregate tickets per user
        $ticketCounts = Ticket::where('draw_id', $draw->id)
            ->select('user_id', DB::raw('COUNT(*) as tickets_count'))
            ->groupBy('user_id')
            ->get();

        if ($ticketCounts->isEmpty()) {
            return response()->json(['message' => 'No tickets available'], 404);
        }

        // Compute total tickets
        $totalTickets = $ticketCounts->sum('tickets_count');

        // Pick a random number between 1 and totalTickets
        $rand = rand(1, $totalTickets);

        // Find the winner based on cumulative ticket counts
        $cumulative = 0;
        foreach ($ticketCounts as $t) {
            $cumulative += $t->tickets_count;
            if ($rand <= $cumulative) {
                $winnerUserId = $t->user_id;
                $winnerTicketsCount = $t->tickets_count;
                break;
            }
        }

        // Update draw
        $draw->update([
            'winner_user_id' => $winnerUserId,
            'status' => 'completed'
        ]);

        $winner = User::find($winnerUserId);

        return response()->json([
            'message' => 'Winner selected successfully',
            'winner' => [
                'id' => $winner->id,
                'name' => $winner->name,
                'email' => $winner->email,
                'tickets_in_draw' => $winnerTicketsCount
            ]
        ]);
    }

    public function drawSummary(Draw $draw)
    {
        // Total tickets in this draw
        $totalTickets = $draw->tickets()->count();

        // Winner info
        $winner = $draw->winner;

        $winnerData = null;
        if ($winner) {
            // Count winner's tickets in this draw
            $winnerTicketsCount = $draw->tickets()
                ->where('user_id', $winner->id)
                ->count();

            $winnerData = [
                'id' => $winner->id,
                'name' => $winner->name,
                'email' => $winner->email,
                'tickets_in_draw' => $winnerTicketsCount
            ];
        }

        return response()->json([
            'draw_id' => $draw->id,
            'draw_name' => $draw->name,
            'total_tickets' => $totalTickets,
            'winner' => $winnerData
        ]);
    }

    public function drawSummaryWithUsers(Draw $draw)
    {
        // Total tickets in this draw
        $totalTickets = $draw->tickets()->count();

        // Winner info
        $winner = $draw->winner;
        $winnerData = null;

        if ($winner) {
            $winnerTicketsCount = $draw->tickets()
                ->where('user_id', $winner->id)
                ->count();

            $winnerData = [
                'id' => $winner->id,
                'name' => $winner->name,
                'email' => $winner->email,
                'tickets_in_draw' => $winnerTicketsCount
            ];
        }

        // Users who participated in this draw with ticket counts
        $participants = $draw->tickets()
            ->selectRaw('user_id, COUNT(*) as tickets_count')
            ->groupBy('user_id')
            ->with('user:id,name,email') // eager load user info
            ->get()
            ->map(function($t){
                return [
                    'id' => $t->user->id,
                    'name' => $t->user->name,
                    'email' => $t->user->email,
                    'tickets_in_draw' => $t->tickets_count
                ];
            })
            ->sortByDesc('tickets_in_draw')
            ->values();

        return response()->json([
            'draw_id' => $draw->id,
            'draw_name' => $draw->name,
            'total_tickets' => $totalTickets,
            'winner' => $winnerData,
            'participants' => $participants
        ]);
    }
}

