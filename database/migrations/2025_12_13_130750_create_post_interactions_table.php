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
    // Tabel Likes untuk Post
    Schema::create('likes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('post_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['user_id', 'post_id']); // Satu user satu like per post
    });

    // Tabel Upvotes untuk Post
    Schema::create('upvotes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('post_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['user_id', 'post_id']);
    });
    
    // Tabel Likes untuk Comment
    Schema::create('comment_likes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['user_id', 'comment_id']);
    });

    // Tambah kolom counter di tabel posts agar query cepat
    Schema::table('posts', function (Blueprint $table) {
        $table->unsignedBigInteger('likes_count')->default(0);
        $table->unsignedBigInteger('upvotes_count')->default(0);
        $table->unsignedBigInteger('comments_count')->default(0);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_interactions');
    }
};
