<?php

namespace Modules\DataTable\Core\Columns;

use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class TextColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class IDColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'text';

    /** @var string */
    public string $name = 'id';

    /** @var string */
    public string $attribute = 'id';

    /** @var string */
    public string $label = 'ID';

    /** @var bool */
    public bool $filterable = true;

    /** @var string|null */
    public ?string $min_width = '90px';

    /** @var string|null */
    public ?string $width = '90px';

    /** @var string|null */
    public ?string $max_width = '90px';

    /**
     * @param string|null $name
     * @param string|null $attribute
     * @param string|null $label
     */
    public function __construct(string $name = null, string $attribute = null, string $label = null)
    {
        parent::__construct($name ?? $this->name, $attribute ?? $name ?? $this->attribute ?? $this->name, $label ?? $this->label);
    }

    /**
     * Resolve Data
     *
     * @param $data
     * @param $entity
     * @return string
     * @throws \Exception
     */
    public function resolveData($data, $entity): string
    {
        $data = $data ?? $this->getNullText();

        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' class='link-info'>{$this->highlightStringInHtml($data)}</a>";
        }

        return $this->highlightStringInHtml($data);
    }
}