<?php

namespace App\Services;

use App\Models\SearchTask;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiCurationService
{
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct()
    {
        // 💡 Dica Sênior: Sempre busque as credenciais do arquivo config/services.php
        $this->apiKey = config('services.gemini.key');
        
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key={$this->apiKey}";
    }

    /**
     * Envia o lote de notícias para o Gemini avaliar a relevância com base no contexto.
     */
    public function curateBatch(\Illuminate\Support\Collection $newsItems, SearchTask $task, array $historyTitles): array
    {
        if ($newsItems->isEmpty()) {
            return [];
        }

        // 1. Monta a lista de notícias atuais que precisam de avaliação
        $newsToEvaluate = $newsItems->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->original_title
        ])->toArray();

        // 2. Constrói o prompt técnico injetando as diretrizes da tarefa e o histórico de desduplicação
        $prompt = $this->buildPrompt($task, $newsToEvaluate, $historyTitles);

        // 🛰️ TELEMETRIA: Grava o prompt exato que está subindo para o Google
        Log::info("=== GEMINI OUTBOUND PROMPT (Task: {$task->name}) ===");
        Log::info($prompt);

        // 3. Faz a chamada HTTP forçando o modo de resposta JSON estruturado
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'contents' => [
                'parts' => [
                    ['text' => $prompt]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                // 🛠️ Schema rígido para forçar o Gemini a responder exatamente o que o Laravel espera
                'responseSchema' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'evaluations' => [
                            'type' => 'ARRAY',
                            'items' => [
                                'type' => 'OBJECT',
                                'properties' => [
                                    'id' => ['type' => 'INTEGER'],
                                    'approved' => ['type' => 'BOOLEAN'],
                                    'reason' => ['type' => 'STRING'],
                                ],
                                'required' => ['id', 'approved', 'reason'],
                            ]
                        ]
                    ],
                    'required' => ['evaluations']
                ]
            ]
        ]);

        if ($response->failed()) {
            Log::error("Gemini API Error: " . $response->body());
            throw new \Exception("Falha na comunicação com a API do Gemini.");
        }

        // 4. Decodifica a resposta JSON estruturada enviada pela IA
        $result = $response->json();
        
        // Extrai o texto cru do JSON que o Gemini envelopa na estrutura dele
        $jsonText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        // 🛰️ TELEMETRIA: Grava a resposta exata e a justificativa que a IA devolveu
        Log::info("=== GEMINI INBOUND RESPONSE (Task: {$task->name}) ===");
        Log::info($jsonText);

        $decodedResponse = json_decode($jsonText, true);

        return $decodedResponse['evaluations'] ?? [];
    }

    /**
     * Engenharia de Prompt focada no core business da SearchTask.
     */
    protected function buildPrompt(SearchTask $task, array $newsToEvaluate, array $historyTitles): string
    {
        $jsonNews = json_encode($newsToEvaluate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $jsonHistory = json_encode($historyTitles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $keywordsString = is_array($task->keywords) 
            ? implode(', ', $task->keywords) 
            : (is_string($task->keywords) ? $task->keywords : '');
        
        return <<<PROMPT
Você é o Editor-Chefe Sênior de um portal de notícias tecnológico automatizado.
Sua missão é avaliar um lote de notícias vindas do estágio e decidir quais são altamente RELEVANTES para a nossa persona e quais devem ser DESCARTADAS.

--- DIRETRIZES DA TAREFA DE BUSCA ---
- Nome do Contexto: {$task->name}
- Palavras-chave de Origem: {$keywordsString}
- Notas de Contexto Adicionais: {$task->context_instructions}

--- REGRA DE OURO: EVITAR DUPLICIDADE (ÚLTIMAS 48 HORAS) ---
Abaixo está a lista de títulos que JÁ foram aprovados ou publicados nas últimas 48h. 
Se qualquer notícia do lote atual cobrir EXATAMENTE o mesmo fato/acontecimento de algum título desta lista, você DEVE REJEITAR (approved: false), mesmo que seja de um site diferente, para evitar inundar nosso feed com o mesmo assunto.
Histórico de Títulos já Aprovados:
{$jsonHistory}

--- LOTE DE NOTÍCIAS PARA AVALIAÇÃO (MÁXIMO 15) ---
Avalie cada item pelo ID correspondente:
{$jsonNews}

--- FORMATO DE SAÍDA EXIGIDO ---
Você deve responder estritamente no formato JSON definido pelo Schema, contendo a lista "evaluations". 
Para cada notícia avaliada, retorne o ID, o booleano "approved" (true se for relevante e inédita, false se for irrelevante ou repetida) e uma breve justificativa em "reason".
PROMPT;
    }
}