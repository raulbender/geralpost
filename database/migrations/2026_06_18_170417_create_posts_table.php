<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('content');

            $table->string('type')->default('news'); // news, article, post, etc.
            $table->string('language', 5)->default('pt'); // pt, en, es
            $table->string('country_code', 2)->nullable()->index();
            $table->string('state_code', 3)->nullable()->index();

            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('pending'); // pending, published, failed, suspended, etc.
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('posts');
    }
};
