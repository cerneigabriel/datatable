<?php

namespace Modules\DataTable\Core\Columns;

use Carbon\Carbon;
use Exception;
use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class DatetimeColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class DatetimeColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'datetime';

    /** @var string */
    public string $format = 'Y-m-d H:i:s';

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

        try {
            $data = Carbon::parse($data)->format($this->format);
        } catch (Exception $exception) {
        }

        return $route ? "<a href='$route' class='link-info'>{$this->highlightStringInHtml($data)}</a>" : $this->highlightStringInHtml($data);
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }
}