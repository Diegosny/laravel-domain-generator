<?php

namespace Domain\DomainGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CreateDomainStructureCommand extends Command
{
    protected $signature = 'make:domain {name : O nome do modelo (ex: User)} {--force : Forçar a criação mesmo se já existirem arquivos}';

    protected $description = 'Cria Model, Migration, Controller, Request padrão, Service e Repository para um novo domínio';

    private function getDomainFolder(): string
    {
        return env('APP_DOMAIN_FOLDER', 'Domain');
    }

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $force = $this->option('force');

        $this->info("Iniciando a criação da estrutura para o domínio: {$name}");

        $this->createModelAndMigration($name, $force);
        $this->createController($name, $force);
        $this->createRequest($name, $force);
        $this->createService($name, $force);
        $this->createRepository($name, $force);

        $this->newLine();
        $this->info("✨ Estrutura para {$name} criada com sucesso!");
    }

    private function createModelAndMigration(string $name, bool $force = false): void
    {
        $this->info("Criando Model e Migration para {$name}...");

        $params = [
            'name' => $name,
            '-m' => true,
        ];

        // A opção --force evita prompts interativos de confirmação do Laravel
        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call('make:model', $params);
    }

    private function createController(string $name, bool $force = false): void
    {
        $controllerName = "{$name}Controller";
        $this->info("Criando Controller {$controllerName}...");

        $params = ['name' => $controllerName];
        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call('make:controller', $params);
    }

    private function createRequest(string $name, bool $force = false): void
    {
        $requestName = "{$name}Request";
        $this->info("Criando Request {$requestName} em app/Http/Requests...");

        $params = ['name' => $requestName];
        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call('make:request', $params);
    }

    private function createService(string $name, bool $force = false): void
    {
        $domainFolder = $this->getDomainFolder();
        $path = app_path("{$domainFolder}/{$name}/Service");
        $fileName = "{$name}Service.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Service {$fileName} em app/{$domainFolder}/{$name}/Service...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && !$force) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $template = <<<PHP
<?php

namespace App\\{$domainFolder}\\{$name}\\Service;

use App\\{$domainFolder}\\{$name}\\Repositories\\{$name}Repository;

class {$name}Service
{
    public function __construct(
        protected {$name}Repository \$repository
    ) {}
}
PHP;

        File::put($fullPath, $template);
    }

    private function createRepository(string $name, bool $force = false): void
    {
        $domainFolder = $this->getDomainFolder();
        $path = app_path("{$domainFolder}/{$name}/Repositories");
        $fileName = "{$name}Repository.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Repository {$fileName} em app/{$domainFolder}/{$name}/Repositories...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && !$force) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $template = <<<PHP
<?php

namespace App\\{$domainFolder}\\{$name}\\Repositories;

use App\\Models\\{$name};

class {$name}Repository
{
    public function __construct(
        protected {$name} \$model
    ) {}
}
PHP;

        File::put($fullPath, $template);
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }
}