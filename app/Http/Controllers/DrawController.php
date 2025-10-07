<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Ticket;
use Illuminate\Http\Request;

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
        if ($draw->status !== 'open') {
            return response()->json(['message' => 'Draw already closed or completed'], 400);
        }

        $tickets = Ticket::where('draw_id', $draw->id)->get();
        if ($tickets->isEmpty()) {
            return response()->json(['message' => 'No tickets available'], 404);
        }

        // Each ticket has equal chance -> users with more tickets have higher odds
        $winnerTicket = $tickets->random();

        $draw->update([
            'winner_user_id' => $winnerTicket->user_id,
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => 'Winner selected successfully',
            'winner' => $winnerTicket->user->name,
            'ticket_code' => $winnerTicket->code,
        ]);
    }
}

