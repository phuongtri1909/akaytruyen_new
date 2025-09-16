<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Khởi tạo Laravel application
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

class ConvertDataSimple
{
    private $tempDbName;
    
    public function __construct()
    {
        $this->tempDbName = 'temp_old_sql_' . time();
    }

    public function run()
    {
        echo "🚀 Bắt đầu chuyển đổi dữ liệu từ file SQL cũ...\n";
        
        try {
            // Tạo database tạm thời
            $this->createTempDatabase();
            
            // Import file SQL cũ
            $this->importOldSqlFile();
            
            // Chuyển đổi dữ liệu
            $this->convertData();
            
            // Dọn dẹp
            $this->cleanup();
            
            echo "✅ Hoàn thành chuyển đổi dữ liệu!\n";
            
        } catch (Exception $e) {
            echo "❌ Lỗi: " . $e->getMessage() . "\n";
            $this->cleanup();
            throw $e;
        }
    }

    private function createTempDatabase()
    {
        echo "📁 Tạo database tạm thời...\n";
        
        $config = config('database.connections.mysql');
        $connection = new \PDO(
            "mysql:host={$config['host']}", 
            $config['username'], 
            $config['password']
        );
        
        $connection->exec("DROP DATABASE IF EXISTS {$this->tempDbName}");
        $connection->exec("CREATE DATABASE {$this->tempDbName}");
    }

    private function importOldSqlFile()
    {
        echo "📥 Import file SQL cũ...\n";
        
        $sqlFile = storage_path('app/akaytruyen.sql');
        
        if (!file_exists($sqlFile)) {
            throw new Exception("File SQL cũ không tồn tại: {$sqlFile}");
        }
        
        $config = config('database.connections.mysql');
        $command = sprintf(
            'mysql -h%s -u%s -p%s %s < %s',
            $config['host'],
            $config['username'],
            $config['password'],
            $this->tempDbName,
            escapeshellarg($sqlFile)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Không thể import file SQL cũ");
        }
    }

