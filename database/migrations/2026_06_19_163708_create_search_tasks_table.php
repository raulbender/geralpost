<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('search_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('keywords');

            $table->string('language', 5)->default('pt');
            $table->string('country_code', 2)->nullable();
            $table->string('state_code', 3)->nullable();
            $table->text('context_instructions')->nullable();


            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('search_tasks');
    }
};
