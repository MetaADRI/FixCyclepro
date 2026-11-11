<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PurgeDeletedUserAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:purge-deleted {--days=30 : Grace period length in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete user accounts whose self-deletion grace period has expired';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = (int)$this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $users = User::where('user_delete', 1)
            ->whereNotNull('deletion_scheduled_at')
            ->where('deletion_scheduled_at', '<=', $cutoff)
            ->get();

        $purged = 0;
        foreach ($users as $user) {
            try {
                $user->delete();
                $purged++;
            } catch (\Exception $e) {
                $this->error("Failed to purge user [{$user->id}]: " . $e->getMessage());
            }
        }

        $this->info("Purged {$purged} user account(s) past their {$days} day deletion grace period.");
        return 0;
    }
}
