<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SeoSetting;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoSettings = [
            [
                'page_key' => 'home',
                'title' => 'Trang chủ - AkayTruyen',
                'description' => 'Đọc truyện online, truyện hay. Akay Truyện luôn tổng hợp và cập nhật các chương truyện một cách nhanh nhất.',
                'keywords' => 'doc truyen, doc truyen online, truyen hay, truyen chu, akay truyen',
                'thumbnail' => 'images/logo/Logoakay.png',
                'is_active' => true
            ],
            [
                'page_key' => 'contact',
                'title' => 'Liên hệ - AkayTruyen',
                'description' => 'Liên hệ với Akay Truyện qua các kênh chính thức: YouTube, Facebook, Discord. Hỗ trợ và phản hồi nhanh chóng.',
                'keywords' => 'lien he, contact, akay truyen, youtube, facebook, discord, ho tro',
                'thumbnail' => 'images/logo/Logoakay.png',
                'is_active' => true
            ],
            [
                'page_key' => 'privacy-policy',
                'title' => 'Chính sách bảo mật - AkayTruyen',
                'description' => 'Chính sách bảo mật và quyền riêng tư của Akay Truyện. Tìm hiểu cách chúng tôi thu thập, sử dụng và bảo vệ thông tin cá nhân của bạn.',
                'keywords' => 'chinh sach bao mat, privacy policy, quyen rieng tu, bao mat thong tin, akay truyen',
                'thumbnail' => 'images/logo/Logoakay.png',
                'is_active' => true
            ],
            [
                'page_key' => 'terms',
                'title' => 'Điều khoản sử dụng - AkayTruyen',
                'description' => 'Điều khoản sử dụng và thỏa thuận người dùng của Akay Truyện. Quyền và nghĩa vụ khi sử dụng dịch vụ.',
                'keywords' => 'dieu khoan su dung, terms of use, thoa thuan nguoi dung, quyen va nghia vu, akay truyen',
                'thumbnail' => 'images/logo/Logoakay.png',
                'is_active' => true
            ],
            [
                'page_key' => 'content-rules',
                'title' => 'Quy định về nội dung - AkayTruyen',
                'description' => 'Quy định về nội dung và các quy tắc đăng bài trên Akay Truyện. Nội dung bị cấm và giới hạn độ tuổi.',
                'keywords' => 'quy dinh noi dung, content rules, noi dung bi cam, quy tac dang bai, akay truyen',
                'thumbnail' => 'images/logo/Logoakay.png',
                'is_active' => true
            ],
            [
                'page_key' => 'confidental',
                'title' => 'Thỏa thuận quyền riêng tư - AkayTruyen',
                'description' => 'Thỏa thuận quyền riêng tư và bảo mật thông tin cá nhân của Akay Truyện. Mục đích xử lý dữ liệu và quyền của người dùng.',
                'keywords' => 'thoa thuan quyen rieng tu, privacy agreement, bao mat thong tin, xu ly du lieu, akay truyen',
                'thumbnail' => 'images/logo/Logoakay.png',
                'is_active' => true
            ],
        ];

        foreach ($seoSettings as $setting) {
            SeoSetting::updateOrCreate(
                ['page_key' => $setting['page_key']],
                $setting
            );
        }
    }
}
