<?php

namespace Modules\DataTable\Core\Columns;

use Closure;
use Exception;
use Illuminate\Support\Collection;
use Modules\DataTable\Core\Abstracts\DataTableColumn;

/**
 * Class ListColumn
 *
 * @package Modules\DataTable\Core\Columns
 */
class ListColumn extends DataTableColumn
{
    /** @var string */
    public string $type = 'list';

    /** @var string */
    public string $separator = ', ';

    public string $beforeString = '';

    public string $afterString = '';

    /** @var \Closure|null */
    public ?Closure $mapHandler = null;

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
        if (!empty($data)) {
            if ($data instanceOf Collection) {
                $data = $this->formatList($data);
            } elseif (is_array($data)) {
                $data = $this->formatList(collect($data));
            } else {
                $data = $this->getNullText();
            }
        } else {
            $data = $this->getNullText();
        }


        if ($route = $this->getRoute($entity)) {
            return "<a href='$route' class='link-info'>{$this->highlightStringInHtml($data)}</a>";
        }

        return $this->highlightStringInHtml($data);
    }

    /**
     * @param string $separator
     * @return $this
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @param string $beforeString
     * @return $this
     */
    public function setBeforeString(string $beforeString): self
    {
        $this->beforeString = $beforeString;

        return $this;
    }

    /**
     * @param string $afterString
     * @return $this
     */
    public function setAfterString(string $afterString): self
    {
        $this->afterString = $afterString;

        return $this;
    }

    /**
     * @param \Closure $handler
     *
     * @return $this
     */
    public function setMapHandler(Closure $handler): self
    {
        $this->mapHandler = $handler;

        return $this;
    }

    /**
     * @return \Closure|null
     */
    public function getMapHandler(): ?Closure
    {
        return $this->mapHandler ?? null;
    }

    private function formatList(Collection $data)
    {
        return $this->beforeString . $this->map($data)->implode($this->separator) . $this->afterString;
    }

    private function map(Collection $collection)
    {
        if ($this->getMapHandler() && ($data = $collection->map($this->getMapHandler())) instanceof Collection) {
            $collection = $data;
        }

        return $collection;
    }
}