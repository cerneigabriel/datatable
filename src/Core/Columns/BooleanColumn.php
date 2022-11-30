<?php

namespace Modules\DataTable\Core\Columns;

use Modules\DataTable\Core\Abstracts\DataTableColumn;
use Modules\DataTable\Core\Abstracts\DataTableConstraint;
use Modules\DataTable\Core\Facades\Constraint;

/**
 * Class BooleanColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class BooleanColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'boolean';

    /** @var string */
    public string $true_string = 'Yes';

    /** @var string */
    public string $false_string = 'No';

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
        $data = $data ? $this->true_string : $this->false_string;

        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' class='link-info'>{$this->highlightStringInHtml($data)}</a>";
        }

        return $this->highlightStringInHtml($data);
    }

    /**
     * @param string $false_string
     * @return $this
     */
    public function setFalseString(string $false_string): self
    {
        $this->false_string = trim($false_string);

        return $this;
    }

    /**
     * @param string $true_string
     * @return $this
     */
    public function setTrueString(string $true_string): self
    {
        $this->true_string = trim($true_string);

        return $this;
    }

    /**
     * @return \Modules\DataTable\Core\Abstracts\DataTableConstraint
     */
    public function makeConstraint(): DataTableConstraint
    {
        return Constraint::boolean($this->name, $this->attribute, $this->label)->setTrueString($this->true_string)->setFalseString($this->false_string);
    }
}