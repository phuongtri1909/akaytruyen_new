<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'Admin',
            'SEO',
            'Content',
            'Mod',
            'User',
            'VIP',
            'VIP PRO',
            'VIP PRO MAX',
            'VIP SIÊU VIỆT',
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Đánh dấu role này là protected (không thể xóa/sửa tên)
            if (!$role->hasAttribute('protected')) {
                // Thêm cột protected nếu chưa có
                if (!\Schema::hasColumn('roles', 'protected')) {
                    \Schema::table('roles', function ($table) {
                        $table->boolean('protected')->default(false)->after('name');
                    });
                }
            }
            
            // Cập nhật role để đánh dấu là protected
            $role->update(['protected' => true]);
            
            $this->command->info("Created protected role: {$roleName}");
        }
    }
}
