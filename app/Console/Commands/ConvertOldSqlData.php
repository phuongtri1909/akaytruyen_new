<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ConvertOldSqlData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:convert-old-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert data from old SQL file to new database structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Bắt đầu chuyển đổi dữ liệu từ file SQL cũ...');
        
        try {
            // Kiểm tra file SQL có tồn tại không
            $sqlFile = storage_path('app/akaytruyen.sql');
            if (!file_exists($sqlFile)) {
                $this->error("File SQL không tồn tại: $sqlFile");
                $this->info('Vui lòng đặt file SQL cũ vào: storage/app/akaytruyen.sql');
                return 1;
            }
            
            $this->info('📁 Đã tìm thấy file SQL: storage/app/akaytruyen.sql');
            
            // Chạy script chuyển đổi dữ liệu
            $this->info('🔄 Đang chạy script chuyển đổi dữ liệu...');
            
            $scriptPath = database_path('scripts/convert_data_simple.php');
            $output = [];
            $returnCode = 0;
            
            exec("php $scriptPath 2>&1", $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception("Script chuyển đổi dữ liệu thất bại: " . implode("\n", $output));
            }
            
            // Hiển thị output của script
            foreach ($output as $line) {
                $this->line($line);
            }
            
            $this->info('✅ Hoàn thành chuyển đổi dữ liệu!');
            $this->info('📊 Kiểm tra dữ liệu:');
            $this->info('   - Users: ' . \DB::table('users')->count());
            $this->info('   - User Bans: ' . \DB::table('user_bans')->count());
            $this->info('   - Ban IPs: ' . \DB::table('ban_ips')->count());
            $this->info('   - Categories: ' . \DB::table('categories')->count());
            $this->info('   - Stories: ' . \DB::table('stories')->count());
            $this->info('   - Categories Stories: ' . \DB::table('categories_stories')->count());
            $this->info('   - Chapters: ' . \DB::table('chapters')->count());
            $this->info('   - Comments: ' . \DB::table('comments')->count());
            $this->info('   - Comment Reactions: ' . \DB::table('comment_reactions')->count());
            $this->info('   - Comment Edit Histories: ' . \DB::table('comment_edit_histories')->count());
            $this->info('   - Donates: ' . \DB::table('donates')->count());
            $this->info('   - Donations: ' . \DB::table('donations')->count());
            $this->info('   - Live Chats: ' . \DB::table('live_chats')->count());
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Lỗi: ' . $e->getMessage());
            return 1;
        }
    }
}
