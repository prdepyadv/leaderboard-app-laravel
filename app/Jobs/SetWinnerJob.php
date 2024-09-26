<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use App\Models\Winner;

class SetWinnerJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle()
    {
        $maxPoints = User::max('points');
        \Log::info('Max points: ' . $maxPoints);
        $usersWithMaxPoints = User::where('points', $maxPoints)->get();
        \Log::info('Users with max points: ' . $usersWithMaxPoints->count());
        if ($usersWithMaxPoints->count() === 1) {
            $user = $usersWithMaxPoints->first();
            Winner::create([
                'user_id' => $user->id,
                'points' => $user->points
            ]);
        }
    }
}
