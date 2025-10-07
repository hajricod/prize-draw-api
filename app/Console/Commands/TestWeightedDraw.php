<?php

namespace App\Console\Commands;

use App\Http\Controllers\DrawController;
use App\Models\Draw;
use Illuminate\Console\Command;

class TestWeightedDraw extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-weighted-draw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pick a winner until a user with 1 ticket is selected';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $draw = Draw::find(1);
        $controller = new DrawController();
        $tries = 0;
        $targetTickets = 1; // exact number of tickets

        do {
            $tries++;
            $response = $controller->pickWinner($draw);
            $winnerData = $response->getData()->winner;

            $this->info("Try $tries: User has {$winnerData->tickets_in_draw} tickets");

            // Reset draw to open for the next iteration
            $draw->update(['winner_user_id' => null, 'status' => 'open']);

        } while ($winnerData->tickets_in_draw != $targetTickets);

        $this->info("User with {$targetTickets} tickets won after $tries tries!");
        $this->info(print_r($winnerData, true));
    }
}
