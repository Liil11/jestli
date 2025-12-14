<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            // Siapa yang melakukan follow (Follower)
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            
            // Siapa yang di-follow (Following)
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');

            // Mencegah duplikat (User A tidak bisa follow User B dua kali)
            $table->primary(['follower_id', 'following_id']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};