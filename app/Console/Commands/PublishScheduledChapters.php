<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Chapter;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledChapters extends Command
{
    protected $signature = 'chapters:publish-scheduled';

    protected $description = 'Xuất bản các chương truyện đã được hẹn giờ';

    public function handle()
    {
        $now = Carbon::now();
        $this->info("Bắt đầu kiểm tra chương cần xuất bản vào: " . $now->format('Y-m-d H:i:s'));

        try {
            $chapterCount = Chapter::where('status', Chapter::STATUS_DRAFT)
                ->whereNotNull('scheduled_publish_at')
                ->where('scheduled_publish_at', '<=', $now)
                ->count();

            if ($chapterCount == 0) {
                $this->info('Không có chương nào cần xuất bản vào lúc này.');
                return 0;
            }

            $this->info("Tìm thấy {$chapterCount} chương cần xuất bản.");
            $processedCount = 0;

            Chapter::where('status', Chapter::STATUS_DRAFT)
                ->whereNotNull('scheduled_publish_at')
                ->where('scheduled_publish_at', '<=', $now)
                ->chunkById(100, function ($chapters) use (&$processedCount) {
                    foreach ($chapters as $chapter) {
                        try {
                            $scheduledTime = $chapter->scheduled_publish_at->format('Y-m-d H:i:s');

                            $publishTime = $chapter->scheduled_publish_at;
                            $chapter->status = Chapter::STATUS_PUBLISHED;
                            $chapter->published_at = $publishTime;
                            $chapter->scheduled_publish_at = null;
                            $chapter->created_at = $publishTime;
                            $chapter->save();

                            $users = User::pluck('id');
                            $data = $users->map(function ($userId) use ($chapter) {
                                return [
                                    'user_id' => $userId,
                                    'story_id' => $chapter->story_id,
                                    'chapter_id' => $chapter->id,
                                    'message' => 'Một chapter mới đã được thêm vào truyện: ' . $chapter->story->name . ' - Chương ' . $chapter->chapter,
                                    'created_at' => now(),
                                ];
                            })->toArray();
                            Notification::insert($data);

                            $processedCount++;
                            $this->info("Đã xuất bản chương {$chapter->chapter}: {$chapter->name} của truyện ID={$chapter->story_id}");
                            Log::info("Xuất bản tự động: Chương {$chapter->chapter} '{$chapter->name}' của truyện ID={$chapter->story_id} (theo lịch: {$scheduledTime})");
                        } catch (\Exception $e) {
                            $this->error("Lỗi khi xuất bản chương {$chapter->chapter}");
                            Log::error("Lỗi xuất bản tự động: Chương {$chapter->chapter} - {$e->getMessage()}");
                        }
                    }
                });

            $this->info("Hoàn thành: Đã xuất bản tổng cộng {$processedCount}/{$chapterCount} chương.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Lỗi khi thực hiện xuất bản chương");
            Log::error("Lỗi xuất bản tự động: {$e->getMessage()}");
            return 1;
        }
    }
}
