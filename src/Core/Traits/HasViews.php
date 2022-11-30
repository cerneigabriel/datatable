<?php

namespace Modules\DataTable\Core\Traits;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

/**
 * Trait HasViews
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasViews
{
    /** @var Collection */
    protected Collection $viewBag;

    /** @var \Illuminate\Support\Collection */
    protected Collection $defaultViewBag;

    /**
     * @return void
     */
    public function mountViews(): void
    {
        $this->defaultViewBag = Collection::make();
        $this->viewBag = Collection::make();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function viewBag()
    {
        return $this->viewBag;
    }

    /**
     * @param int|string $key
     * @param mixed $value
     * @return $this
     */
    public function putToViewBag(int|string $key, mixed $value): static
    {
        $this->viewBag->put($key, $value);

        return $this;
    }

    /**
     * @param ...$values
     * @return $this
     */
    public function pushToViewBag(...$values): static
    {
        $this->viewBag->push(...$values);

        return $this;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function mergeWithViewBag(array $items): static
    {
        $this->viewBag = $this->viewBag->merge($items);

        return $this;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveRender(): string
    {
        /** @var \Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\View $view */
        $view = (($temp = $this->resolveView()) instanceof Closure ? $temp->call($this, []) : $temp);

        return $view->with($this->defaultViewBag->merge($this->viewBag)->toArray())->render();
    }

    /**
     * Resolve the Blade view or view file that should be used when rendering the component.
     *
     * @return \Illuminate\Contracts\Support\Htmlable|\Illuminate\Contracts\View\View|\Closure
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveView(): Htmlable|ViewContract|Closure
    {
        $view = $this->render();

        if ($view instanceof ViewContract) {
            return $view;
        }

        if ($view instanceof Htmlable) {
            return $view;
        }

        $resolver = function ($view) {
            $factory = Container::getInstance()->make('view');

            return strlen($view) <= PHP_MAXPATHLEN && $factory->exists($view)
                ? $view
                : $this->createBladeViewFromString($factory, $view);
        };

        return $view instanceof Closure
            ? function (array $data = []) use ($view, $resolver) {
                return View::make($resolver($view($data)));
            }
            : View::make($resolver($view));
    }

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    abstract protected function render(): ViewContract|Htmlable|string|Closure;

    /**
     * Create a Blade view with the raw component string content.
     *
     * @param \Illuminate\Contracts\View\Factory $factory
     * @param string $contents
     * @return string
     */
    protected function createBladeViewFromString(ViewFactory $factory, string $contents): string
    {
        $factory->addNamespace(
            '__components',
            $directory = Container::getInstance()['config']->get('view.compiled')
        );

        if (!is_file($viewFile = $directory . '/' . sha1($contents) . '.blade.php')) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($viewFile, $contents);
        }

        return '__components::' . basename($viewFile, '.blade.php');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function defaultViewBag()
    {
        return $this->defaultViewBag;
    }
}