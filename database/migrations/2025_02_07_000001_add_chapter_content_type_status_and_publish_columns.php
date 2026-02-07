<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->string('content_type', 10)->default('plain')->after('content')
                ->comment('plain: textarea, rich: ckeditor');
            $table->string('status', 20)->default('published')->after('content_type')
                ->comment('draft: nháp, published: đã xuất bản');
            $table->timestamp('scheduled_publish_at')->nullable()->after('status')
                ->comment('Hẹn giờ đăng - chỉ áp dụng khi status=draft');
            $table->timestamp('published_at')->nullable()->after('scheduled_publish_at')
                ->comment('Thời điểm xuất bản');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['content_type', 'status', 'scheduled_publish_at', 'published_at']);
        });
    }
};
