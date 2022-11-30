<?php

namespace Modules\DataTable\Core\Columns;

use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class EmailColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class EmailColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'text';

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

        $route = $this->getRoute($entity);

        return "<a href='" . ($route ?? "mailto:$data") . "' class='link-info'>{$this->highlightStringInHtml($data)}</a>";
    }
}