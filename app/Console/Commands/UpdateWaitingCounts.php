<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;

class UpdateWaitingCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waiting:update-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update waiting counts for all restaurants based on today\'s waiting users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating waiting counts for all restaurants...');

        $restaurants = Restaurant::all();
        $updated = 0;

        foreach ($restaurants as $restaurant) {
            $oldCount = $restaurant->current_waiting_count;
            $restaurant->updateWaitingCount();
            $newCount = $restaurant->fresh()->current_waiting_count;

            if ($oldCount !== $newCount) {
                $this->line("Restaurant '{$restaurant->name}': {$oldCount} → {$newCount}");
                $updated++;
            }
        }

        $this->info("Updated {$updated} restaurants out of {$restaurants->count()} total.");
        
        return Command::SUCCESS;
    }
}
