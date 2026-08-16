<?php

namespace Domain\DomainGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use RuntimeException;

class CreateDomainStructureCommand extends Command
{
    protected $signature = 'make:domain
                            {name : O nome do domínio (ex: User)}
                            {--force : Forçar criação de arquivos existentes}';

    protected $description = 'Cria a estrutura base de um domínio.';

    private function getDomainFolder(): string
    {
        return config(
            'domain-generator.domain_folder',
            env('APP_DOMAIN_FOLDER', 'Domain')
        );
    }

    private function getStubsPath(): string
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.'Stubs';
    }

    public function handle(): int
    {
        $name = ucfirst($this->argument('name'));
        $force = (bool) $this->option('force');

        $this->info("Criando domínio {$name}...");
        $this->newLine();

        $this->createModelAndMigration($name, $force);
        $this->createController($name, $force);
        $this->createRequest($name, $force);
        $this->createDto($name, $force);
        $this->createService($name, $force);
        $this->createRepository($name, $force);

        $this->newLine();
        $this->info("✨ Domínio {$name} criado com sucesso!");

        return self::SUCCESS;
    }

    private function createModelAndMigration(string $name, bool $force): void
    {
        $params = [
            'name' => $name,
            '-m' => true,
        ];

        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call('make:model', $params);

        $this->outputCommandOutput();
    }

    private function createController(string $name, bool $force): void
    {
        $this->generateFromStub(
            'controller.stub',
            app_path('Http/Controllers'),
            "{$name}Controller.php",
            [
                'name' => $name,
                'controller' => "{$name}Controller",
                'domainFolder' => $this->getDomainFolder(),
            ],
            $force,
            'Controller'
        );
    }

    private function createRequest(string $name, bool $force): void
    {
        $params = [
            'name' => "{$name}Request",
        ];

        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call('make:request', $params);

        $this->outputCommandOutput();
    }

    private function createDto(string $name, bool $force): void
    {
        $this->generateFromStub(
            'dto.stub',
            app_path("{$this->getDomainFolder()}/{$name}/DTO"),
            "{$name}DTO.php",
            [
                'name' => $name,
                'domainFolder' => $this->getDomainFolder(),
            ],
            $force,
            'DTO'
        );
    }

    private function createService(string $name, bool $force): void
    {
        $this->generateFromStub(
            'service.stub',
            app_path("{$this->getDomainFolder()}/{$name}/Service"),
            "{$name}Service.php",
            [
                'name' => $name,
                'domainFolder' => $this->getDomainFolder(),
            ],
            $force,
            'Service'
        );
    }

    private function createRepository(string $name, bool $force): void
    {
        $this->generateFromStub(
            'repository.stub',
            app_path("{$this->getDomainFolder()}/{$name}/Repositories"),
            "{$name}Repository.php",
            [
                'name' => $name,
                'domainFolder' => $this->getDomainFolder(),
            ],
            $force,
            'Repository'
        );
    }

    private function generateFromStub(
        string $stub,
        string $path,
        string $fileName,
        array $variables,
        bool $force,
        string $label
    ): void {

        $fullPath = $path.DIRECTORY_SEPARATOR.$fileName;

        $this->info("Criando {$label} {$fileName}...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && ! $force) {
            $this->warn("{$fileName} já existe.");

            return;
        }

        File::put(
            $fullPath,
            $this->replaceStubVariables(
                $this->loadStub($stub),
                $variables
            )
        );
    }

    private function loadStub(string $stub): string
    {
        $path = $this->getStubsPath()
            .DIRECTORY_SEPARATOR
            .strtolower($stub);

        if (! File::exists($path)) {
            throw new RuntimeException(
                "Stub não encontrado: {$path}"
            );
        }

        return File::get($path);
    }

    private function replaceStubVariables(
        string $stub,
        array $variables
    ): string {

        foreach ($variables as $key => $value) {

            $stub = str_replace(
                [
                    "{{ {$key} }}",
                    "{{{$key}}}",
                    '$'.strtoupper($key),
                    'Dummy'.ucfirst($key),
                ],
                $value,
                $stub
            );
        }

        return $stub;
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (! File::isDirectory($path)) {
            File::makeDirectory(
                $path,
                0755,
                true
            );
        }
    }

    private function outputCommandOutput(): void
    {
        $output = trim(Artisan::output());

        if ($output !== '') {
            $this->line($output);
        }
    }
}
