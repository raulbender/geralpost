<?php

namespace App\Jobs;

use App\Models\SearchTask;
use App\Models\StageNews;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class FilterNewsWithAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * O número de vezes que o Laravel tentará reexecutar este Job
     * caso o Gemini ou a rede falhem no meio do caminho.
     */
    public $tries = 3;

    /**
     * O número de segundos que o Laravel aguardará antes de tentar o retry.
     */
    public $backoff = 10;

    /**
     * Cria uma nova instância do Job passando o ID da tarefa de busca.
     */
    public function __construct(protected int $searchTaskId)
    {
    }

    /**
     * Executa o motor de curadoria com IA.
     */
    public function handle(): void
    {
        $task = SearchTask::find($this->searchTaskId);
        if (!$task) {
            return;
        }

        $isFirstLoop = true;

        // 🔄 O LOOP DO BILL: Processa blocos de 15 até limpar o estágio desta Task
        while (true) {
            
            // 1. Busca dinamicamente as próximas 15 notícias pendentes desta tarefa
            $pendingNews = StageNews::where('search_task_id', $this->searchTaskId)
                                    ->where('status', 'pending_curation')
                                    ->limit(15) // 👈 Teto seguro para o prompt do Gemini
                                    ->get();

            // 🛑 CRITÉRIO DE PARADA: Se o banco limpou, missão cumprida!
            if ($pendingNews->isEmpty()) {
                break;
            }

            // ⏳ Cortesia náutica: a partir do segundo bloco, dá um respiro de 3 segundos para evitar 429
            if (!$isFirstLoop) {
                sleep(3);
            }
            $isFirstLoop = false;

            // 🎯 CONTEXTO VIVO: Busca os títulos aprovados/publicados nas últimas 48h
            // (inclui o que acabou de ser aprovado no loop anterior!)
            $alreadyApprovedTitles = StageNews::where('search_task_id', $this->searchTaskId)
                                        ->whereIn('status', ['approved_for_edition', 'converted', 'published'])
                                        ->where('updated_at', '>=', now()->subHours(48))
                                        ->pluck('original_title')
                                        ->toArray();

            // 🚀 Envia o bloco atual para curadoria do Gemini
            // Se a API falhar aqui, a Exception quebrará o Job e o Laravel agendará o retry automático.
            // As que já foram salvas como aprovadas/rejeitadas não serão processadas de novo!
            $this->processBatchWithGemini($pendingNews, $task, $alreadyApprovedTitles);
        }
    }

/**
     * Comunica com a API do Gemini e atualiza os status no banco de dados.
     */
    protected function processBatchWithGemini($newsItems, SearchTask $task, array $history): void
    {
        // 💡 O Laravel resolve o serviço automaticamente via Service Container!
        $gemini = app(\App\Services\GeminiCurationService::class);
        
        // Dispara a curadoria externa (Se der Exception, o Job falha e entra o mecanismo de retry)
        $evaluations = $gemini->curateBatch($newsItems, $task, $history);

        // Mapeia o retorno da IA para atualizar o banco na velocidade da luz
        foreach ($evaluations as $eval) {
            $stageItem = $newsItems->firstWhere('id', $eval['id']);
            
            if ($stageItem) {
                $newStatus = $eval['approved'] ? 'approved_for_edition' : 'discarded';
                
                $stageItem->update([
                    'status' => $newStatus,
                    // Dica: você pode salvar o motivo ($eval['reason']) num campo de log ou na própria tabela se quiser debugar depois!
                ]);
            }
        }

        Log::info("TotalPost AI Curation: Bloco de " . $newsItems->count() . " notícias processado e carimbado via Gemini para a Task ID: {$task->id}");
    }

}


// <?php

// namespace App\Jobs;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use App\Models\SearchTask;
// use App\Models\StageNews;
// use App\Models\Post;

// class FilterNewsWithAiJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     /**
//      * O ID da tarefa de busca que este Job vai processar de forma isolada.
//      */
//     protected $searchTaskId;

//     /**
//      * Create a new job instance.
//      */
//     public function __construct($searchTaskId)
//     {
//         $this->searchTaskId = $searchTaskId;
//     }

//     /**
//      * Execute the job.
//      */
//     public function handle(): void
//     {
//         // 1. Localiza a tarefa de busca pai
//         $task = SearchTask::find($this->searchTaskId);

//         if (!$task) {
//             return;
//         }

//         // 2. Procura todas as notícias pendentes na área de estágio para ESTA tarefa
//         $pendingNews = StageNews::where('search_task_id', $this->searchTaskId)
//                                 ->where('status', 'pending_curation')
//                                 ->get();

//         if ($pendingNews->isEmpty()) {
//             return;
//         }

//         // 3. Procura o histórico de posts das últimas 48h para evitar duplicidade de contexto
//         $recentTitles = Post::where('country_code', $task->country_code)
//                             ->where('state_code', $task->state_code)
//                             ->where('created_at', '>=', now()->subHours(48))
//                             ->pluck('title')
//                             ->toArray();

//         // 🪵 Log temporário para ver no terminal do sail worker que o Job funcionou
//         \Log::info("⚓ [Job Curadoria] Processando {$pendingNews->count()} notícias para a tarefa: [{$task->name}]");
        
//         // =========================================================================
//         // PRÓXIMO PASSO: Lógica de conexão com o Gemini Flash Lite (Prompt & Payload)
//         // =========================================================================
//     }
// }