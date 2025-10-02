<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:repository {name : The name of the repository, including optional subfolder structure (e.g., User/UserRepository)}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new Repository, its interface (with optional folder structure), and register it in the RepositoryServiceProvider.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        // Normalize the name for repository and interface
        $normalizedPath = str_replace('\\', '/', $name);
        $className = basename($normalizedPath);
        $namespacePath = dirname($normalizedPath);

        $repositoryNamespace = 'App\\Repositories'.($namespacePath !== '.' ? '\\'.str_replace('/', '\\', $namespacePath) : '');
        $interfaceNamespace = 'App\\Repositories\\Contracts'.($namespacePath !== '.' ? '\\'.str_replace('/', '\\', $namespacePath) : '');

        // Paths
        $repositoryPath = app_path("Repositories/{$normalizedPath}Repository.php");
        $interfacePath = app_path("Repositories/Contracts/{$normalizedPath}RepositoryInterface.php");
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        // Create Repository
        if (! File::exists($repositoryPath)) {
            File::ensureDirectoryExists(dirname($repositoryPath));
            File::put($repositoryPath, $this->getRepositoryStubContent($className, $repositoryNamespace, $interfaceNamespace));
            $this->info("Repository created: {$repositoryPath}");
        } else {
            $this->warn("Repository already exists: {$repositoryPath}");
        }

        // Create Interface
        if (! File::exists($interfacePath)) {
            File::ensureDirectoryExists(dirname($interfacePath));
            File::put($interfacePath, $this->getInterfaceStubContent($className, $interfaceNamespace));
            $this->info("Interface created: {$interfacePath}");
        } else {
            $this->warn("Interface already exists: {$interfacePath}");
        }

        // Register in Repository Provider
        if (File::exists($providerPath)) {
            $this->registerInProvider($providerPath, $className, $repositoryNamespace, $interfaceNamespace);
        } else {
            $this->error('RepositoryServiceProvider not found');
        }
    }

    /**
     * Register the repository and interface in the RepositoriesServiceProvider.
     */
    protected function registerInProvider($providerPath, $className, $repositoryNamespace, $interfaceNamespace): void
    {
        $providerContent = File::get($providerPath);

        // Add the binding in the `register` method
        $binding = "\$this->app->bind(\\{$interfaceNamespace}\\{$className}RepositoryInterface::class, \\{$repositoryNamespace}\\{$className}Repository::class);";

        if (! Str::contains($providerContent, $binding)) {
            $providerContent = preg_replace(
                '/public function register\(\): void\s*{/',
                "public function register(): void\n    {\n        {$binding}",
                $providerContent
            );

            $this->info('Binding added to RepositoryServiceProvider -> register()');

            $providesEntry = "\\{$interfaceNamespace}\\{$className}RepositoryInterface::class";
            $providerContent = preg_replace(
                '/return \[([\s\S]*?)\];/',
                "return [\n            {$providesEntry},$1];",
                $providerContent
            );

            $this->info('Provides entry added to RepositoryServiceProvider -> provides()');
        } else {
            $this->warn('Binding already exists in RepositoryServiceProvider -> register()');
        }

        // Save changes to the provider file
        File::put($providerPath, $providerContent);
        $this->info('RepositoryServiceProvider updated successfully.');
    }

    /**
     * Get the stub content for the repository.
     */
    protected function getRepositoryStubContent($className, $namespace, $interfaceNamespace): string
    {
        return <<<PHP
            <?php
            
            namespace {$namespace};
            
            use App\Repositories\BaseRepository;
            use use Illuminate\Database\Eloquent\Model;{$interfaceNamespace}\\{$className}RepositoryInterface;
            
            class {$className}Repository extends BaseRepository implements {$className}RepositoryInterface
            {
                // Implement methods for {$className}
                protected \$model;

                /**
                 * HotelRepository constructor.
                 */
                public function __construct(Model \$model)
                {
                    parent::__construct(\$model);
                }
            }
            
            PHP;
    }

    /**
     * Get the stub content for the interface.
     */
    protected function getInterfaceStubContent($className, $namespace): string
    {
        return <<<PHP
            <?php
            
            namespace {$namespace};
            
            interface {$className}RepositoryInterface extends BaseRepositoryInterface
            {
                // Define methods for {$className}
            }
            
            PHP;
    }
}
