<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\AiContentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchAndProcessAiNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AiContentService $aiService): void
    {
        $rawPayload = $aiService->generatePayloadFromTrends();

        $reviewedData = $aiService->reviewAndTranslate($rawPayload);

        Post::create([
            'user_id' => 1, // No futuro, isso pode ser dinâmico ou um usuário específico para IA
            'title' => $reviewedData['title_pt'], // Por enquanto salvando o título em PT
            'content' => $reviewedData['content_pt'], // Por enquanto salvando o conteúdo em PT
            'status' => $reviewedData['status'], // Salva como 'revised'
        ]);
    }
}