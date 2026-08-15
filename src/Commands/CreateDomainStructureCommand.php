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
                            {--force : Forçar a criação mesmo se já existirem arquivos}';

    protected $description = 'Cria Model, Migration, Controller, Request, DTO, Service e Repository para um novo domínio';

    /**
     * Retorna a pasta base dos domínios.
     */
    private function getDomainFolder(): string
    {
        return config(
            'domain-generator.domain_folder',
            env('APP_DOMAIN_FOLDER', 'Domain')
        );
    }

    /**
     * Retorna o diretório dos stubs.
     */
    private function getStubsPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Stubs';
    }

    /**
     * Executa o comando.
     */
    public function handle(): int
    {
        $name = ucfirst($this->argument('name'));
        $force = (bool) $this->option('force');

        $this->info("Iniciando a criação da estrutura para o domínio: {$name}");
        $this->newLine();

        $this->createModelAndMigration($name, $force);
        $this->createController($name, $force);
        $this->createRequest($name, $force);
        $this->createDto($name, $force);
        $this->createService($name, $force);
        $this->createRepository($name, $force);

        $this->newLine();
        $this->info("✨ Estrutura para {$name} criada com sucesso!");

        return self::SUCCESS;
    }

    /**
     * Cria Model e Migration utilizando os generators nativos do Laravel.
     */
    private function createModelAndMigration(string $name, bool $force = false): void
    {
        $this->info("Criando Model e Migration para {$name}...");

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

    /**
     * Cria Controller.
     */
    private function createController(string $name, bool $force = false): void
    {
        $this->generateFromStub(
            stub: 'controller.stub',
            path: app_path('Http/Controllers'),
            fileName: "{$name}Controller.php",
            variables: [
                'name' => $name,
                'controller' => "{$name}Controller",
                'domainFolder' => $this->getDomainFolder(),
            ],
            force: $force,
            label: 'Controller'
        );
    }

    /**
     * Cria Request.
     */
    private function createRequest(string $name, bool $force = false): void
    {
        $requestName = "{$name}Request";

        $this->info("Criando Request {$requestName}...");

        $params = [
            'name' => $requestName,
        ];

        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call('make:request', $params);

        $this->outputCommandOutput();
    }

    /**
     * Cria DTO.
     */
    private function createDto(string $name, bool $force = false): void
    {
        $this->generateFromStub(
            stub: 'dto.stub',
            path: app_path("{$this->getDomainFolder()}/{$name}/DTO"),
            fileName: "{$name}DTO.php",
            variables: [
                'name' => $name,
                'domainFolder' => $this->getDomainFolder(),
            ],
            force: $force,
            label: 'DTO'
        );
    }

    /**
     * Cria Service.
     */
    private function createService(string $name, bool $force = false): void
    {
        $this->generateFromStub(
            stub: 'service.stub',
            path: app_path("{$this->getDomainFolder()}/{$name}/Service"),
            fileName: "{$name}Service.php",
            variables: [
                'name' => $name,
                'domainFolder' => $this->getDomainFolder(),
            ],
            force: $force,
            label: 'Service'
        );
    }

    /**
     * Cria Repository.
     */
    private function createRepository(string $name, bool $force = false): void
    {
        $this->generateFromStub(
            stub: 'repository.stub',
            path: app_path("{$this->getDomainFolder()}/{$name}/Repositories"),
            fileName: "{$name}Repository.php",
            variables: [
                'name' => $name,
                'domainFolder' => $this->getDomainFolder(),
            ],
            force: $force,
            label: 'Repository'
        );
    }

    /**
     * Gera um arquivo a partir de um stub.
     */
    private function generateFromStub(
        string $stub,
        string $path,
        string $fileName,
        array $variables,
        bool $force,
        string $label
    ): void {
        $fullPath = $path . DIRECTORY_SEPARATOR . $fileName;

        $this->info("Criando {$label} {$fileName}...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && ! $force) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");

            return;
        }

        $content = $this->replaceStubVariables(
            $this->loadStub($stub),
            $variables
        );

        File::put($fullPath, $content);
    }

    /**
     * Carrega um stub da biblioteca.
     */
    private function loadStub(string $stub): string
    {
        $stub = strtolower($stub);

        $path = $this->getStubsPath() . DIRECTORY_SEPARATOR . $stub;

        if (! File::exists($path)) {
            throw new RuntimeException(
                sprintf(
                    'Stub [%s] não encontrado em [%s].',
                    $stub,
                    $this->getStubsPath()
                )
            );
        }

        return File::get($path);
    }

    /**
     * Substitui as variáveis existentes no stub.
     */
    private function replaceStubVariables(string $stub, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $stub = str_replace(
                [
                    "{{ {$key} }}",
                    "{{{$key}}}",
                    '$' . strtoupper($key),
                    'Dummy' . ucfirst($key),
                ],
                $value,
                $stub
            );
        }

        return $stub;
    }

    /**
     * Garante que o diretório exista.
     */
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

    /**
     * Exibe o output do comando Artisan executado.
     */
    private function outputCommandOutput(): void
    {
        $output = trim(Artisan::output());

        if ($output !== '') {
            $this->line($output);
        }
    }
}