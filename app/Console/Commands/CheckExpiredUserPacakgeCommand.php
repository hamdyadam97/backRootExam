<?php

namespace App\Console\Commands;

use App\Models\Userpackges;
use Illuminate\Console\Command;

class CheckExpiredUserPacakgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-user-package-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Userpackges::query()
            ->whereDate('end_date', '<', now()->toDateString())
            ->update([
                'subscription_status' => 0
            ]);
    }
}
