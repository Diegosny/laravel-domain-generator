<?php

namespace Domain\DomainGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CreateDomainStructureCommand extends Command
{
    protected $signature = 'make:domain {name : O nome do modelo (ex: User)}';

    protected $description = 'Cria Model, Migration, Controller, Request padrão, Service e Repository para um novo domínio';

    private function getDomainFolder(): string
    {
        return env('APP_DOMAIN_FOLDER', 'Domain');
    }

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $domainFolder = $this->getDomainFolder();

        $this->info("Iniciando a criação da estrutura para o domínio: {$name}");

        $this->createModelAndMigration($name);
        $this->createController($name);
        $this->createRequest($name);
        $this->createService($name);
        $this->createRepository($name);

        $this->newLine();
        $this->info("✨ Estrutura para {$name} criada com sucesso!");
    }

    private function createModelAndMigration(string $name): void
    {
        $this->info("Criando Model e Migration para {$name}...");
        Artisan::call('make:model', [
            'name' => $name,
            '-m' => true,
        ]);
    }

    private function createController(string $name): void
    {
        $controllerName = "{$name}Controller";
        $this->info("Criando Controller {$controllerName}...");
        Artisan::call('make:controller', [
            'name' => $controllerName,
        ]);
    }

    private function createRequest(string $name): void
    {
        $requestName = "{$name}Request";
        $this->info("Criando Request {$requestName} em app/Http/Requests...");
        Artisan::call('make:request', [
            'name' => $requestName,
        ]);
    }

    private function createService(string $name): void
    {
        $domainFolder = $this->getDomainFolder();
        $path = app_path("{$domainFolder}/{$name}/Service");
        $fileName = "{$name}Service.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Service {$fileName} em app/{$domainFolder}/{$name}/Service...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath)) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $template = <<<PHP
<?php

namespace App\\{$domainFolder}\\{$name}\Service;

use App\\{$domainFolder}\\{$name}\Repositories\\{$name}Repository;

class {$name}Service
{
    public function __construct(
        protected {$name}Repository \$repository
    ) {}
}
PHP;

        File::put($fullPath, $template);
    }

    private function createRepository(string $name): void
    {
        $domainFolder = $this->getDomainFolder();
        $path = app_path("{$domainFolder}/{$name}/Repositories");
        $fileName = "{$name}Repository.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Repository {$fileName} em app/{$domainFolder}/{$name}/Repositories...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath)) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $template = <<<PHP
<?php

namespace App\\{$domainFolder}\\{$name}\Repositories;

use App\Models\\{$name};

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