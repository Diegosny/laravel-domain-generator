<?php

namespace Domain\DomainGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateDomainStructureCommand extends Command
{
    protected $signature = 'make:domain
                            {name : O nome do modelo (ex: User)}
                            {--force : Forçar a criação mesmo se já existirem arquivos}';

    protected $description = 'Cria Model, Migration, Controller, Request, DTO, Service e Repository para um novo domínio';

    /**
     * Retorna a pasta base dos domínios.
     */
    private function getDomainFolder(): string
    {
        return env('APP_DOMAIN_FOLDER', 'Domain');
    }

    /**
     * Retorna o diretório dos stubs.
     */
    private function getStubsPath(): string
    {
        return dirname(__DIR__) . '/Stubs';
    }

    /**
     * Executa o comando.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $force = (bool) $this->option('force');

        $this->info("Iniciando a criação da estrutura para o domínio: {$name}");
        $this->newLine();

        $this->createModelAndMigration($name, $force);
        $this->createController($name, $force);
        $this->createRequests($name, $force);
        $this->createDtos($name, $force);
        $this->createService($name, $force);
        $this->createRepository($name, $force);
        $this->createResource($name, $force);

        $this->newLine();
        $this->info("✨ Estrutura para {$name} criada com sucesso!");

        return self::SUCCESS;
    }

    /**
     * Cria Model e Migration.
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

        $this->replaceGeneratedModel($name);
    }

    /**
     * Substitui a Model gerada pelo Laravel pelo model.stub.
     */
    private function replaceGeneratedModel(string $name): void
    {
        $modelPath = app_path("Models/{$name}.php");

        $stub = $this->loadStub('model.stub');

        $content = $this->replaceStubVariables($stub, [
            'namespace' => 'App\Models',
            'model' => $name,
            'table' => $this->getTableName($name),
            'hashPrefix' => $this->getHashPrefix($name),
        ]);

        File::put($modelPath, $content);
    }

    /**
     * Retorna o nome da tabela automaticamente.
     *
     * Patient -> patients
     * MedicalRecord -> medical_records
     */
    private function getTableName(string $model): string
    {
        return Str::snake(
            Str::pluralStudly($model)
        );
    }

    /**
     * Gera um prefixo automaticamente.
     *
     * Patient             -> PAT
     * Product             -> PRO
     * MedicalRecord       -> MRE
     * EmergencyAttendance -> EAT
     * UserSessionToken    -> UST
     */
    private function getHashPrefix(string $model): string
    {
        $words = preg_split(
            '/(?=[A-Z])/',
            $model,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 3));
        }

        $prefix = '';

        foreach ($words as $word) {
            $prefix .= strtoupper($word[0]);
        }

        if (strlen($prefix) < 3) {

            $firstWord = strtoupper($words[0]);
            $i = 1;

            while (
                strlen($prefix) < 3 &&
                isset($firstWord[$i])
            ) {
                $prefix .= $firstWord[$i];
                $i++;
            }
        }

        return substr($prefix, 0, 3);
    }

    /**
     * Cria Controller.
     */
    private function createController(string $name, bool $force = false): void
    {
        $domainFolder = $this->getDomainFolder();

        $controllerName = "{$name}Controller";

        $path = app_path('Http/Controllers');
        $fileName = "{$controllerName}.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Controller {$fileName}...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && ! $force) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $stub = $this->loadStub('controller.stub');

        $content = $this->replaceStubVariables($stub, [
            'name' => $name,
            'controller' => $controllerName,
            'domainFolder' => $domainFolder,
        ]);

        File::put($fullPath, $content);
    }

    /**
     * Cria Request.
     */
    private function createRequests(string $name, bool $force = false): void
    {
         $requests = [
            "Create{$name}Request",
            "Update{$name}Request",
        ];

        foreach ($requests as $request) {

            $this->info("Criando Request {$request}...");

            $params = ['name' => $request];

            if ($force) {
                $params['--force'] = true;
            }

            Artisan::call('make:request', $params);

            $this->outputCommandOutput();
        }
    }

    /**
     * Cria DTO.
     */
    private function createDtos(string $name, bool $force = false): void
    {
        $domainFolder = $this->getDomainFolder();

        $path = app_path("{$domainFolder}/{$name}/DTO");

        $this->ensureDirectoryExists($path);

        $dtos = [
            "Create{$name}DTO" => 'dto-create.stub',
            "Update{$name}DTO" => 'dto-update.stub',
        ];

        foreach ($dtos as $class => $stubFile) {

            $file = "{$path}/{$class}.php";

            $this->info("Criando DTO {$class}...");

            if (File::exists($file) && ! $force) {

                $this->warn("{$class} já existe.");

                continue;
            }

            $stub = $this->loadStub($stubFile);

            File::put(
                $file,
                $this->replaceStubVariables($stub, [
                    'name' => $name,
                    'dto' => $class,
                    'domainFolder' => $domainFolder,
                ])
            );
        }
    }

    /**
     * Cria Service.
     */
    private function createService(string $name, bool $force = false): void
    {
        $domainFolder = $this->getDomainFolder();

        $path = app_path("{$domainFolder}/{$name}/Service");

        $fileName = "{$name}Service.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Service {$fileName}...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && ! $force) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $stub = $this->loadStub('service.stub');

        $content = $this->replaceStubVariables($stub, [
            'name' => $name,
            'domainFolder' => $domainFolder,
        ]);

        File::put($fullPath, $content);
    }

    /**
     * Cria Repository.
     */
    private function createRepository(string $name, bool $force = false): void
    {
        $domainFolder = $this->getDomainFolder();

        $path = app_path("{$domainFolder}/{$name}/Repositories");

        $fileName = "{$name}Repository.php";
        $fullPath = "{$path}/{$fileName}";

        $this->info("Criando Repository {$fileName}...");

        $this->ensureDirectoryExists($path);

        if (File::exists($fullPath) && ! $force) {
            $this->warn("O arquivo {$fileName} já existe. Ignorado.");
            return;
        }

        $stub = $this->loadStub('repository.stub');

        $content = $this->replaceStubVariables($stub, [
            'name' => $name,
            'domainFolder' => $domainFolder,
        ]);

        File::put($fullPath, $content);
    }

    private function createResource(string $name, bool $force = false): void 
    {

        $path = app_path('Http/Resources');

        $fileName = "{$name}Resource.php";

        $fullPath = "{$path}/{$fileName}";

        $this->ensureDirectoryExists($path);

        $this->info("Criando Resource {$fileName}...");

        if (File::exists($fullPath) && ! $force) {

            $this->warn("{$fileName} já existe.");

            return;
        }

        $stub = $this->loadStub('resource.stub');

        File::put(
            $fullPath,
            $this->replaceStubVariables($stub, [
                'name' => $name,
            ])
        );
    }

    /**
     * Carrega um stub da biblioteca.
     */
    private function loadStub(string $stub): string
    {
        $path = $this->getStubsPath() . "/{$stub}";

        if (! File::exists($path)) {
            throw new \RuntimeException("Stub não encontrado: {$path}");
        }

        return File::get($path);
    }

    /**
     * Substitui variáveis do stub.
     */
    private function replaceStubVariables(string $stub, array $variables): string
    {
        foreach ($variables as $key => $value) {

            $stub = str_replace(
                [
                    '{{ ' . $key . ' }}',
                    '{{' . $key . '}}',
                    '$' . strtoupper($key),
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
     * Exibe o output dos comandos Artisan.
     */
    private function outputCommandOutput(): void
    {
        $output = Artisan::output();

        if (filled($output)) {
            $this->line(trim($output));
        }
    }
}