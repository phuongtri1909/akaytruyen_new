<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\User;

class MigrateUserAvatarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:user-avatars {--dry-run : Show what would be migrated without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate user avatars from uploads to storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting user avatar migration...');

        $users = User::whereNotNull('avatar')
                    ->where(function($query) {
                        $query->where('avatar', 'like', 'uploads/images/avatar/%')
                              ->orWhere('avatar', 'like', '/uploads/images/avatar/%');
                    })
                    ->get();

        if ($users->isEmpty()) {
            $this->info('✅ No avatars to migrate found!');
            return;
        }

        $this->info("Found {$users->count()} avatars to migrate");

        $migrated = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                $oldPath = public_path($user->avatar);
                
                if (!File::exists($oldPath)) {
                    $this->warn("⚠️  File not found: {$user->avatar}");
                    continue;
                }

                // Generate new filename
                $extension = pathinfo($user->avatar, PATHINFO_EXTENSION);
                $newFileName = $user->id . '_' . time() . '.' . $extension;
                $newPath = 'avatars/' . $newFileName;

                if ($this->option('dry-run')) {
                    $this->line("Would migrate: {$user->avatar} -> {$newPath}");
                    $migrated++;
                    continue;
                }

                // Copy to storage
                $fileContent = File::get($oldPath);
                Storage::disk('public')->put($newPath, $fileContent);

                // Update database
                $user->avatar = $newPath;
                $user->save();

                // Delete old file
                File::delete($oldPath);

                $this->info("✅ Migrated: {$user->avatar} -> {$newPath}");
                $migrated++;

            } catch (\Exception $e) {
                $error = "User ID {$user->id}: " . $e->getMessage();
                $errors[] = $error;
                $this->error("❌ {$error}");
            }
        }

        if ($this->option('dry-run')) {
            $this->info("🔍 Dry run completed. Would migrate {$migrated} avatars.");
        } else {
            $this->info("✅ Migration completed!");
            $this->info("📊 Results:");
            $this->info("   - Migrated: {$migrated}");
            $this->info("   - Errors: " . count($errors));
        }

        if (!empty($errors)) {
            $this->warn("⚠️  Errors encountered:");
            foreach ($errors as $error) {
                $this->line("   - {$error}");
            }
        }
    }
} 