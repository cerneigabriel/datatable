<?php

namespace Modules\DataTable\Core\Collections;

use Illuminate\Support\Collection;
use Modules\DataTable\Core\Abstracts\DataTableNotification;

/**
 * Class NotificationsCollection
 *
 * @package Modules\DataTable\Core\Collections
 */
class NotificationsCollection extends Collection
{
    /**
     * @throws \Throwable
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        foreach ($this->items as $key => $item) {
            if (is_array($item)) {
                $item = (object)$item;
            }
            if (isset($item->message)) {
                if (!($item instanceof DataTableNotification)) {
                    $this->items[$key] = new DataTableNotification(
                        type: $item->type ?? DataTableNotification::DEFAULT_TYPE,
                        message: $item->message ?? '',
                        timeout: $item->timeout ?? DataTableNotification::DEFAULT_TIMEOUT,
                        level: $key
                    );
                } else {
                    $item->setLevel($key);
                }
            }
        }
    }

    /**
     * @return $this
     */
    public function reorder()
    {
        foreach ($this->sortBy(fn($item) => $item->level) as $key => $item) {
            $item->setLevel($key);
        }

        return $this;
    }
}