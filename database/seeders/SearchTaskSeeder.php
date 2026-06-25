<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SearchTask;

class SearchTaskSeeder extends Seeder
{
    public function run(): void
    {
        // 🌍 Contexto 1: Geopolítica e Grandes Fatos Mundiais (Foco em Poder e Conflitos)
        SearchTask::updateOrCreate(
            ['slug' => 'geopolitica-mundial'],
            [
                'name' => 'Geopolítica e Grandes Fatos Mundiais',
                'keywords' => [
                    // Players e Líderes Globais
                     'Donald Trump', 'Xi Jinping', 'Vladimir Putin', 
                    'Netanyahu', 'Macron', 'Friedrich Merz',
                    // Organizações de Poder e Alianças
                    'G7', 'G20', 'ONU', 'OTAN', 'Brics', 'Kremlin', 'Casa Branca', 'Pequim', 'Congresso dos EUA', 'Parlamento Europeu',
                    // Conflitos e Tensões Atuais
                    'Guerra na Ucrânia', 'Faixa de Gaza', 'Oriente Médio', 'Líbano',
                    'Mar da China Meridional', 'Sanções Internacionais', 'Tensões Diplomáticas'
                ],
                'language' => 'pt',
                'country_code' => 'BR',
                'state_code' => null,
                'context_instructions' => 'Foco total em movimentações de poder político, acordos de paz, declarações de guerra, sanções econômicas severas e cúpulas de líderes mundiais. REJEITE: oscilações diárias de bolsas de valores, relatórios corporativos de empresas, notícias sobre turismo, cultura e fofocas geopolíticas secundárias.',
                'is_active' => true,
            ]
        );

        // 🇧🇷 Contexto 2: Política Nacional (Foco Total nas Eleições 2026)
        SearchTask::updateOrCreate(
            ['slug' => 'politica-nacional'],
            [
                'name' => 'Política Nacional e Sucessão Presidencial',
                'keywords' => [
                    // Eleições 2026 e Sucessão
                    'Eleições 2026', 'Sucessão Presidencial', 'Pesquisa Eleitoral', 
                    'Candidato à Presidência', 'Campanha Eleitoral', 'Debate Político',
                    // Instituições e Poder Central
                    'Planalto', 'Congresso Nacional', 'STF', 'TSE', 'Câmara dos Deputados', 
                    'Senado Federal', 'Reforma Política', 'Partidos Políticos', 
                    // Principais Atores e Líderes
                    'Flávio Bolsonaro', 'Lula', 'Renan Santos', 'Ronaldo Caiado', 'Augusto Cury'
                ],
                'language' => 'pt',
                'country_code' => 'BR',
                'state_code' => null,
                'context_instructions' => 'Foco absoluto nas Eleições de 2026. Articulações, pesquisas, bastidores consolidados e decisões do TSE para as Eleições 2026, além de grandes votações no Congresso e decisões do STF. REJEITE: indicadores econômicos puros (PIB, IPCA), fofocas de deputados do baixo clero e brigas ideológicas que não alterem o cenário eleitoral ou institucional.',
                'is_active' => true,
            ]
        );
    }
}
// <?php

// namespace Database\Seeders;

// use Illuminate\Database\Seeder;
// use App\Models\SearchTask;

// class SearchTaskSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // Exemplo 1: Tecnologia Global (Sem região atrelada)
//         SearchTask::updateOrCreate(
//             ['slug' => 'tech-global'], // O Laravel procura por isso para decidir se cria ou atualiza
//             [
//                 'name' => 'Tecnologia Global',
//                 'keywords' => ['Artificial Intelligence', 'Laravel', 'Linux', 'Docker'],
//                 'language' => 'en',
//                 'country_code' => null,
//                 'state_code' => null,
//                 'is_active' => true,
//             ]
//         );

//         // Exemplo 2: Política Regional (São Paulo)
//         SearchTask::updateOrCreate(
//             ['slug' => 'politica-sp'],
//             [
//                 'name' => 'Política de São Paulo',
//                 'keywords' => ['Governador Tarcísio', 'Prefeitura de SP', 'Alesp'],
//                 'language' => 'pt',
//                 'country_code' => 'BR',
//                 'state_code' => 'SP',
//                 'is_active' => true,
//             ]
//         );
//     }
// }