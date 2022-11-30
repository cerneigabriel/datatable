<?php

namespace Modules\DataTable\Core\Abstracts;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\DataTable\Core\Facades\Constraint;
use Modules\DataTable\Core\Interfaces\Abstracts\DataTableColumnInterface;
use Modules\DataTable\Core\Resolvers\EloquentAttributeResolver;
use Modules\DataTable\Core\Traits;

/**
 * Class DataTableColumn
 *
 * @package Modules\DataTable\Core\Abstracts
 */
abstract class DataTableColumn extends SerializableDataTable implements DataTableColumnInterface
{
    use Traits\HasRelationships;
    use Traits\HasRoute;
    use Traits\HasSizes;

    /** @var string */
    public string $type;

    /** @var string */
    public string $name;

    /** @var string */
    public string $attribute;

    /** @var string */
    public string $label;

    /** @var string $nullText */
    public string $nullText = '';

    /** @var Closure|null */
    public ?Closure $renderCallback = null;

    /** @var bool */
    public bool $renderCallbackSkipCustomFormatting = false;

    /** @var bool */
    public bool $searchable = true;

    /** @var bool */
    public bool $sortable = true;

    /** @var bool */
    public bool $filterable = false;

    /** @var \Closure|null */
    public ?Closure $visibilityResolver = null;

    /** @var string|null */
    public ?string $highlight_string = null;

    /**
     * @param string $name
     * @param string|null $attribute
     * @param string|null $label
     */
    public function __construct(string $name, string $attribute = null, string $label = null)
    {
        $this->mountSerialization();
        $this->name = $name;
        $this->attribute = $attribute ?? $name;
        $this->label = $this->makeLabel($label ?? $name);
    }

    /**
     * @param $name
     * @return string
     */
    protected function makeLabel($name): string
    {
        return ucwords(Str::replace(['-', '_', '.'], ' ', $name));
    }

    /**
     * @return string
     */
    public function getNullText(): string
    {
        return $this->nullText;
    }

    /**
     * @param string $nullText
     * @return $this
     */
    public function setNullText(string $nullText): static
    {
        $this->nullText = $nullText;

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $attribute
     * @return $this
     */
    public function setAttribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param \Closure $renderCallback
     * @param bool $skipCustomFormatting
     * @return $this
     */
    public function setRenderCallback(Closure $renderCallback, bool $skipCustomFormatting = false): self
    {
        $this->renderCallback = $renderCallback;
        $this->renderCallbackSkipCustomFormatting = $skipCustomFormatting;

        return $this;
    }

    /**
     * @param bool $searchable
     * @return $this
     */
    public function setSearchable(bool $searchable): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * @param bool $sortable
     * @return $this
     */
    public function setSortable(bool $sortable): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @param bool $filterable
     * @return $this
     */
    public function setFilterable(bool $filterable): self
    {
        $this->filterable = $filterable;

        return $this;
    }

    /**
     * @param bool|\Closure $resolver
     * @return $this
     */
    public function setVisibility(bool|Closure $resolver): self
    {
        $this->visibilityResolver = ($resolver instanceof Closure ? $resolver : function () use ($resolver) {
            return $resolver;
        });

        return $this;
    }

    /**
     * @param string|null $highlight_string
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @return $this
     */
    public function setHighlightString(?string $highlight_string, Builder|null $query = null): static
    {
        if (isset($highlight_string)) {
            $highlight_string = trim($highlight_string);
            if ((!$query || $this->queryable($query)) && $this->searchable && !empty($highlight_string)) {
                $this->highlight_string = $highlight_string;
            }
        }

        return $this;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function isVisible($entity): bool
    {
        if (is_null($this->visibilityResolver)) {
            return true;
        }

        return (bool)$this->visibilityResolver->call($this, $entity);
    }

    /**
     * @param $entity
     * @return string|null
     * @throws \Exception
     */
    public function render($entity): ?string
    {
        if (isset($this->renderCallback)) {
            if ($this->renderCallbackSkipCustomFormatting && $this->isRenderCallbackReturnTypeSupported($data = $this->resolveRenderCallback($entity))) {
                return (string)$data;
            }

            return $this->resolveData($this->resolveRenderCallback($entity), $entity);
        } else if (is_object($entity) || is_array($entity)) {
            return $this->resolveData(EloquentAttributeResolver::make($this->attribute, $entity)->extractData(), is_array($entity) ? (object)$entity : $entity);
        }

        return null;
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
            return static::unsupportedRenderCallbackReturnType($given_type, $this->attribute);
        }
    }

    /**
     * @static
     * @param string $given_type
     * @param string $columnAttribute
     * @return mixed
     */
    private static function unsupportedRenderCallbackReturnType(string $given_type, string $columnAttribute): mixed
    {
        return throw new InvalidArgumentException(
            "Given type '$given_type' cannot be converted to 'string'." .
            "Make sure your \$renderCallback for column '$columnAttribute' is returning 'string' or other supported types to perform the conversion or set \$renderCallbackSkipCustomFormatting parameter to 'false' when setting the \$renderCallback."
        );
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function resolveRenderCallback($entity): mixed
    {
        if (isset($this->renderCallback)) {
            return $this->renderCallback->call($this, $entity);
        }

        return null;
    }

    /**
     * @param $data
     * @param $entity
     * @return string
     * @throws \Exception
     */
    public function resolveData($data, $entity): string
    {
        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' target='{$this->getTarget()}' class='link-info'>$data</a>";
        }

        return $data;
    }

    /**
     * @param string $html
     * @return string
     */
    public function highlightStringInHtml(string $html): string
    {
        if (!is_null($this->highlight_string)) {
            return preg_replace('#'. preg_quote($this->highlight_string) .'#i', '<span class="bg-primary bg-opacity-70 text-white px-0.5">\\0</span>', $html);
        }

        return $html;
    }

    /**
     * @return \Modules\DataTable\Core\Abstracts\DataTableConstraint
     */
    public function makeConstraint(): DataTableConstraint
    {
        return Constraint::{$this->type}($this->name, $this->attribute, $this->label);
    }
}