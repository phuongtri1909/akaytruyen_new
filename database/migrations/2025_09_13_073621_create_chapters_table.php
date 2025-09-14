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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('story_id');
            $table->integer('chapter');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->tinyInteger('is_new')->default(0)->comment('0: not new, 1: new');
            $table->bigInteger('views')->default(0);
            $table->timestamp('updated_content_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
