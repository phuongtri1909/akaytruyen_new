<?php

namespace App\Console\Commands;

use App\Models\Story;
use App\Services\StoryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateStoryImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:migrate-images {--dry-run : Chỉ hiển thị những gì sẽ được migrate mà không thực hiện}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate story images từ public/images/stories sang storage/app/public/stories';

    

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Bắt đầu migrate story images...');

        // Kiểm tra storage link
        if (!$this->checkStorageLink()) {
            $this->error('❌ Storage link chưa được tạo. Chạy: php artisan storage:link');
            return 1;
        }

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 Chế độ DRY RUN - Không thực hiện thay đổi thực tế');
        }

        try {
            $result = $this->migrateOldImages();

            if ($dryRun) {
                $this->info("📊 Kết quả DRY RUN:");
                $this->info("   - Sẽ migrate: {$result['migrated']} images");
                if (!empty($result['errors'])) {
                    $this->warn("   - Lỗi: " . count($result['errors']) . " items");
                    foreach ($result['errors'] as $error) {
                        $this->line("     • {$error}");
                    }
                }
            } else {
                $this->info("✅ Migration hoàn thành!");
                $this->info("   - Đã migrate: {$result['migrated']} images");

                if (!empty($result['errors'])) {
                    $this->warn("   - Lỗi: " . count($result['errors']) . " items");
                    foreach ($result['errors'] as $error) {
                        $this->line("     • {$error}");
                    }
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Lỗi: " . $e->getMessage());
            return 1;
        }
    }

    public function migrateOldImages()
    {
        $stories = Story::whereNotNull('image')->get();
        $migrated = 0;
        $errors = [];

        foreach ($stories as $story) {
            try {
                if (str_starts_with($story->image, '/images/stories/')) {
                    $oldPath = public_path($story->image);

                    if (file_exists($oldPath)) {
                        // Lấy thông tin file
                        $originalName = basename($oldPath);
                        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

                        // Tạo tên file mới
                        $fileName = "story_{$story->id}_{$baseName}.{$extension}";

                        // Copy file sang storage
                        $newPath = 'stories/' . $fileName;
                        Storage::disk('public')->put($newPath, file_get_contents($oldPath));;

                        // Cập nhật database
                        $story->update(['image' => $newPath]);

                        // Xóa file cũ
                        unlink($oldPath);

                        $migrated++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Story ID {$story->id}: " . $e->getMessage();
            }
        }

        return [
            'migrated' => $migrated,
            'errors' => $errors
        ];
    }

    /**
     * Kiểm tra storage link
     */
    protected function checkStorageLink()
    {
        $linkPath = public_path('storage');
        return is_link($linkPath) || file_exists($linkPath);
    }
}
