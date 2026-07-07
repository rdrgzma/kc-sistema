<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-activity-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adiciona o rastreio do Spatie Activity Log a todos os Modelos (App\Models)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelsPath = app_path('Models');
        $files = File::allFiles($modelsPath);

        $traitUse = 'use App\Traits\LogsSystemActivity;';
        $traitName = 'LogsSystemActivity';

        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            
            // Se já tem a trait, ignora
            if (str_contains($content, $traitUse) || str_contains($content, 'use ' . $traitName . ';')) {
                $this->info("Modelo {$file->getFilename()} já possui a trait.");
                continue;
            }

            // Ignorar modelos que não sejam classes normais, interfaces ou traits
            if (!preg_match('/class\s+(\w+)\s+extends/is', $content, $matches)) {
                continue;
            }

            // Encontrar o namespace ou declaração de classe para adicionar o import
            if (!str_contains($content, 'use App\Traits\LogsSystemActivity;')) {
                // Adiciona depois do namespace
                $content = preg_replace(
                    '/(namespace\s+App\\\Models;)/is',
                    "$1\n\nuse App\\Traits\\LogsSystemActivity;",
                    $content
                );
            }

            // Encontra a declaração da classe e insere a trait logo abaixo
            $content = preg_replace(
                '/(class\s+\w+\s+extends\s+\w+[^\{]*\{)/is',
                "$1\n    use LogsSystemActivity;\n",
                $content
            );

            file_put_contents($file->getPathname(), $content);
            $this->info("Adicionado Activity Log ao modelo: {$file->getFilename()}");
        }

        $this->info('Todos os modelos foram atualizados com sucesso. Recomendamos correr o php-cs-fixer ou pint para reformatar o código.');
    }
}
