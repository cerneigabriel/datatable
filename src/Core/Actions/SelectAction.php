<?php

namespace Modules\DataTable\Core\Actions;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Modules\DataTable\Core\Abstracts\DataTableAction;
use Modules\DataTable\Core\Actions\Traits\RequireConfirmation;

/**
 * Class SelectAction
 *
 * @package Modules\DataTable\Core\Actions
 */
class SelectAction extends DataTableAction
{
    use RequireConfirmation {
        getRequireConfirmation as getRequireConfirmation;
    }

    /** @var string */
    public string $name = 'select';

    /** @var string */
    public string $text = 'Select';

    /** @var string */
    public string $label;

    /** @var string */
    public string $placeholder;

    /** @var string */
    public string $primaryKey = 'id';

    /** @var array */
    public array $config = [
        'itemSelectText' => '',
        'allowHTML' => true,
        'placeholder' => true,
        'shouldSort' => false,
    ];

    /** @var array */
    public array $value = [];

    /**
     * @var \Closure|null
     */
    public ?Closure $optionsHandler;

    /**
     * @var \Closure|null
     */
    public ?Closure $actionHandler;

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null, string $label = null)
    {
        parent::__construct($name ?? $this->name);

        $this->placeholder = $label ?? $this->makeText($name);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param mixed $currentOption
     * @param mixed $previousOption
     * @return bool
     */
    public function getRequireConfirmation(Model $entity, mixed $currentOption = null, mixed $previousOption = null): bool
    {
        if (!is_null($this->requireConfirmationResolver)) {
            return $this->requireConfirmationResolver->call($this, $entity, $currentOption, $previousOption);
        }

        return $this->requireConfirmation;
    }

    /**
     * @param $entity
     * @return string
     */
    public function resolveData($entity): string
    {
        return view('datatable::actions.select', [
            'action' => $this,
            'entity' => $entity,
            'primaryKey' => $this->primaryKey,
        ])->render();
    }

    /**
     * @param array|\Closure $options
     * @return $this
     */
    public function setOptions(array|Closure $options): self
    {
        $this->optionsHandler = $options instanceof Closure
            ? $options
            : fn() => $options;

        return $this;
    }

    /**
     * @param string|null $nullValueLabel
     * @return $this
     */
    public function setOptionsNullValue(string $nullValueLabel = null): static
    {
        $this->config['placeholder'] = true;
        $this->config['placeholderValue'] = $nullValueLabel;

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config): static
    {
        $this->config = collect($config)->filter(fn($item, $index) => (is_string($index) && !is_null($item)))->toArray();

        return $this;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return array
     * @throws \Exception
     */
    public function config(Model $entity): array
    {
        $this->options($entity);
        if ($this->config['placeholder'] ?? false) {
            $this->config['choices'] = [[
                'value' => '',
                'label' => $this->config['placeholderValue'],
                'selected' => !collect($this->config['choices'] ?? [])->where('selected', true)->count(),
                'disabled' => false,
            ], ...$this->config['choices'] ?? []];
        }

        return $this->config;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @return array
     * @throws \Exception
     */
    public function options(Model $entity): array
    {
        if ($this->getOptionsHandler()) {
            $options = $this->getOptionsHandler()->call($this, $entity);

            if (is_array($options) || is_object($options)) {
                $options = collect($options)
                    ->map(function ($label, $value) {
                        if (is_string($label)) {
                            return [
                                'value' => $value,
                                'label' => $label,
                            ];
                        }

                        if (is_numeric($value) && (is_array($label) || is_object($label))) {
                            if (isset($label['label']) && isset($label['value'])) {
                                return $label;
                            }
                        }

                        return null;
                    })
                    ->filter(function ($option) {
                        return !is_null($option);
                    })
                    ->values()
                    ->toArray();

                $this->config['choices'] = $options;

                return $this->config['choices'];
            }
        }


        return throw new Exception("The options passed are not formed correctly for action '$this->name'");
    }

    /**
     * @return \Closure|null
     */
    public function getOptionsHandler(): ?Closure
    {
        return $this->optionsHandler ?? null;
    }

    /**
     * @return \Closure
     * @throws \Exception
     */
    public function getActionHandler(): Closure
    {
        if (is_null($this->actionHandler)) {
            throw new Exception("Handler for {$this->name} is missing.");
        }

        return $this->actionHandler;
    }

    /**
     * @param \Closure $handler
     * @return $this
     */
    public function setActionHandler(Closure $handler): self
    {
        $this->actionHandler = $handler;

        return $this;
    }

    /**
     * @param $primaryKey
     *
     * @return $this
     */
    public function setPrimaryKey($primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @param mixed $entityId
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getValue(mixed $entityId, string $key, mixed $default = null): mixed
    {
        return ($this->value[$entityId] ?? [])[$key] ?? $default;
    }

    /**
     * @param mixed $entityId
     * @param array $value
     * @return $this
     */
    public function setValue(mixed $entityId, array $value): static
    {
        $this->value[$entityId] = $value;

        return $this;
    }

    /**
     * @param mixed $entityId
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setValueKey(mixed $entityId, string $key, mixed $value): static
    {
        $this->value[$entityId][$key] = $value;

        return $this;
    }
}