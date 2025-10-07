<?php

namespace Database\Seeders;

use App\Models\Draw;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrizeDrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users
        $users = User::factory(10)->create();

        // Create draws
        $draws = Draw::factory(3)->create();

        foreach ($draws as $draw) {
            foreach ($users as $user) {
                // Give each user 1–5 tickets for each draw
                $ticketCount = rand(1, 5);
                Ticket::factory($ticketCount)->create([
                    'user_id' => $user->id,
                    'draw_id' => $draw->id,
                ]);
            }
        }
    }
}
