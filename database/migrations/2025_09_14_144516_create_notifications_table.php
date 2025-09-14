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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('story_id');
            $table->unsignedBigInteger('chapter_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->dateTime('created_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('story_id')->references('id')->on('stories')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');

            $table->index('user_id');
            $table->index('story_id');
            $table->index('chapter_id');
            $table->index('created_at');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
