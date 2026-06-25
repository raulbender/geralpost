<?php

namespace App\Services;

use App\Models\SearchTask;
use App\Models\ProcessedSource;
use App\Models\StageNews;
use Illuminate\Support\Facades\Http;
use App\Jobs\FilterNewsWithAiJob;

class NewsIngestionService 
{
    /**
     * Processa uma tarefa de busca específica, extrai os links do feed,
     * valida a duplicidade global e move os rascunhos para a área de estágio.
     */
    public function processSearchTask(SearchTask $task): int
    {
        // 💡 Dica Sênior: Garantimos que as keywords sejam tratadas como array.
        // Se você adicionou o protected $casts = ['keywords' => 'array'] no seu Model SearchTask,
        // o Laravel já entrega como array automaticamente.
        $keywordsArray = is_array($task->keywords) ? $task->keywords : json_decode($task->keywords, true);
        
        if (empty($keywordsArray)) {
            return 0;
        }

        // Montamos uma query avançada para o Google News unindo os termos com "OR" e aspas duplas
        // Exemplo resultante: "Governador Tarcísio" OR "Prefeitura de SP" OR "Alesp"
        $queryStr = implode(' OR ', array_map(fn($kw) => '"'.$kw.'"', $keywordsArray));
        $encodedQuery = urlencode($queryStr);

        // Parametrizamos o idioma e o país com base nas diretrizes da tarefa
        $hl = $task->language ?? 'pt-BR';
        $gl = $task->country_code ?? 'BR';
        
        $rssUrl = "https://news.google.com/rss/search?q={$encodedQuery}&hl={$hl}&gl={$gl}&ceid={$gl}:{$hl}";

        $response = Http::get($rssUrl);

        if ($response->failed()) {
            return 0;
        }

        $xml = simplexml_load_string($response->body());
        if (!$xml || !isset($xml->channel->item)) {
            return 0;
        }

        $newItemsCount = 0;

        foreach ($xml->channel->item as $item) {
            // ⏳ FILTRO TEMPORAL SÊNIOR: Captura a data de publicação do RSS
            $pubDateString = (string) $item->pubDate;
            
            if (!empty($pubDateString)) {
                try {
                    $pubDate = \Illuminate\Support\Carbon::parse($pubDateString);
                    
                    // Se a notícia foi publicada ANTES de 24 horas atrás, ignora e pula
                    if ($pubDate->isBefore(now()->subHours(48))) {
                        continue;
                    }
                } catch (\Exception $e) {
                    // Failsafe: Se a data vier corrompida, fazemos o log e deixamos passar por segurança
                    \Illuminate\Support\Facades\Log::warning("Erro ao parsear data do RSS: " . $pubDateString);
                }
            }



            $newsLink = (string) $item->link;
            $newsTitle = (string) $item->title;
            $newsSource = (string) $item->source;

            $urlHash = hash('sha256', $newsLink);

            // 🛑 BARREIRA 1: Verifica duplicidade global histórica
            if (ProcessedSource::where('url_hash', $urlHash)->exists()) {
                continue;
            }

            // Se o link é inédito, crava ele na barreira global histórica imediatamente
            ProcessedSource::create([
                'url' => $newsLink,
                'url_hash' => $urlHash,
                'source' => $newsSource,
            ]);

            // 📥 BARREIRA 2: Insere na Área de Estágio vinculando à tarefa de busca
            StageNews::create([
                'search_task_id' => $task->id, // 👈 O vínculo determinístico que criamos!
                'original_title' => $newsTitle,
                'original_url' => $newsLink,
                'original_hash' => $urlHash,
                'language' => $task->language,
                'country_code' => $task->country_code,
                'state_code' => $task->state_code,
                'status' => 'pending_curation', // Pronta para o robô de IA avaliar
            ]);

            $newItemsCount++;

            // Proteção de escala: processamos no máximo 15 por ciclo por tarefa para não inundar o estágio
            // if ($newItemsCount >= 15) {
            //     break;
            // }
        }

        // 🚀 O Gran Finale: Se novos itens entraram no estágio para ESTA tarefa, 
        // despachamos o Job de IA no Redis passando apenas o ID da tarefa correspondente.
        if ($newItemsCount > 0) {
            FilterNewsWithAiJob::dispatch($task->id);
        }

        return $newItemsCount;
    }
}


// <?php

// namespace App\Services;

// use App\Models\ProcessedSource; // 👈 Nosso model de controle
// use Illuminate\Support\Facades\Http;
// use App\Jobs\FilterNewsWithAiJob; // 👈 O Job que criaremos para a fila do Redis

// class NewsIngestionService 
// {
//     /**
//      * Processa uma tarefa de busca específica, lê o Google News, 
//      * filtra os duplicados e despacha os inéditos para a fila do Redis.
//      */
//     public function processSearchTask(string $keyword): int
//     {
//         // Monta a URL dinamicamente baseado na palavra-chave (ex: 'tecnologia', 'php')
//         $encodedKeyword = urlencode($keyword);
//         $rssUrl = "https://news.google.com/rss/search?q={$encodedKeyword}&hl=pt-BR&gl=BR&ceid=BR:pt-419";

//         $response = Http::get($rssUrl);

//         if ($response->failed()) {
//             return 0; // Retorna zero notícias processadas se o feed falhar
//         }

//         $xml = simplexml_load_string($response->body());
//         $newItemsPayload = [];

//         // Loop por todas as matérias entregues pelo Google News
//         foreach ($xml->channel->item as $item) {
//             $newsLink = (string) $item->link;
//             $newsTitle = (string) $item->title;
//             $newsSource = (string) $item->source;

//             // Gera o hash único da URL para verificação rápida na tabela indexada
//             $urlHash = hash('sha256', $newsLink);

//             // CHECK DE DUPLICIDADE: Se já processamos antes, ignora e pula para o próximo link[cite: 1]
//             if (ProcessedSource::where('url_hash', $urlHash)->exists()) {
//                 continue;
//             }

//             // LINK INÉDITO DETECTADO! Registra na barreira imediatamente[cite: 1]
//             ProcessedSource::create([
//                 'url' => $newsLink,
//                 'url_hash' => $urlHash,
//                 'source' => $newsSource,
//             ]);

//             // Monta o payload minimalista que irá navegar pela nossa fila do Redis
//             $newItemsPayload[] = [
//                 'title' => $newsTitle,
//                 'url' => $newsLink,
//                 'source' => $newsSource,
//             ];

//             // Proteção de escala: pegamos no máximo 10 por ciclo para processamento saudável
//             // if (count($newItemsPayload) >= 10) {
//             //     break;
//             // }
//         }

//         // Se encontramos itens inéditos, despachamos o lote completo para a Fila do Redis
//         if (!empty($newItemsPayload)) {
//             // FilterNewsWithAiJob::dispatch($newItemsPayload); 
//             // 👆 (Descomentaremos essa linha assim que criarmos o Job!)
//         }

//         return count($newItemsPayload); // Retorna quantas notícias novas mandamos para a esteira
//     }
// }