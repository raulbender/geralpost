<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\StageNews;

class SearchTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'keywords',
        'language',
        'country_code',
        'state_code',
        'is_active',
    ];

    // 🧠 Lembra que conversamos no bloco anterior? 
    // Esse cast garante que o Laravel transforme o JSON do banco em array PHP automaticamente!
    protected $casts = [
        'keywords' => 'array',
    ];

    /**
     * 🚢 Uma tarefa possui muitas notícias na área de estágio.
     */
    public function stageNews(): HasMany
    {
        return $this->hasMany(StageNews::class);
    }
}

// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

// class SearchTask extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'name',
//         'keywords',
//         'is_active',
//     ];

//     /**
//      * O Toque Sênior: Mapeia os tipos de dados nativos.
//      * Isso faz o Laravel converter o JSON do banco em um array PHP automaticamente!
//      */
//     protected $casts = [
//         'keywords' => 'array',
//         'is_active' => 'boolean',
//     ];
// }

