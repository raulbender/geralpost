<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StageNews extends Model
{
    use HasFactory;

    // 🛡️ Liberando os portões para inserção em lote com segurança
    protected $fillable = [
        'search_task_id', // 👈 A chave que faltava!
        'original_title',
        'original_url',
        'original_hash',
        'language',
        'country_code',
        'state_code',
        'draft_title',
        'draft_content',
        'status',
    ];

    /**
     * 🚢 Elo de Ligação: Cada registro do estágio pertence a uma tarefa de busca.
     */
    public function searchTask(): BelongsTo
    {
        return $this->belongsTo(SearchTask::class);
    }
}