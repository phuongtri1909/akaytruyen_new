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
            'Admin' => ['conduongbachu69@gmail.com', 'nang2025@gmail.com', 'nguyenphuochau12t2@gmail.com', 'chicuong.longan@gmail.com', 'khaicybers@gmail.com'],
            'SEO' => ['khai692005@gmail.com'],
            'Content' => ['damn231120@gmail.com', 'kiendayne132@gmail.com', 'quangserious2004@gmail.com', 'datsilver2004@gmail.com'],
            'Mod' => ['fanclubcdbc@gmail.com', 'fanclubcdbc@gmail.com', 'bmtriet2808@gmail.com'],
            'VIP' => ['phamthang.95er@gmail.com', 'nhatlinh1890@gmail.com', 'xuanquynh2903@gmail.com', '0978823255chien@gmail.com', 'tunghuynh1131@gmail.com', 'quocnghia003@gmail.com', 'lemanhkt92@gmail.com', 'ngthanhtink16@gmail.com', 'private68@gmail.com','nguyennamctnd@gmail.com','handoi2233@gmail.com','trucnhanchu@gmail.com','sonnt13@gmail.com'],
            'VIP PRO' => ['doanthienbao227@gmail.com', 'pdtrung0609@gmail.com', 'anhemhohoc@gmail.com', 'khangmap1507@gmail.com', 'anh1298674@gmail.com', 'zorba.movie@gmail.com', 'phieuluucongtu@gmail.com', 'ngocnguyen2410@yahoo.com', 'mnhao.ulaw@gmail.com', 'phanquang2009@gmail.com', 'vietanh29gi@gmail.com', 'hieunls@gmail.com', 'duongminh1691@gmail.com', 'khanhnguyen2687@gmail.com', 'ntl0918044428@gmail.com', 'vantam1412@gmail.com', 'vituebinha6k98htk@gmail.com', 'liem.hoanghieu@gmail.com', 'nothing2a2@gmail.com', 'dat25759@gmail.com', 'truongphg11@gmail.com', 'huuducd9@gmail.com', 'phamduyhung.ftu@gmail.com', 'hrcloudta@gmail.com', 'thanglehuy@gmail.com', '0916897058t@gmail.com', 'nguyenhung060904@gmail.com', 'vboy1368@gmail.com', 'dangatung@gmail.com', 'huangmingyong@gmail.com', 'quangmanh16091988@gmail.com', 'anhdaynayyyyy@gmail.com', 'thaiphuongduy54@gmail.com', 'hoangbobe37@gmail.com', 'taosanh@gmail.com', 'ngkiet2003@gmail.com', 'tuboytq2609@gmail.com', 'namhai6405@gmail.com', 'trungtamluyenthicasio@gmail.com', 'leviethuynh95@gmail.com', 'hoangminh3305@gmail.com', 'luantrandinh@gmail.com', 'vantinh108@gmail.com', 'truongan1309@gmail.com', 'truongx017@gmail.com', 'cuonghatinh8@gmail.com', 'buiminhtam2410@gmail.com', 'nguyendtn20@gmail.com', 'phamduyhunghcm2003@gmail.com', 'v19001560@gmail.com', 'huyld97@gmail.com', 'hoangductung2202@gmail.com', 'hoafi000@gmail.com', 'takoxocua@gmail.com', 'electabuzz26290@gmail.com', 'Phanvantammta52@gmail.com', 'vickyzeung@gmail.com', 'haidangbdm@gmail.com', 'hoangnamcdpro@gmail.com', 'trandoanlong04@gmail.com', 'daophuonga5k50@gmail.com', 'nguyentankhai.qn@gmail.com', 'dat.lt905@gmail.com', '89.beings.denture@icloud.com', 'lehungbon@gmail.com', 'acefeedthanthanh@gmail.com', 'gijoe.legolas@gmail.com', 'vugia33@gmail.com', 'hahuuluong1997@gmail.com', 'smileboycpr81@gmail.com', 'nhanmath97@gmail.com', 'dbg.vanchan@gmail.com', 'songoku.register@gmail.com', 'chuvandung789@gmail.com', 'fcfockem996@gmail.com', 'vophibang1995@gmail.com', 'nonhero86@gmail.com', 'phamvuthanh@yahoo.com', 'vanlinh141216@gmail.com', 'binhpc66bp@gmail.com', 'nguyennhungftu@yahoo.com', 'Bsduandq@gmail.com', 'kienhqhg1991@gmail.com', 'nvquan216@gmail.com', 'vugiaphat20232018@gmail.com', 'thetri1975@gmail.com', 'phieudu8105@gmail.com', 'thienthuy495@gmail.com', 'buivanhanh1996hb@gmail.com', 'giatan2510@gmail.com', 'baohuanho@gmail.com', 'dangnguyen1596@gmail.com', 'namphongnguyen8@gmail.com', 'madoanhiep31122001@gmail.com', 'doancanhvietanh1995@gmali.com', 'phammanhcuong1905@icloud.com', 'nguyenduycuong.2581999@gmail.com', 'thehai2410@gmail.com', 'tranchaulinh1996@gmail.com', 'hungtran2315@gmail.com', 'thhueabcd@gmail.com', 'congtrieu21287819@gmail.com', 'ngocthanh2352@gmail.com', 'nguyenhoangson1503@gmail.com', 'tao.vovan@gmail.com', 'tranthiquyenbacgiang@gmail.com', 'ngothanhphuc99@gmail.com', 'haibigty@gmail.com', 'chaudotruongson@gmail.com', 'duongmsb@gmail.com', 'toiday01@gmail.com', 'nguyenvietthang72@gmail.com', 'ninhdt00935@gmail.com', 'linthipi@gmail.com', 'test.courier586@passinbox.com', 'mymail@nguyenhuyhuan.xyz', 'vncfhoang@gmail.com', '992ngocnghia@gmail.com', 'pkathmoshopkh@gmail.com', 'duclongchu03@gmail.com', 'binhtran.vozer@gmail.com', 'dhson2810@gmail.com'],
            'VIP PRO MAX' => ['Lockhotp@gmail.com', 'binhle.07101995@gmail.com', 'nguyentuanphong29112012@gmail.com', 'linhoxyz@gmail.com', 'khanhxthuy@gmail.com', 'cuongdes@yahoo.com', 'nguyenhoanganhpvoil1@gmail.com', 'giahung234016@gmail.com', 'sonhongduong@gmail.com', 'taiga2407@gmail.com', 'phongqlkd625@gmail.com', 'tanlh0303@gmail.com', 'hoalai86@yahoo.com', 'vuongquoc@gmail.com', 'trinhtuan261190@gmail.com', 'hoanggiatrang.qtv123@gmail.com', 'ptd292@gmail.com', 'harypotter76214@gmail.com', 'caothanhtoanhg9572@gmail.com', 'khiemhoang2303@gmail.com', 'pacita.vn@gmail.com', 'duluontaynguyen@gmail.com', 'rontran81@gmail.com', 'luatsuquangtrung6979@gmail.com', 'rodianbamarap@gmail.com', 'bs.dungnt@gmail.com', 'hoaidong@tantienphat.vn', 'trancongbinh04@gmail.com', 'ericnguyenvn@gmail.com', 'venusloc@yahoo.com'],
            'VIP SIÊU VIỆT' => ['thanh.son.m.tran@gmail.com','hoangdinh2125@gmail.com','buinhatlinh708@gmail.com','thql.bp@gmail.com','anthonytn79@gmail.com','Akaytruyen@gmail.com'],
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
