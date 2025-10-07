<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Draw;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request, Draw $draw)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'count' => 'required|integer|min:1'
        ]);

        $tickets = [];
        for ($i = 0; $i < $validated['count']; $i++) {
            $tickets[] = Ticket::create([
                'user_id' => $validated['user_id'],
                'draw_id' => $draw->id
            ]);
        }

        return response()->json([
            'message' => "{$validated['count']} ticket(s) added successfully",
            'tickets' => $tickets
        ], 201);
    }
}