    private function convertData()
    {
        // Cấu hình connection tạm thời
        config(['database.connections.temp' => [
            'driver' => 'mysql',
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => $this->tempDbName,
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);

        // Chuyển đổi users
        $this->convertUsers();
        
        // Chuyển đổi user_bans
        $this->convertUserBans();
        
        // Chuyển đổi ban_ips
        $this->convertBanIps();
        
        // Chuyển đổi categories
        $this->convertCategories();
        
        // Chuyển đổi stories
        $this->convertStories();
        
        // Chuyển đổi categories_stories
        $this->convertCategoriesStories();
        
        // Chuyển đổi chapters
        $this->convertChapters();
        
        // Chuyển đổi comments
        $this->convertComments();
        
        // Chuyển đổi comment_reactions
        $this->convertCommentReactions();
        
        // Chuyển đổi comment_edit_histories
        $this->convertCommentEditHistories();
        
        // Chuyển đổi donates
        $this->convertDonates();
        
        // Chuyển đổi donations
        $this->convertDonations();
        
        // Chuyển đổi livechat
        $this->convertLivechat();
    }

    private function convertUsers()
    {
        echo "👥 Chuyển đổi dữ liệu users...\n";
        
        $oldUsers = DB::connection('temp')->table('users')->get();
        echo "   📊 Tìm thấy " . $oldUsers->count() . " users trong database tạm thời\n";
        
        $count = 0;
        
        foreach ($oldUsers as $user) {
            $existingUser = DB::table('users')->where('id', $user->id)->first();
            
            if (!$existingUser) {
                DB::table('users')->insert([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password ?: '',
                    'status' => $user->status,
                    'ip_address' => $user->ip_address,
                    'last_login_time' => $user->last_login_time,
                    'avatar' => $user->avatar,
                    'email_verified_at' => $user->email_verified_at,
                    'google_id' => $user->google_id,
                    'donate_amount' => $user->donate_amount ?: 0.00,
                    'active' => $user->active,
                    'key_active' => $user->key_active,
                    'key_reset_password' => $user->key_reset_password,
                    'reset_password_at' => $user->reset_password_at,
                    'rating' => $user->rating,
                    'created_by' => $user->created_by,
                    'remember_token' => $user->remember_token,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count users\n";
    }

    private function convertUserBans()
    {
        echo "🚫 Chuyển đổi dữ liệu user_bans...\n";
        
        $usersWithBans = DB::connection('temp')
            ->table('users')
            ->where(function($query) {
                $query->where('ban_login', 1)
                      ->orWhere('ban_comment', 1)
                      ->orWhere('ban_rate', 1)
                      ->orWhere('ban_read', 1);
            })
            ->get();
        
        $count = 0;
        foreach ($usersWithBans as $user) {
            $existingBan = DB::table('user_bans')->where('user_id', $user->id)->first();
            
            if (!$existingBan) {
                DB::table('user_bans')->insert([
                    'user_id' => $user->id,
                    'login' => (bool) $user->ban_login,
                    'comment' => (bool) $user->ban_comment,
                    'rate' => (bool) $user->ban_rate,
                    'read' => (bool) $user->ban_read,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count user_bans\n";
    }

    private function convertBanIps()
    {
        echo "🌐 Chuyển đổi dữ liệu ban_ips...\n";
        
        $hasBannedIps = DB::connection('temp')
            ->select("SHOW TABLES LIKE 'banned_ips'");
        
        if (empty($hasBannedIps)) {
            echo "   ⚠️ Không tìm thấy bảng banned_ips\n";
            return;
        }
        
        $bannedIps = DB::connection('temp')->table('banned_ips')->get();
        
        $count = 0;
        foreach ($bannedIps as $bannedIp) {
            $existingIp = DB::table('ban_ips')
                ->where('ip_address', $bannedIp->ip_address)
                ->first();
            
            if (!$existingIp) {
                DB::table('ban_ips')->insert([
                    'ip_address' => $bannedIp->ip_address,
                    'user_id' => $bannedIp->user_id,
                    'created_at' => $bannedIp->created_at,
                    'updated_at' => $bannedIp->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count ban_ips\n";
    }

    private function convertCategories()
    {
        echo "📂 Chuyển đổi dữ liệu categories...\n";
        
        $oldCategories = DB::connection('temp')->table('categories')->get();
        $count = 0;
        
        foreach ($oldCategories as $category) {
            $existingCategory = DB::table('categories')->where('id', $category->id)->first();
            
            if (!$existingCategory) {
                DB::table('categories')->insert([
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'desc' => $category->desc,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count categories\n";
    }

    private function convertStories()
    {
        echo "📚 Chuyển đổi dữ liệu stories...\n";
        
        $oldStories = DB::connection('temp')->table('stories')->get();
        $count = 0;
        
        foreach ($oldStories as $story) {
            $existingStory = DB::table('stories')->where('id', $story->id)->first();
            
            if (!$existingStory) {
                // Kiểm tra author_id có tồn tại trong users không
                $authorId = null;
                if ($story->author_id && $story->author_id > 0) {
                    $authorExists = DB::table('users')->where('id', $story->author_id)->exists();
                    $authorId = $authorExists ? $story->author_id : null;
                }
                
                DB::table('stories')->insert([
                    'id' => $story->id,
                    'image' => $story->image ?: '',
                    'slug' => $story->slug ?: '',
                    'name' => $story->name ?: '',
                    'desc' => $story->desc,
                    'author_id' => $authorId,
                    'status' => $story->status ?: 0,
                    'is_full' => $story->is_full ?: 0,
                    'is_new' => $story->is_new ?: 0,
                    'is_hot' => $story->is_hot ?: 0,
                    'created_at' => $story->created_at,
                    'updated_at' => $story->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count stories\n";
    }

    private function convertCategoriesStories()
    {
        echo "🔗 Chuyển đổi dữ liệu categories_stories...\n";
        
        $oldCategoriesStories = DB::connection('temp')->table('categorie_storie')->get();
        $count = 0;
        
        foreach ($oldCategoriesStories as $categoryStory) {
            $existingRecord = DB::table('categories_stories')
                ->where('category_id', $categoryStory->categorie_id)
                ->where('story_id', $categoryStory->storie_id)
                ->first();
            
            if (!$existingRecord) {
                DB::table('categories_stories')->insert([
                    'category_id' => $categoryStory->categorie_id,
                    'story_id' => $categoryStory->storie_id,
                    'created_at' => $categoryStory->created_at,
                    'updated_at' => $categoryStory->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count categories_stories\n";
    }

    private function convertChapters()
    {
        echo "📖 Chuyển đổi dữ liệu chapters...\n";
        
        $oldChapters = DB::connection('temp')->table('chapters')->get();
        $count = 0;
        
        foreach ($oldChapters as $chapter) {
            $existingChapter = DB::table('chapters')->where('id', $chapter->id)->first();
            
            if (!$existingChapter) {
                // Kiểm tra story_id có tồn tại trong stories không
                $storyId = $chapter->story_id ?: 0;
                if ($storyId > 0) {
                    $storyExists = DB::table('stories')->where('id', $storyId)->exists();
                    if (!$storyExists) {
                        $storyId = 0;
                    }
                }
                
                DB::table('chapters')->insert([
                    'id' => $chapter->id,
                    'story_id' => $storyId,
                    'chapter' => $chapter->chapter ?: 0,
                    'name' => $chapter->name ?: '',
                    'slug' => $chapter->slug ?: '',
                    'content' => $chapter->content ?: '',
                    'is_new' => $chapter->is_new ?: 0,
                    'views' => $chapter->views ?: 0,
                    'updated_content_at' => null,
                    'created_at' => $chapter->created_at,
                    'updated_at' => $chapter->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count chapters\n";
    }

    private function convertComments()
    {
        echo "💬 Chuyển đổi dữ liệu comments...\n";
        
        $oldComments = DB::connection('temp')->table('comments')->get();
        echo "   📊 Tìm thấy " . $oldComments->count() . " comments trong database tạm thời\n";
        
        $count = 0;
        
        foreach ($oldComments as $comment) {
            $existingComment = DB::table('comments')->where('id', $comment->id)->first();
            
            if (!$existingComment) {
                // Xử lý chapter_id - trong database cũ đây là slug, cần tìm id tương ứng
                $chapterId = null;
                if ($comment->chapter_id) {
                    // Tìm chapter theo slug trong database mới
                    $chapter = DB::table('chapters')->where('slug', $comment->chapter_id)->first();
                    if ($chapter) {
                        $chapterId = $chapter->id;
                    } else {
                        // Nếu không tìm thấy theo slug, thử tìm theo ID (trường hợp hiếm)
                        if (is_numeric($comment->chapter_id)) {
                            $chapter = DB::table('chapters')->where('id', $comment->chapter_id)->first();
                            if ($chapter) {
                                $chapterId = $chapter->id;
                            }
                        }
                    }
                }
                
                // Xử lý user_id - chỉ chấp nhận số nguyên và kiểm tra tồn tại
                $userId = null;
                if (is_numeric($comment->user_id) && $comment->user_id > 0) {
                    $userExists = DB::table('users')->where('id', $comment->user_id)->exists();
                    if ($userExists) {
                        $userId = (int)$comment->user_id;
                    }
                }
                
                // Xử lý reply_id - chỉ chấp nhận số nguyên và kiểm tra tồn tại
                $replyId = null;
                if (is_numeric($comment->reply_id) && $comment->reply_id > 0) {
                    $replyExists = DB::table('comments')->where('id', $comment->reply_id)->exists();
                    if ($replyExists) {
                        $replyId = (int)$comment->reply_id;
                    }
                }
                
                // Xử lý edited_by - kiểm tra xem cột có tồn tại không
                $editedBy = null;
                if (property_exists($comment, 'edited_by') && is_numeric($comment->edited_by) && $comment->edited_by > 0) {
                    $editedByExists = DB::table('users')->where('id', $comment->edited_by)->exists();
                    if ($editedByExists) {
                        $editedBy = (int)$comment->edited_by;
                    }
                }
                
                // Chỉ insert nếu có chapter_id hợp lệ (vì cột này không được null)
                if ($chapterId) {
                    try {
                        DB::table('comments')->insert([
                            'id' => $comment->id,
                            'chapter_id' => $chapterId,
                            'user_id' => $userId,
                            'comment' => $comment->comment ?: '',
                            'reply_id' => $replyId,
                            'level' => property_exists($comment, 'level') ? ($comment->level ?: 0) : 0,
                            'is_pinned' => property_exists($comment, 'is_pinned') ? ($comment->is_pinned ?: 0) : 0,
                            'pinned_at' => property_exists($comment, 'pinned_at') ? $comment->pinned_at : null,
                            'is_edited' => property_exists($comment, 'is_edited') ? ($comment->is_edited ?: 0) : 0,
                            'edited_at' => property_exists($comment, 'edited_at') ? $comment->edited_at : null,
                            'edited_by' => $editedBy,
                            'edit_count' => property_exists($comment, 'edit_count') ? ($comment->edit_count ?: 0) : 0,
                            'created_at' => $comment->created_at,
                            'updated_at' => $comment->updated_at,
                        ]);
                        $count++;
                    } catch (\Exception $e) {
                        echo "   ⚠️ Bỏ qua comment ID {$comment->id}: " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "   ⚠️ Không tìm thấy chapter với slug: {$comment->chapter_id} cho comment ID {$comment->id}\n";
                }
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count comments\n";
    }

    private function convertCommentReactions()
    {
        echo "👍 Chuyển đổi dữ liệu comment_reactions...\n";
        
        // Kiểm tra xem bảng có tồn tại không
        $hasTable = DB::connection('temp')
            ->select("SHOW TABLES LIKE 'comment_reactions'");
        
        if (empty($hasTable)) {
            echo "   ⚠️ Không tìm thấy bảng comment_reactions\n";
            return;
        }
        
        $oldReactions = DB::connection('temp')->table('comment_reactions')->get();
        $count = 0;
        
        foreach ($oldReactions as $reaction) {
            $existingReaction = DB::table('comment_reactions')
                ->where('comment_id', $reaction->comment_id)
                ->where('user_id', $reaction->user_id)
                ->where('type', $reaction->type)
                ->first();
            
            if (!$existingReaction) {
                // Xử lý comment_id - kiểm tra tồn tại trong bảng comments
                $commentId = null;
                if (is_numeric($reaction->comment_id) && $reaction->comment_id > 0) {
                    $commentExists = DB::table('comments')->where('id', $reaction->comment_id)->exists();
                    $commentId = $commentExists ? (int)$reaction->comment_id : null;
                }
                
                // Xử lý user_id - kiểm tra tồn tại trong bảng users
                $userId = null;
                if (is_numeric($reaction->user_id) && $reaction->user_id > 0) {
                    $userExists = DB::table('users')->where('id', $reaction->user_id)->exists();
                    $userId = $userExists ? (int)$reaction->user_id : null;
                }
                
                if ($commentId && $userId) {
                    DB::table('comment_reactions')->insert([
                        'id' => $reaction->id,
                        'comment_id' => $commentId,
                        'user_id' => $userId,
                        'type' => $reaction->type ?: '',
                        'created_at' => $reaction->created_at,
                        'updated_at' => $reaction->updated_at,
                    ]);
                    $count++;
                }
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count comment_reactions\n";
    }

    private function convertCommentEditHistories()
    {
        echo "📝 Chuyển đổi dữ liệu comment_edit_histories...\n";
        
        // Kiểm tra xem bảng có tồn tại không
        $hasTable = DB::connection('temp')
            ->select("SHOW TABLES LIKE 'comment_edit_histories'");
        
        if (empty($hasTable)) {
            echo "   ⚠️ Không tìm thấy bảng comment_edit_histories\n";
            return;
        }
        
        $oldHistories = DB::connection('temp')->table('comment_edit_histories')->get();
        $count = 0;
        
        foreach ($oldHistories as $history) {
            $existingHistory = DB::table('comment_edit_histories')->where('id', $history->id)->first();
            
            if (!$existingHistory) {
                // Xử lý comment_id - kiểm tra tồn tại trong bảng comments
                $commentId = null;
                if (is_numeric($history->comment_id) && $history->comment_id > 0) {
                    $commentExists = DB::table('comments')->where('id', $history->comment_id)->exists();
                    $commentId = $commentExists ? (int)$history->comment_id : null;
                }
                
                // Xử lý edited_by - kiểm tra xem cột có tồn tại không
                $editedBy = null;
                if (property_exists($history, 'edited_by') && is_numeric($history->edited_by) && $history->edited_by > 0) {
                    $editedByExists = DB::table('users')->where('id', $history->edited_by)->exists();
                    $editedBy = $editedByExists ? (int)$history->edited_by : null;
                }
                
                if ($commentId) {
                    DB::table('comment_edit_histories')->insert([
                        'id' => $history->id,
                        'comment_id' => $commentId,
                        'old_content' => $history->old_content,
                        'new_content' => $history->new_content,
                        'edited_by' => $editedBy,
                        'edited_at' => $history->edited_at,
                        'edit_reason' => $history->edit_reason,
                        'created_at' => $history->created_at,
                        'updated_at' => $history->updated_at,
                    ]);
                    $count++;
                }
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count comment_edit_histories\n";
    }

    private function convertDonates()
    {
        echo "💰 Chuyển đổi dữ liệu donates...\n";
        
        // Kiểm tra xem bảng có tồn tại không
        $hasTable = DB::connection('temp')
            ->select("SHOW TABLES LIKE 'donates'");
        
        if (empty($hasTable)) {
            echo "   ⚠️ Không tìm thấy bảng donates\n";
            return;
        }
        
        $oldDonates = DB::connection('temp')->table('donates')->get();
        echo "   📊 Tìm thấy " . $oldDonates->count() . " donates trong database tạm thời\n";
        
        $count = 0;
        
        foreach ($oldDonates as $donate) {
            $existingDonate = DB::table('donates')->where('id', $donate->id)->first();
            
            if (!$existingDonate) {
                // Xử lý story_id - kiểm tra xem cột có tồn tại không
                $storyId = null;
                if (property_exists($donate, 'story_id') && is_numeric($donate->story_id) && $donate->story_id > 0) {
                    $storyExists = DB::table('stories')->where('id', $donate->story_id)->exists();
                    $storyId = $storyExists ? (int)$donate->story_id : null;
                }
                
                DB::table('donates')->insert([
                    'id' => $donate->id,
                    'story_id' => $storyId,
                    'bank_name' => $donate->bank_name ?: '',
                    'donate_info' => $donate->donate_info,
                    'image' => $donate->image ?: '',
                    'created_at' => $donate->created_at,
                    'updated_at' => $donate->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count donates\n";
    }

    private function convertDonations()
    {
        echo "🎁 Chuyển đổi dữ liệu donations...\n";
        
        // Kiểm tra xem bảng có tồn tại không
        $hasTable = DB::connection('temp')
            ->select("SHOW TABLES LIKE 'donations'");
        
        if (empty($hasTable)) {
            echo "   ⚠️ Không tìm thấy bảng donations\n";
            return;
        }
        
        $oldDonations = DB::connection('temp')->table('donations')->get();
        echo "   📊 Tìm thấy " . $oldDonations->count() . " donations trong database tạm thời\n";
        
        $count = 0;
        
        foreach ($oldDonations as $donation) {
            $existingDonation = DB::table('donations')->where('id', $donation->id)->first();
            
            if (!$existingDonation) {
                // Database cũ không có cột story_id, set null
                $storyId = null;
                
                DB::table('donations')->insert([
                    'id' => $donation->id,
                    'story_id' => $storyId,
                    'name' => $donation->name ?: '',
                    'amount' => is_numeric($donation->amount) ? (float)$donation->amount : 0.00,
                    'donated_at' => $donation->donated_at,
                    'created_at' => $donation->created_at,
                    'updated_at' => $donation->updated_at,
                ]);
                $count++;
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count donations\n";
    }

    private function convertLivechat()
    {
        echo "💬 Chuyển đổi dữ liệu livechat...\n";
        
        $oldLivechats = DB::connection('temp')->table('livechat')->get();
        echo "   📊 Tìm thấy " . $oldLivechats->count() . " livechat trong database tạm thời\n";
        
        $count = 0;
        
        foreach ($oldLivechats as $livechat) {
            $existingLivechat = DB::table('live_chats')->where('id', $livechat->id)->first();
            
            if (!$existingLivechat) {
                // Kiểm tra user_id tồn tại
                $userId = null;
                if (is_numeric($livechat->user_id) && $livechat->user_id > 0) {
                    $userExists = DB::table('users')->where('id', $livechat->user_id)->exists();
                    $userId = $userExists ? (int)$livechat->user_id : null;
                }
                
                // Kiểm tra parent_id tồn tại (nếu có)
                $parentId = null;
                if ($livechat->parent_id && is_numeric($livechat->parent_id) && $livechat->parent_id > 0) {
                    $parentExists = DB::table('live_chats')->where('id', $livechat->parent_id)->exists();
                    $parentId = $parentExists ? (int)$livechat->parent_id : null;
                }
                
                try {
                    DB::table('live_chats')->insert([
                        'id' => $livechat->id,
                        'user_id' => $userId,
                        'content' => $livechat->content ?: '',
                        'parent_id' => $parentId,
                        'pinned' => $livechat->pinned ?: false,
                        'created_at' => $livechat->created_at,
                        'updated_at' => $livechat->updated_at,
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    echo "   ⚠️ Bỏ qua livechat ID {$livechat->id}: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "   ✅ Đã chuyển đổi $count livechat\n";
    }

    private function cleanup()
    {
        echo "🧹 Dọn dẹp...\n";
        
        try {
            $config = config('database.connections.mysql');
            $connection = new \PDO(
                "mysql:host={$config['host']}", 
                $config['username'], 
                $config['password']
            );
            $connection->exec("DROP DATABASE IF EXISTS {$this->tempDbName}");
        } catch (Exception $e) {
            echo "⚠️ Không thể xóa database tạm thời: " . $e->getMessage() . "\n";
        }
    }
}

// Chạy script
if (php_sapi_name() === 'cli') {
    $converter = new ConvertDataSimple();
    $converter->run();
}
