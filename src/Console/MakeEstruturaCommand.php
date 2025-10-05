<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MakeEstruturaCommand extends Command
{
    protected $signature = 'make:estrutura {name : Nome da entidade, ex: Produto ou Cadastro/Produto}';
    protected $description = 'Gera estrutura completa: Model, Migration, Requests, Controller, Service/Interface, Repository/Interface, Provider e Policy com exemplos de uso e DI.';
    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem();
    }

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $raw = str_replace('\\', '/', trim($this->argument('name')));
        $segments = array_filter(explode('/', $raw));

        $entity = Str::studly(array_pop($segments));
        $moduleSegments = array_map(fn($s) => Str::studly($s), $segments);

        $moduleNamespace = $moduleSegments ? implode('\\', $moduleSegments) : '';
        $modulePath = $moduleSegments ? implode('/', $moduleSegments) : '';

        $this->info("Gerando estrutura para: " . ($moduleNamespace ? "$moduleNamespace\\$entity" : $entity));
        Log::info("Início da criação da estrutura para: " . ($moduleNamespace ? "$moduleNamespace\\$entity" : $entity));

        $replacements = [
            '{{name}}' => $entity,
            '{{nameCamel}}' => lcfirst($entity),
            '{{Namespace}}' => $moduleNamespace ? '\\' . $moduleNamespace : '',
            '{{table}}' => Str::snake(Str::pluralStudly($entity)),
        ];

        // --- 1) Model
        $modelPath = app_path("Models" . ($modulePath ? "/$modulePath/$entity.php" : "/$entity.php"));
        $this->createFromStub('model.stub', $modelPath, $replacements);

        // --- 1.1) Factory
        $this->call('make:factory', [
            'name' => "{$entity}Factory",
            '--model' => ($moduleNamespace ? "App\\Models\\{$moduleNamespace}\\$entity" : "App\\Models\\$entity"),
        ]);

        // --- 1.2) Seeder
        $this->call('make:seeder', [
            'name' => "{$entity}Seeder",
        ]);

        // --- 2) Migration
        $migrationName = 'create_' . $replacements['{{table}}'] . '_table';
        $this->info("Criando migration: $migrationName");
        Log::info("Criando migration: $migrationName");
        $this->call('make:migration', ['name' => $migrationName, '--create' => $replacements['{{table}}']]);
        $this->info("Migration criada: $migrationName");
        Log::info("Migration criada: $migrationName");

        // --- 3) Requests
        $reqBase = ($modulePath ? $modulePath . '/' : '') . $entity;
        foreach (["Store{$entity}Request", "Update{$entity}Request"] as $req) {
            $this->info("Criando Request: $req");
            Log::info("Criando Request: $req");
            $this->call('make:request', ['name' => "{$reqBase}/$req"]);
            $this->info("Request criado: $req");
            Log::info("Request criado: $req");
        }

        // --- 4) Policy
        $policyName = ($modulePath ? $modulePath . '/' : '') . "{$entity}Policy";
        $modelForPolicy = $moduleNamespace ? "App\\Models\\$moduleNamespace\\$entity" : "App\\Models\\$entity";
        $this->info("Criando Policy: $policyName");
        Log::info("Criando Policy: $policyName");
        $this->call('make:policy', ['name' => $policyName, '--model' => $modelForPolicy]);
        $this->info("Policy criada: $policyName");
        Log::info("Policy criada: $policyName");

        // --- 5) Skeletons
        $filesToGenerate = [
            'controller.stub' => app_path("Http/Controllers" . ($modulePath ? "/$modulePath/$entity/$entity" : "/$entity/$entity") . "Controller.php"),
            'service_interface.stub' => app_path("Services" . ($modulePath ? "/$modulePath/$entity" : "/$entity") . "/{$entity}ServiceInterface.php"),
            'service.stub' => app_path("Services" . ($modulePath ? "/$modulePath/$entity" : "/$entity") . "/{$entity}Service.php"),
            'repository_interface.stub' => app_path("Repositories" . ($modulePath ? "/$modulePath/$entity" : "/$entity") . "/{$entity}RepositoryInterface.php"),
            'repository.stub' => app_path("Repositories" . ($modulePath ? "/$modulePath/$entity" : "/$entity") . "/{$entity}Repository.php"),
            'provider.stub' => app_path("Providers" . ($modulePath ? "/$modulePath/$entity" : "/$entity") . "/{$entity}ServiceProvider.php"),
        ];

        foreach ($filesToGenerate as $stub => $dest) {
            $this->createFromStub($stub, $dest, $replacements);
        }

        $this->info("✅ Estrutura completa gerada com sucesso.");
        Log::info("Estrutura completa gerada para $entity");
    }

    /**
     * @throws FileNotFoundException
     */
    protected function createFromStub(string $stubName, string $destPath, array $replacements): void
    {
        $stubPath = base_path("stubs/estrutura/$stubName");
        if (!$this->files->exists($stubPath)) {
            $this->error("Stub $stubName não encontrado em stubs/estrutura");
            Log::error("Stub $stubName não encontrado em stubs/estrutura");
            return;
        }

        $this->info("Criando $stubName em $destPath...");
        Log::info("Criando $stubName em $destPath...");

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        if (!is_dir(dirname($destPath))) mkdir(dirname($destPath), 0755, true);
        file_put_contents($destPath, $content);

        $this->info("$stubName criado: $destPath");
        Log::info("$stubName criado: $destPath");
    }
}
