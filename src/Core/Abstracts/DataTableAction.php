<?php

namespace Modules\DataTable\Core\Abstracts;

use Closure;
use Exception;
use ReflectionClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use Modules\DataTable\Core\Interfaces\Abstracts\DataTableActionInterface;

/**
 * Class DataTableAction
 *
 * @package Modules\DataTable\Core\Abstracts
 */
abstract class DataTableAction extends SerializableDataTable implements DataTableActionInterface
{
    /** @var string */
    public string $name;

    /** @var \Closure */
    public Closure $renderCallback;

    /** @var \Closure|null */
    public ?Closure $availabilityResolver = null;

    /** @var string */
    public string $text;

    /** @var array */
    public array $attributes = [];

    /** @var string */
    public string $textColor = 'slate-500';

    /** @var array */
    public array $beforeBrowserEvents = [];

    /** @var array */
    public array $afterBrowserEvents = [];

    /** @var array */
    public array $beforeListeners = [];

    /** @var array */
    public array $afterListeners = [];

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->mountSerialization();
        $this->name = $this->makeName($name);
        $this->text = $this->makeText($this->name);
        $this->setTextColor($this->textColor);
    }

    /**
     * @param $name
     * @return string
     */
    protected function makeName($name): string
    {
        return strtolower(Str::replace(['-', '_', '.'], '-', $name));
    }

    /**
     * @param $name
     * @return string
     */
    protected function makeText($name): string
    {
        return ucwords(Str::replace(['-', '_', '.'], ' ', $name));
    }

    /**
     * Set text color using tailwind classes (primary, secondary, danger, warning, slate, gray, ...)
     *
     * @param string $textColor
     * @return $this
     */
    public function setTextColor(string $textColor): static
    {
        if (str_contains($this->attributes['class'] ?? '', 'text-')) {
            $this->removeClass("text-$this->textColor");
        }

        $this->textColor = strtolower(trim($textColor));
        $this->addClass("text-$this->textColor");

        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function removeClass(string $class): static
    {
        if (preg_match("/^\/.+\/[a-z]*$/i", $class)) {
            $this->attributes['class'] = preg_replace("/\S*$class\S*/i", '', trim($this->attributes['class'] ?? ''));
        } else {
            $classes = $this->getClasses();
            $classes = $classes->reject(fn($item) => $item === $class);
            $this->attributes['class'] = $classes->implode(' ');
        }

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getClasses(): Collection
    {
        return collect(explode(' ', $this->attributes['class'] ?? ''));
    }

    /**
     * @param string $class
     * @return $this
     */
    public function addClass(string $class): static
    {
        $classes = $this->getClasses();
        $classes = $classes->push($class);
        $this->attributes['class'] = $classes->implode(' ');

        return $this;
    }

    /**
     * @param string|array $event
     * @param mixed|null $data
     * @return $this
     */
    public function dispatchBeforeBrowserEvents(string|array $event, mixed $data = null): static
    {
        $this->dispatchBrowserEvents($event, $data, 'before');

        return $this;
    }

    /**
     * @param string|array $event
     * @param mixed|null $data
     * @param string $when
     * @return void
     */
    private function dispatchBrowserEvents(string|array $event, mixed $data = null, string $when = 'after')
    {
        if (is_string($event)) {
            $this->{"{$when}BrowserEvents"} = [[$event, $data]];
        } else {
            $this->{"{$when}BrowserEvents"} = $event;
        }
    }

    /**
     * @param string|array $event
     * @param mixed|null $data
     * @return $this
     */
    public function dispatchAfterBrowserEvents(string|array $event, mixed $data = null): static
    {
        $this->dispatchBrowserEvents($event, $data);

        return $this;
    }

    /**
     * @param string|array $listener
     * @param mixed|null $data
     * @return $this
     */
    public function emitBeforeListeners(string|array $listener, mixed $data = null): static
    {
        $this->emitListeners($listener, $data, 'before');

        return $this;
    }

    /**
     * @param string|array $listener
     * @param mixed|null $data
     * @param string $when
     * @return void
     */
    private function emitListeners(string|array $listener, mixed $data = null, string $when = 'after')
    {
        if (is_string($listener)) {
            $this->{"{$when}Listeners"} = [[$listener, $data]];
        } else {
            $this->{"{$when}Listeners"} = $listener;
        }
    }

    /**
     * @param string|array $listener
     * @param mixed|null $data
     * @return $this
     */
    public function emitAfterListeners(string|array $listener, mixed $data = null): static
    {
        $this->emitListeners($listener, $data);

        return $this;
    }

    /**
     * @return $this
     */
    public function resetClasses(): static
    {
        $this->attributes['class'] = '';

        return $this;
    }

    /**
     * @param \Closure $renderCallback
     * @return $this
     */
    public function setRenderCallback(Closure $renderCallback): self
    {
        $this->renderCallback = $renderCallback;

        return $this;
    }

    /**
     * @param \Closure $resolver
     * @return $this
     */
    public function setAvailabilityResolver(Closure $resolver): self
    {
        $this->availabilityResolver = $resolver;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return string
     */
    public function renderAttributes(): string
    {
        return collect($this->attributes)->map(function ($value, $attribute) {
            return "$attribute=\"$value\"";
        })->implode(' ');
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param $entity
     * @return null
     */
    public function render($entity)
    {
        if (!$this->isAvailable($entity)) {
            return null;
        }

        if (isset($this->renderCallback) && $this->isRenderCallbackReturnTypeSupported($data = $this->resolveRenderCallback($entity))) {
            return (string)$data;
        } else {
            if (is_object($entity) || is_array($entity)) {
                $data = is_array($entity) ? (object)$entity : $entity;


                return $this->resolveData($data);
            }
        }

        return null;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function isAvailable($entity): bool
    {
        if (is_null($this->availabilityResolver)) {
            return true;
        }

        return $this->availabilityResolver->call($this, $entity, $this);
    }

    /**
     * @param $data
     * @return bool|mixed
     */
    private function isRenderCallbackReturnTypeSupported($data)
    {
        $given_type = gettype($data);

        try {
            settype($data, 'string');

            return true;
        } catch (Exception $exception) {
            return static::unsupportedRenderCallbackReturnType($given_type, $this->name);
        }
    }

    /**
     * @static
     * @param string $given_type
     * @param string $action
     * @return mixed
     */
    private static function unsupportedRenderCallbackReturnType(string $given_type, string $action): mixed
    {
        return throw new InvalidArgumentException(
            "Given type '$given_type' cannot be converted to 'string'." .
            "Make sure your \$renderCallback for action '$action' is returning 'string' or other supported types to perform the conversion."
        );
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function resolveRenderCallback($entity): mixed
    {
        if (isset($this->renderCallback)) {
            return $this->renderCallback->call($this, $entity, $this);
        }

        return null;
    }
}