<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SearchTask;

class SearchTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Exemplo 1: Tecnologia Global (Sem região atrelada)
        SearchTask::updateOrCreate(
            ['slug' => 'tech-global'], // O Laravel procura por isso para decidir se cria ou atualiza
            [
                'name' => 'Tecnologia Global',
                'keywords' => ['Artificial Intelligence', 'Laravel', 'Linux', 'Docker'],
                'language' => 'en',
                'country_code' => null,
                'state_code' => null,
                'is_active' => true,
            ]
        );

        // Exemplo 2: Política Regional (São Paulo)
        SearchTask::updateOrCreate(
            ['slug' => 'politica-sp'],
            [
                'name' => 'Política de São Paulo',
                'keywords' => ['Governador Tarcísio', 'Prefeitura de SP', 'Alesp'],
                'language' => 'pt',
                'country_code' => 'BR',
                'state_code' => 'SP',
                'is_active' => true,
            ]
        );
    }
}