<?php

namespace Modules\DataTable\Core\Columns;

use Illuminate\Support\Collection;
use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class CountColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class CountColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'count';

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
        $data = !empty($data) ? (is_string($data) ? $data : ($data instanceOf Collection ? $data->count() : (is_array($data) ? count($data) : $this->getNullText()))) : $this->getNullText();

        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' class='link-info'>{$this->highlightStringInHtml($data)}</a>";
        }

        return $this->highlightStringInHtml($data);
    }
}