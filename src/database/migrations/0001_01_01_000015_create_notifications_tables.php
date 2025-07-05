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
            $table->unsignedBigInteger(column: 'user_id');
            $table->unsignedBigInteger(column: 'actor_id')->nullable();
            $table->json('data');
            $table->string('type')->nullable(); // like_post, comment_post, message_received, new_follower, post_published, subscription_canceled, subscription_success
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign(columns: 'user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign(columns: 'actor_id')->references('id')->on('users')->onDelete('cascade');
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
