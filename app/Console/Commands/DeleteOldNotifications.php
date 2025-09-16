<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các thông báo và thông báo bị tag cũ hơn 24 giờ và 16 giờ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedNotifications = DB::table('notifications')
            ->where('created_at', '<', Carbon::now()->subDay())
            ->delete();

        $deletedTaggedNotifications = DB::table('user_taggeds')
            ->where('created_at', '<', Carbon::now()->subHours(16))
            ->delete();

        $this->info("Đã xóa $deletedNotifications thông báo cũ hơn 24 giờ.");
        $this->info("Đã xóa $deletedTaggedNotifications thông báo bị tag cũ hơn 16 giờ.");
    }
}

