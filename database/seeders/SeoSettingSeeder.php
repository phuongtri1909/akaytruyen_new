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
        ];

        foreach ($seoSettings as $setting) {
            SeoSetting::updateOrCreate(
                ['page_key' => $setting['page_key']],
                $setting
            );
        }
    }
}
