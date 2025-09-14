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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->tinyInteger('status')->default(0)->nullable()->comment('0: inactive, 1: active');
            $table->tinyInteger('is_full')->default(0)->nullable();
            $table->tinyInteger('is_new')->default(0)->nullable();
            $table->tinyInteger('is_hot')->default(0)->nullable();

            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
