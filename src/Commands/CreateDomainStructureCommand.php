<?php

namespace Domain\DomainGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

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
        return env(
            'APP_DOMAIN_FOLDER',
            'Domain'
        );
    }

    /**
     * Executa o comando.
     */
    public function handle(): int
    {
        $name = ucfirst(
            $this->argument('name')
        );

        $force = (bool) $this->option('force');

        $this->info(
            "Iniciando a criação da estrutura para o domínio: {$name}"
        );

        $this->newLine();

        /*
         * Model + Migration
         */
        $this->createModelAndMigration(
            $name,
            $force
        );

        /*
         * Controller
         */
        $this->createController(
            $name,
            $force
        );

        /*
         * Request
         */
        $this->createRequest(
            $name,
            $force
        );

        /*
         * DTO
         */
        $this->createDto(
            $name,
            $force
        );

        /*
         * Service
         */
        $this->createService(
            $name,
            $force
        );

        /*
         * Repository
         */
        $this->createRepository(
            $name,
            $force
        );

        $this->newLine();

        $this->info(
            "✨ Estrutura para {$name} criada com sucesso!"
        );

        return self::SUCCESS;
    }

    /**
     * Cria Model e Migration.
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

        $this->ensureDirectoryExists($path);

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $namespace = 'App\\Http\\Controllers';

        $serviceNamespace =
            "App\\{$domainFolder}\\{$name}\\Service";

        $dtoNamespace =
            "App\\{$domainFolder}\\{$name}\\DTO";

        $requestNamespace =
            'App\\Http\\Requests';

        $template = <<<PHP
<?php

namespace {$namespace};

use {$serviceNamespace}\\{$name}Service;
use {$requestNamespace}\\{$name}Request;
use {$dtoNamespace}\\{$name}DTO;
use Domain\\DomainGenerator\\Abstracts\\AbstractController;

class {$controllerName} extends AbstractController
{
    protected mixed \$service;

    protected ?string \$requestValidate = {$name}Request::class;

    protected ?string \$requestDto = {$name}DTO::class;

    public function __construct({$name}Service \$service)
    {
        \$this->service = \$service;
    }
}
PHP;

        File::put(
            $fullPath,
            $template
        );
    }

    /**
     * Cria Request.
     */
    private function createRequest(
        string $name,
        bool $force = false
    ): void {
        $requestName = "{$name}Request";

        $this->info(
            "Criando Request {$requestName} em app/Http/Requests..."
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

    /**
     * Cria DTO.
     */
    private function createDto(
        string $name,
        bool $force = false
    ): void {
        $domainFolder = $this->getDomainFolder();

        $path = app_path(
            "{$domainFolder}/{$name}/DTO"
        );

        $fileName = "{$name}DTO.php";

        $fullPath = "{$path}/{$fileName}";

        $this->info(
            "Criando DTO {$fileName} em app/{$domainFolder}/{$name}/DTO..."
        );

        $this->ensureDirectoryExists($path);

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $namespace =
            "App\\{$domainFolder}\\{$name}\\DTO";

        $template = <<<PHP
<?php

namespace {$namespace};

use Domain\\DomainGenerator\\Abstracts\\AbstractDTO;

final class {$name}DTO extends AbstractDTO
{
    public function __construct()
    {
    }
}
PHP;

        File::put(
            $fullPath,
            $template
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
            "Criando Service {$fileName} em app/{$domainFolder}/{$name}/Service..."
        );

        $this->ensureDirectoryExists($path);

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $serviceNamespace =
            "App\\{$domainFolder}\\{$name}\\Service";

        $repositoryNamespace =
            "App\\{$domainFolder}\\{$name}\\Repositories";

        $template = <<<PHP
<?php

namespace {$serviceNamespace};

use {$repositoryNamespace}\\{$name}Repository;
use Domain\\DomainGenerator\\Abstracts\\AbstractService;

class {$name}Service extends AbstractService
{
    public function __construct({$name}Repository \$repository)
    {
        \$this->repository = \$repository;
    }
}
PHP;

        File::put(
            $fullPath,
            $template
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
            "Criando Repository {$fileName} em app/{$domainFolder}/{$name}/Repositories..."
        );

        $this->ensureDirectoryExists($path);

        if (
            File::exists($fullPath) &&
            ! $force
        ) {
            $this->warn(
                "O arquivo {$fileName} já existe. Ignorado."
            );

            return;
        }

        $repositoryNamespace =
            "App\\{$domainFolder}\\{$name}\\Repositories";

        $modelNamespace =
            'App\\Models';

        $template = <<<PHP
<?php

namespace {$repositoryNamespace};

use {$modelNamespace}\\{$name};
use Domain\\DomainGenerator\\Abstracts\\AbstractRepository;

class {$name}Repository extends AbstractRepository
{
    public function __construct({$name} \$model)
    {
        parent::__construct(\$model);
    }
}
PHP;

        File::put(
            $fullPath,
            $template
        );
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
     * Exibe o output de comandos Artisan executados internamente.
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