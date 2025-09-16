<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RevertExpiredVIP extends Command
{
    protected $signature = 'vip:revert';
    protected $description = 'Xoá role VIP đã hết hạn và trả về role thường';

    public function handle(): void
    {
        $this->info("Đang xử lý user hết hạn VIP...");

        $roleVIP = Role::where('name', 'VIP')->first();
        $roleUser = Role::where('name', 'User')->first();

        $expiredUsers = DB::table('model_has_roles')
            ->where('role_id', $roleVIP->id)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredUsers as $user) {
            DB::table('model_has_roles')
                ->where('model_id', $user->model_id)
                ->where('model_type', $user->model_type)
                ->where('role_id', $roleVIP->id)
                ->delete();

            DB::table('model_has_roles')->updateOrInsert([
                'model_id'   => $user->model_id,
                'model_type' => $user->model_type,
                'role_id'    => $roleUser->id,
            ]);
        }

        $this->info("Đã xử lý " . count($expiredUsers) . " user hết hạn VIP.");
    }
}

