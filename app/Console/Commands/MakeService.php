<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:service {name : The name of the service, including optional subfolder structure (e.g., MasterServices/Master)}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new Service, its interface (with optional folder structure), and register it in the ServicesServiceProvider';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        $normalizedPath = str_replace('\\', '/', $name);
        $className = basename($normalizedPath);
        $namespacePath = dirname($normalizedPath);

        $serviceNamespace = 'App\\Services'.($namespacePath !== '.' ? '\\'.str_replace('/', '\\', $namespacePath) : '');
        $interfaceNamespace = 'App\\Services\\Interfaces'.($namespacePath !== '.' ? '\\'.str_replace('/', '\\',
                    $namespacePath) : '');

        $servicePath = app_path("Services/{$normalizedPath}Service.php");
        $interfacePath = app_path("Services/Interfaces/{$normalizedPath}ServiceInterface.php");
        $providerPath = app_path('Providers/ServicesServiceProvider.php');

        if (! File::exists($servicePath)) {
            File::ensureDirectoryExists(dirname($servicePath));
            File::put($servicePath, $this->getServiceStubContent($className, $serviceNamespace, $interfaceNamespace));
            $this->info("Service created: {$servicePath}");
        } else {
            $this->warn("Service already exists: {$servicePath}");
        }

        if (! File::exists($interfacePath)) {
            File::ensureDirectoryExists(dirname($interfacePath));
            File::put($interfacePath, $this->getInterfaceStubContent($className, $interfaceNamespace));
            $this->info("Interface created: {$interfacePath}");
        } else {
            $this->warn("Interface already exists: {$interfacePath}");
        }

        if (File::exists($providerPath)) {
            $providerContent = File::get($providerPath);

            $binding = "\$this->app->bind(\\{$interfaceNamespace}\\{$className}ServiceInterface::class, \\{$serviceNamespace}\\{$className}Service::class);";

            if (! Str::contains($providerContent, $binding)) {
                $providerContent = preg_replace(
                    '/public function register\(\): void\s*{/',
                    "public function register(): void\n    {\n        {$binding}",
                    $providerContent
                );

                $this->info('Binding added to ServicesServiceProvider -> register()');

                $providesEntry = "\\{$interfaceNamespace}\\{$className}ServiceInterface::class";

                $providerContent = preg_replace(
                    '/return \[([\s\S]*?)\];/',
                    "return [\n            {$providesEntry},$1];",
                    $providerContent
                );

                File::put($providerPath, $providerContent);
                $this->info('Service registered in ServicesServiceProvider');
            } else {
                $this->warn('Service already registered in ServicesServiceProvider');
            }
        } else {
            $this->error('ServicesServiceProvider not found');
        }
    }

    /**
     * Get the stub content for the service.
     */
    protected function getServiceStubContent($className, $namespace, $interfaceNamespace): string
    {
        return <<<PHP
            <?php
            
            namespace {$namespace};
            
            use {$interfaceNamespace}\\{$className}ServiceInterface;
            
            class {$className}Service implements {$className}ServiceInterface
            {
                // Implement the methods of {$className}ServiceInterface
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
            
            interface {$className}ServiceInterface
            {
                // Define the methods for the {$className}Service
            }
            
            PHP;
    }
}
