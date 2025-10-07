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
        Schema::create('saved_chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('story_id');
            $table->unsignedBigInteger('chapter_id');
            $table->integer('scroll_position')->default(0); // Vị trí cuộn (pixel)
            $table->decimal('read_progress', 5, 2)->default(0.00); // Tiến độ đọc (%)
            $table->timestamp('last_read_at')->nullable(); // Thời gian đọc cuối
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('story_id')->references('id')->on('stories')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
            
            $table->unique(['user_id', 'story_id']); // Mỗi user chỉ lưu 1 chương mới nhất của mỗi truyện
            $table->index(['user_id', 'last_read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_chapters');
    }
};
