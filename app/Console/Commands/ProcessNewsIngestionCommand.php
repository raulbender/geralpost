<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsIngestionService;
use App\Models\SearchTask;

class ProcessNewsIngestionCommand extends Command
{
    /**
     * O nome e a assinatura do comando que você digita no terminal.
     */
    protected $signature = 'news:ingest';

    /**
     * A descrição do comando.
     */
    protected $description = 'Scan active search tasks, fetch top trends from Google News and queue unseen items.';

    /**
     * Executa a rota de ingestão de notícias varrendo todas as tarefas ativas.
     */
    public function handle(NewsIngestionService $ingestionService): int
    {
        $this->info('⚓ [Radar] Iniciando a varredura global de ingestão...');

        // 🚢 Passo 1: Busca todas as tarefas de busca que estão ativas no sistema
        $activeTasks = SearchTask::where('is_active', true)->get();

        if ($activeTasks->isEmpty()) {
            $this->warn('⚠️ Nenhuma tarefa de busca ativa encontrada no banco de dados.');
            return Command::SUCCESS;
        }

        $totalGlobalQueued = 0;

        // 🚢 Passo 2: Faz o loop por cada uma delas de forma independente
        foreach ($activeTasks as $task) {
            $this->comment("🛰️ Escaneando o feed para a tarefa: [{$task->name}]...");
            
            // Passamos o Model completo da tarefa para o Service cuidar
            $queuedForTask = $ingestionService->processSearchTask($task);

            if ($queuedForTask > 0) {
                $this->info("✅ Sucesso! {$queuedForTask} novas notícias inseridas no estágio para [{$task->name}].");
                $totalGlobalQueued += $queuedForTask;
            } else {
                $this->line("🔍 Sem novidades para [{$task->name}]. Radar limpo.");
            }
        }

        $this->info("⚓ [Radar] Varredura finalizada. Total de novos itens no estágio: {$totalGlobalQueued}");

        return Command::SUCCESS;
    }
}


// <?php

// namespace App\Console\Commands;

// use Illuminate\Console\Attributes\Description;
// use Illuminate\Console\Attributes\Signature;
// use Illuminate\Console\Command;
// use App\Services\NewsIngestionService; 

// #[Signature('news:ingest')] // 👈 Mudamos aqui!
// #[Description('Scan active search tasks, fetch top trends from Google News and queue unseen items.')] // 👈 Mudamos aqui!
// class ProcessNewsIngestionCommand extends Command
// {
//     /**
//      * O nome e a assinatura do comando que você digita no terminal.
//      * @var string
//      */
//     protected $signature = 'news:ingest';

//     /**
//      * A descrição do comando (aparece quando você roda sail artisan list).
//      * @var string
//      */
//     protected $description = 'Scan active search tasks, fetch top trends from Google News and queue unseen items.';

//     /**
//      * O Laravel resolve automaticamente as dependências passadas no construtor ou no método handle().
//      */
//     public function handle(NewsIngestionService $ingestionService): int
//     {
//         $this->info('⚓ [Radar] Iniciando a ingestão de notícias...');

//         // 💡 Por enquanto, como estamos testando e temos apenas uma tarefa de busca conceitual,
//         // vamos passar a palavra-chave fixada como 'tecnologia'. 
//         // Logo mais, faremos um loop que varre a sua tabela `search_tasks` real do banco!
//         $keyword = 'tecnologia'; 

//         $this->comment("Buscando tendências para o termo: [{$keyword}]");
        
//         $totalQueued = $ingestionService->processSearchTask($keyword);

//         if ($totalQueued > 0) {
//             $this->info("✅ Sucesso! {$totalQueued} novas notícias foram enviadas para a fila do Redis.");
//         } else {
//             $this->line('🔍 Nenhuma novidade no feed nas últimas checagens. Radar limpo.');
//         }

//         return Command::SUCCESS;
//     }
// }