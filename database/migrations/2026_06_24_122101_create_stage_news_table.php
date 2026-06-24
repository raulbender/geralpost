<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_news', function (Blueprint $table) {
            $table->id();

            $table->foreignId('search_task_id')->constrained()->cascadeOnDelete();
            
            // Dados brutos vindos do Command/RSS
            $table->text('original_title');
            $table->text('original_url');
            $table->string('original_hash')->unique(); // Evita que o Command insira o mesmo link duas vezes
            
            // Metadados que a tarefa injeta na esteira
            $table->string('language', 5)->default('pt');
            $table->string('country_code', 2)->nullable();
            $table->string('state_code', 3)->nullable();
            
            // Campos onde as IAs (Job 2 e Job 3) vão salvar os rascunhos refinados
            $table->string('draft_title')->nullable();
            $table->text('draft_content')->nullable();
            
            // Máquina de Estados da Automação
            $table->string('status')->default('pending_curation'); // pending_curation, approved_for_edition, drafted, ready_for_approval, discarded, converted
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_news');
    }
};