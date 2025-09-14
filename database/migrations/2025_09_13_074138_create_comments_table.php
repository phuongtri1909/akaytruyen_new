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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chapter_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->longText('comment');
            $table->unsignedBigInteger('reply_id')->nullable();
            $table->integer('level')->default(0);
            $table->tinyInteger('is_pinned')->default(0)->comment('0: not pinned, 1: pinned');
            $table->timestamp('pinned_at')->nullable();
            $table->boolean('is_edited')->default(0)->comment('0: not edited, 1: edited');
            $table->timestamp('edited_at')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->integer('edit_count')->default(0);
            $table->timestamps();

            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reply_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
