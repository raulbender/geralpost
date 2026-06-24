<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SearchTask;
use App\Models\StageNews;
use App\Models\Post;

class FilterNewsWithAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O ID da tarefa de busca que este Job vai processar de forma isolada.
     */
    protected $searchTaskId;

    /**
     * Create a new job instance.
     */
    public function __construct($searchTaskId)
    {
        $this->searchTaskId = $searchTaskId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Localiza a tarefa de busca pai
        $task = SearchTask::find($this->searchTaskId);

        if (!$task) {
            return;
        }

        // 2. Procura todas as notícias pendentes na área de estágio para ESTA tarefa
        $pendingNews = StageNews::where('search_task_id', $this->searchTaskId)
                                ->where('status', 'pending_curation')
                                ->get();

        if ($pendingNews->isEmpty()) {
            return;
        }

        // 3. Procura o histórico de posts das últimas 48h para evitar duplicidade de contexto
        $recentTitles = Post::where('country_code', $task->country_code)
                            ->where('state_code', $task->state_code)
                            ->where('created_at', '>=', now()->subHours(48))
                            ->pluck('title')
                            ->toArray();

        // 🪵 Log temporário para ver no terminal do sail worker que o Job funcionou
        \Log::info("⚓ [Job Curadoria] Processando {$pendingNews->count()} notícias para a tarefa: [{$task->name}]");
        
        // =========================================================================
        // PRÓXIMO PASSO: Lógica de conexão com o Gemini Flash Lite (Prompt & Payload)
        // =========================================================================
    }
}