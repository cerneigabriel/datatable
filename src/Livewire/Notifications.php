<?php

namespace Modules\DataTable\Livewire;

use Livewire\Component;
use Modules\DataTable\Core\Collections\NotificationsCollection;
use Modules\DataTable\Core\Services\Notify;

/**
 * Class Notifications
 *
 * @package Modules\DataTable\Livewire
 */
class Notifications extends Component
{
    /** @var bool */
    public bool $showNotifications = false;

    /** @var string[] */
    protected $listeners = ['notify'];

    /**
     * @return void
     */
    public function notify(): void
    {
        $this->showNotifications = true;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->showNotifications) {
            $notifications = NotificationsCollection::make(Notify::getNotifications());
        }
        Notify::removeNotifications();

        return view('datatable::notifications', ['notifications' => $notifications ?? []]);
    }
}
