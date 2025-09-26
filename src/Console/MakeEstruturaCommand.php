<?php

namespace PauloCoelho\ScaffoldPlus\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MakeEstruturaCommand extends Command
{
    protected string $signature = 'make:estrutura {name : Nome da entidade, ex: Produto ou Cadastro/Produto}';
    protected string $description = 'Gera estrutura completa: Model, Controller, Service/Interface, Repository/Interface, Provider, Requests e Policy com exemplos de uso.';
    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem();
    }

    public function handle(): void
    {
        $raw = str_replace('\\', '/', trim($this->argument('name')));
        $segments = array_filter(explode('/', $raw));

        $entity = Str::studly(array_pop($segments));
        $moduleSegments = array_map(fn($s) => Str::studly($s), $segments);

        $moduleNamespace = $moduleSegments ? implode('\\', $moduleSegments) : '';
        $modulePath = $moduleSegments ? implode('/', $moduleSegments) : '';

        $this->info("Gerando estrutura para: " . ($moduleNamespace ? "$moduleNamespace\\$entity" : $entity));

        $replacements = [
            '{{name}}' => $entity,
            '{{nameCamel}}' => lcfirst($entity),
            '{{Namespace}}' => $moduleNamespace ? '\\' . $moduleNamespace : '',
            '{{table}}' => Str::snake(Str::pluralStudly($entity)),
        ];

        $modelPath = app_path("Models" . ($modulePath ? "/$modulePath/$entity.php" : "/$entity.php"));
        $this->createFromStub('model.stub', $modelPath, $replacements);

        $migrationName = 'create_' . $replacements['{{table}}'] . '_table';
        $this->call('make:migration', ['name' => $migrationName, '--create' => $replacements['{{table}}']]);

        $reqBase = ($modulePath ? $modulePath . '/' : '') . $entity;
        $this->call('make:request', ['name' => "{$reqBase}/Store{$entity}Request"]);
        $this->call('make:request', ['name' => "{$reqBase}/Update{$entity}Request"]);

        $policyName = ($modulePath ? $modulePath . '/' : '') . "{$entity}Policy";
        $modelForPolicy = $moduleNamespace ? "App\\Models\\$moduleNamespace\\$entity" : "App\\Models\\$entity";
        $this->call('make:policy', ['name' => $policyName, '--model' => $modelForPolicy]);

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
    }

    protected function createFromStub(string $stubName, string $destPath, array $replacements): void
    {
        $stubPath = __DIR__ . '/../stubs/' . $stubName;
        if (!$this->files->exists($stubPath)) {
            $this->error("Stub $stubName não encontrado");
            return;
        }

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        if (!is_dir(dirname($destPath))) mkdir(dirname($destPath), 0755, true);
        file_put_contents($destPath, $content);

        $this->info("$stubName criado: $destPath");
        Log::info("$stubName criado: $destPath");
    }
}