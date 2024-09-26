<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;

class ResetUserScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-user-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the points of all users to 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::where('points', '>', 0)->update(['points' => 0]);
        $this->info('All user scores have been reset to 0');
    }
}
