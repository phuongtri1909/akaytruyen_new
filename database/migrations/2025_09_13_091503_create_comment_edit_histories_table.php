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
        Schema::create('comment_edit_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->text('old_content');
            $table->text('new_content');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->timestamp('edited_at');
            $table->string('edit_reason')->nullable();
            $table->timestamps();

            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['comment_id', 'edited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_edit_histories');
    }
};
