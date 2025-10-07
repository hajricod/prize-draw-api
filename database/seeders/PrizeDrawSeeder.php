<?php

namespace Database\Seeders;

use App\Models\Draw;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrizeDrawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ Create users
        $users = User::factory(10000)->create();

        // 2️⃣ Create draws
        $draws = Draw::factory(1)->create();

        foreach ($draws as $draw) {
            $tickets = [];

            foreach ($users as $user) {
                // Random number of tickets per user (1–500)
                $ticketCount = rand(1, 50);

                for ($i = 0; $i < $ticketCount; $i++) {
                    $tickets[] = [
                        'user_id' => $user->id,
                        'draw_id' => $draw->id,
                        'code' => Str::upper(Str::random(10)), // unique code
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // 3️⃣ Insert tickets in chunks to avoid memory issues
            foreach (array_chunk($tickets, 10000) as $chunk) {
                DB::table('tickets')->insert($chunk);
            }
        }
    }
}
