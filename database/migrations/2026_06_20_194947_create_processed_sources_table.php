<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('processed_sources', function (Blueprint $table) {
            $table->id();

            $table->text('url');

            $table->string('url_hash', 64)->unique();

            $table->string('source')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('processed_sources');
    }
};
