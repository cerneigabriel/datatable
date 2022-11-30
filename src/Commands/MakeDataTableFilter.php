<?php

namespace Modules\DataTable\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Modules\DataTable\Core\Abstracts\DataTableFilter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeDataTable
 *
 * @package Modules\DataTable\Commands
 */
#[AsCommand(name: 'make:datatable:filter')]
class MakeDataTableFilter extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:datatable:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a datatable filter file in App/DataTableFilters';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('inline')) {
            return __DIR__ . '/../stubs/DataTableFilter.inline.stub';
        }

        return __DIR__ . '/../stubs/DataTableFilter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\DataTableFilters';
    }

    /**
     * @return array[]
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, "Create the {$this->type} even if the view already exists"],
            ['inline', null, InputOption::VALUE_NONE, 'Create a {$this->type} that renders an inline view'],
        ];
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $replace["{{ abstractDatatableFilter }}"] = DataTableFilter::class;

        if (!$this->option('inline')) {
            $replace["{{ view }}"] = $this->buildView();
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * @return string|null
     */
    protected function buildView(): ?string
    {
        $path = $this->viewPath(
            str_replace('.', '/', "vendor.datatable.filters.{$this->getView()}") . '.blade.php'
        );

        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        if ($this->files->exists($path) && !$this->option('force')) {
            $this->error('View already exists!');

            return null;
        }

        file_put_contents(
            $path,
            <<<'blade'
                <div class="col-span-4">
                    <!-- Content goes here -->
                </div>
            blade
        );

        $this->info('Filter view created successfully!');
        $this->line($path);

        return "datatable::filters.{$this->getView()}";
    }

    /**
     * Get the view name relative to the components directory.
     *
     * @return string view
     */
    protected function getView()
    {
        $name = str_replace('\\', '/', $this->argument('name'));

        return collect(explode('/', $name))
            ->map(function ($part) {
                return Str::kebab($part);
            })
            ->implode('.');
    }
}