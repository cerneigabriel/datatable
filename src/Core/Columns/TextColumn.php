<?php

namespace Modules\DataTable\Core\Columns;

use App\Models\Publication;
use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class TextColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class TextColumn extends DataTableColumn
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

        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' class='link-info'>{$this->highlightStringInHtml($data)}</a>";
        }

        return $this->highlightStringInHtml($data);
    }
}