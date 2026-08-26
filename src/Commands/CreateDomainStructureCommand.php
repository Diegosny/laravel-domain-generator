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

    protected $description = 'Cria Model, Migration, Controller, Requests, DTOs, Resource, Service e Repository para um novo domínio';

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

        $this->info(
            "Iniciando a criação da estrutura para o domínio: {$name}"
        );

        $this->newLine();

        $this->createModelAndMigration(
            $name,
            $force
        );

        $this->createController(
            $name,
            $force
        );

        $this->createRequests(
            $name,
            $force
        );

        $this->createDtos(
            $name,
            $force
        );

        $this->createResource(
            $name,
            $force
        );

        $this->createService(
            $name,
            $force
        );

        $this->createRepository(
            $name,
            $force
        );

        $this->newLine();

        $this->info(
            "✨ Estrutura para {$name} criada com sucesso!"
        );

        $this->newLine();

        $this->line('Arquivos principais gerados:');
        $this->line("  • Model: {$name}.php");
        $this->line("  • Request: Create{$name}Request.php");
        $this->line("  • Request: Update{$name}Request.php");
        $this->line("  • DTO: Create{$name}DTO.php");
        $this->line("  • DTO: Update{$name}DTO.php");
        $this->line("  • Resource: {$name}Resource.php");
        $this->line("  • Controller: {$name}Controller.php");
        $this->line("  • Service: {$name}Service.php");
        $this->line("  • Repository: {$name}Repository.php");

        return self::SUCCESS;
    }

    /**
     * Cria Model e Migration utilizando o generator nativo
     * do Laravel e depois aplica o model.stub da biblioteca.
     */
    private function createModelAndMigration(
        string $name,
        bool $force = false
    ): void {
        $this->info(
            "Criando Model e Migration para {$name}..."
        );

        $params = [
            'name' => $name,
            '-m' => true,
        ];

        if ($force) {
            $params['--force'] = true;
        }

        Artisan::call(
            'make:model',
            $params
        );

        $this->outputCommandOutput();

        $this->replaceGeneratedModel(
            $name
        );
    }

    /**
     * Substitui a Model criada pelo Laravel pelo model.stub
     * disponibilizado pela biblioteca.
     */
    private function replaceGeneratedModel(
        string $name
    ): void {
        $modelPath = app_path(
            "Models/{$name}.php"
        );

        $stub = $this->loadStub(
            'model.stub'
        );

        $content = $this->replaceStubVariables(
            $stub,
            [
                'name' => $name,
                'model' => $name,
                'namespace' => 'App\\Models',
                'table' => $this->getTableName($name),
                'hashPrefix' => $this->getHashPrefix($name),
            ]
        );

        File::put(
            $modelPath,
            $content
        );

        $this->line(
            "Model {$name}.php configurada com HasHash."
        );
    }

    /**
     * Retorna automaticamente o nome da tabela.
     *
     * Exemplos:
     *
     * Patient       -> patients
     * Product       -> products
     * Organization  -> organizations
     * MedicalRecord -> medical_records
     */
    private function getTableName(
        string $model
    ): string {
        return Str::snake(
            Str::pluralStudly($model)
        );
    }

    /**
     * Gera dinamicamente o prefixo utilizado pelo HasHash.
     *
     * Não existem entidades conhecidas ou prefixos fixos
     * dentro da biblioteca.
     *
     * Exemplos:
     *
     * Patient             -> PAT
     * Product             -> PRO
     * Organization        -> ORG
     * MedicalRecord       -> MRE
     * EmergencyAttendance -> EAT
     * UserSessionToken    -> UST
     */
    private function getHashPrefix(
        string $model
    ): string {
        $words = preg_split(
            '/(?=[A-Z])/',
            Str::studly($model),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if (empty($words)) {
            return strtoupper(
                substr($model, 0, 3)
            );
        }

        /**
         * Model com apenas uma palavra.
         *
         * Patient -> PAT
         * Product -> PRO
         */
        if (count($words) === 1) {
            return strtoupper(
                substr($words[0], 0, 3)
            );
        }

        /**
         * Model composta.
         *
         * Inicialmente utiliza a primeira letra
         * de cada palavra.
         */
        $prefix = '';

        foreach ($words as $word) {
            $prefix .= strtoupper(
                substr($word, 0, 1)
            );
        }

        /**
         * Se o acrônimo possuir menos de 3 caracteres,
         * completa utilizando caracteres da primeira
         * palavra.
         *
         * MedicalRecord
         *
         * MR
         * +
         * E
         *
         * = MRE
         */
        if (strlen($prefix) < 3) {
            $firstWord = strtoupper(
                $words[0]
            );

            $index = 1;

            while (
                strlen($prefix) < 3 &&
                isset($firstWord[$index])
            ) {
                $prefix .= $firstWord[$index];

                $index++;
            }
        }

        return substr(
            $prefix,
            0,
            3
        );
    }

    /**
     * Cria Controller.
     */
    private function createController(
        string $name,
        bool $force = false
    ): void {
        $domainFolder = $this->getDomainFolder();

        $controllerName = "{$name}Controller";

        $path = app_path(
            'Http/Controllers'
        );

        $fileName = "{$controllerName}.php";

        $fullPath = "{$path}/{$fileName}";

        $this->info(
            "Criando Controller {$fileName}..."
        );

        $this->ensureDirectoryExists(
            $path
        );

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $stub = $this->loadStub(
            'controller.stub'
        );

        $content = $this->replaceStubVariables(
            $stub,
            [
                'name' => $name,
                'controller' => $controllerName,
                'domainFolder' => $domainFolder,
            ]
        );

        File::put(
            $fullPath,
            $content
        );
    }

    /**
     * Cria os Requests de Create e Update.
     *
     * Exemplo:
     *
     * CreateUserRequest
     * UpdateUserRequest
     */
    private function createRequests(
        string $name,
        bool $force = false
    ): void {
        $requests = [
            "Create{$name}Request",
            "Update{$name}Request",
        ];

        foreach ($requests as $requestName) {
            $this->info(
                "Criando Request {$requestName}..."
            );

            $params = [
                'name' => $requestName,
            ];

            if ($force) {
                $params['--force'] = true;
            }

            Artisan::call(
                'make:request',
                $params
            );

            $this->outputCommandOutput();
        }
    }

    /**
     * Cria os DTOs de Create e Update.
     *
     * Exemplo:
     *
     * CreateUserDTO
     * UpdateUserDTO
     */
    private function createDtos(
        string $name,
        bool $force = false
    ): void {
        $domainFolder = $this->getDomainFolder();

        $path = app_path(
            "{$domainFolder}/{$name}/DTO"
        );

        $this->ensureDirectoryExists(
            $path
        );

        $dtos = [
            [
                'class' => "Create{$name}DTO",
                'stub' => 'dto-create.stub',
            ],
            [
                'class' => "Update{$name}DTO",
                'stub' => 'dto-update.stub',
            ],
        ];

        foreach ($dtos as $dto) {
            $className = $dto['class'];
            $stubName = $dto['stub'];

            $fileName = "{$className}.php";

            $fullPath = "{$path}/{$fileName}";

            $this->info(
                "Criando DTO {$fileName}..."
            );

            if (
                File::exists($fullPath) &&
                ! $force
            ) {
                $this->warn(
                    "O arquivo {$fileName} já existe. Ignorado."
                );

                continue;
            }

            $stub = $this->loadStub(
                $stubName
            );

            $content = $this->replaceStubVariables(
                $stub,
                [
                    'name' => $name,
                    'dto' => $className,
                    'domainFolder' => $domainFolder,
                ]
            );

            File::put(
                $fullPath,
                $content
            );
        }
    }

    /**
     * Cria Resource.
     *
     * Exemplo:
     *
     * UserResource
     */
    private function createResource(
        string $name,
        bool $force = false
    ): void {
        $path = app_path(
            'Http/Resources'
        );

        $fileName = "{$name}Resource.php";

        $fullPath = "{$path}/{$fileName}";

        $this->info(
            "Criando Resource {$fileName}..."
        );

        $this->ensureDirectoryExists(
            $path
        );

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $stub = $this->loadStub(
            'resource.stub'
        );

        $content = $this->replaceStubVariables(
            $stub,
            [
                'name' => $name,
                'resource' => "{$name}Resource",
            ]
        );

        File::put(
            $fullPath,
            $content
        );
    }

    /**
     * Cria Service.
     */
    private function createService(
        string $name,
        bool $force = false
    ): void {
        $domainFolder = $this->getDomainFolder();

        $path = app_path(
            "{$domainFolder}/{$name}/Service"
        );

        $fileName = "{$name}Service.php";

        $fullPath = "{$path}/{$fileName}";

        $this->info(
            "Criando Service {$fileName}..."
        );

        $this->ensureDirectoryExists(
            $path
        );

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $stub = $this->loadStub(
            'service.stub'
        );

        $content = $this->replaceStubVariables(
            $stub,
            [
                'name' => $name,
                'domainFolder' => $domainFolder,
            ]
        );

        File::put(
            $fullPath,
            $content
        );
    }

    /**
     * Cria Repository.
     */
    private function createRepository(
        string $name,
        bool $force = false
    ): void {
        $domainFolder = $this->getDomainFolder();

        $path = app_path(
            "{$domainFolder}/{$name}/Repositories"
        );

        $fileName = "{$name}Repository.php";

        $fullPath = "{$path}/{$fileName}";

        $this->info(
            "Criando Repository {$fileName}..."
        );

        $this->ensureDirectoryExists(
            $path
        );

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $stub = $this->loadStub(
            'repository.stub'
        );

        $content = $this->replaceStubVariables(
            $stub,
            [
                'name' => $name,
                'domainFolder' => $domainFolder,
            ]
        );

        File::put(
            $fullPath,
            $content
        );
    }

    /**
     * Carrega um stub da biblioteca.
     */
    private function loadStub(
        string $stub
    ): string {
        $path = $this->getStubsPath() . "/{$stub}";

        if (! File::exists($path)) {
            throw new \RuntimeException(
                "Stub não encontrado: {$path}"
            );
        }

        return File::get(
            $path
        );
    }

    /**
     * Substitui as variáveis existentes no stub.
     *
     * Suporta:
     *
     * {{ name }}
     * {{name}}
     * $NAME
     */
    private function replaceStubVariables(
        string $stub,
        array $variables
    ): string {
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
    private function ensureDirectoryExists(
        string $path
    ): void {
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
        $output = Artisan::output();

        if (filled($output)) {
            $this->line(
                trim($output)
            );
        }
    }
}