<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Định nghĩa permissions cho từng role
        $rolePermissions = [
            'Admin' => [
                'dashboard',
                'xem_danh_sach_nguoi_dung',
                'them_nguoi_dung',
                'sua_nguoi_dung',
                'xoa_nguoi_dung',
                'switch_user',
                'xem_danh_sach_vai_tro',
                'them_vai_tro',
                'sua_vai_tro',
                'xoa_vai_tro',
                'xem_danh_sach_quyen',
                'gan_quyen_cho_vai_tro',
                'xem_danh_sach_binh_luan',
                'xoa_binh_luan',
                'ghim_binh_luan',
                'xem_danh_sach_danh_muc',
                'them_danh_muc',
                'sua_danh_muc',
                'xoa_danh_muc',
                'xem_danh_sach_truyen',
                'them_truyen',
                'sua_truyen',
                'xoa_truyen',
                'co_quyen_voi_truyen_cua_minh',
                'xem_danh_sach_chuong',
                'them_chuong',
                'sua_chuong',
                'xoa_chuong',
                'co_quyen_voi_chuong_cua_minh',
                'xem_danh_sach_danh_gia',
                'sua_danh_gia',
                'xoa_danh_gia',
                'xem_danh_sach_thong_tin_donate',
                'them_thong_tin_donate',
                'sua_thong_tin_donate',
                'xoa_thong_tin_donate',
                'xem_danh_sach_thanh_vien_donate',
                'them_thanh_vien_donate',
                'sua_thanh_vien_donate',
                'xoa_thanh_vien_donate',
                'cau_hinh_seo',
                'cau_hinh_setting',
                'download_epub',
                'xem_chuong_truyen_vip',
                'cau_hinh_google',
                'cau_hinh_smtp',
                'quan_ly_admin_khac',
                'xem_log_hoat_dong',
            ],
            'SEO' => [
                'dashboard',
                'cau_hinh_seo',
                'xem_danh_sach_truyen',
                'sua_truyen',
                'xem_danh_sach_danh_muc',
                'sua_danh_muc',
                'xem_log_hoat_dong',
            ],
            'Content' => [
                'dashboard',
                'xem_danh_sach_truyen',
                'them_truyen',
                'sua_truyen',
                'co_quyen_voi_truyen_cua_minh',
                'xem_danh_sach_chuong',
                'them_chuong',
                'sua_chuong',
                'co_quyen_voi_chuong_cua_minh',
                'xem_danh_sach_danh_muc',
                'them_danh_muc',
                'sua_danh_muc',
            ],
            'Mod' => [
                'dashboard',
                'xem_danh_sach_binh_luan',
                'xoa_binh_luan',
                'ghim_binh_luan',
                'xem_danh_sach_nguoi_dung',
                'sua_nguoi_dung',
                'cau_hinh_google',
                'cau_hinh_smtp',
            ],
            'VIP' => [
                'download_epub',
                'xem_chuong_truyen_vip',
            ],
            'VIP PRO' => [
                'download_epub',
                'xem_chuong_truyen_vip',
            ],
            'VIP PRO MAX' => [
                'download_epub',
                'xem_chuong_truyen_vip',
            ],
            'VIP SIÊU VIỆT' => [
                'download_epub',
                'xem_chuong_truyen_vip',
            ],
        ];

      
        $roleEmails = [
            'Admin' => ['conduongbachu69@gmail.com'],
            'SEO' => [], 
            'Content' => [], 
            'Mod' => [], 
            'VIP' => [],
            'VIP PRO' => [],
            'VIP PRO MAX' => [],
            'VIP SIÊU VIỆT' => [],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);
                $role->givePermissionTo($permission);
            }

            if (isset($roleEmails[$roleName]) && !empty($roleEmails[$roleName])) {
                foreach ($roleEmails[$roleName] as $email) {
                    $user = User::where('email', $email)->first();
                    if ($user && !$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                        $this->command->info("Đã gán role '{$roleName}' cho user: {$email}");
                    }
                }
            }
        }

        $userRole = Role::firstOrCreate(['name' => 'User']);
        $allUsers = User::all();
        
        foreach ($allUsers as $user) {
            if (!$user->hasAnyRole(['Admin', 'SEO', 'Content', 'Mod', 'VIP', 'VIP PRO', 'VIP PRO MAX', 'VIP SIÊU VIỆT'])) {
                $user->assignRole('User');
                $this->command->info("Đã gán role 'User' cho user: {$user->email}");
            }
        }

        $this->command->info('Permission seeding completed successfully!');
    }
}
