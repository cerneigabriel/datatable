<?php

namespace Modules\DataTable\Core\Services;

use Illuminate\Support\Facades\Session;
use Modules\DataTable\Core\Abstracts\DataTableNotification;
use Modules\DataTable\Core\Collections\NotificationsCollection;

/**
 * Class Notify
 *
 * @package Modules\DataTable\Core\Services
 */
class Notify
{
    public const NOTIFICATIONS_KEY = 'notifications';

    /** @var \Modules\DataTable\Core\Collections\NotificationsCollection */
    private static NotificationsCollection $notifications;


    /**
     * @static
     * @param string $message
     * @param int $timeout
     * @return bool
     * @throws \Throwable
     */
    public static function success(string $message, int $timeout = DataTableNotification::DEFAULT_TIMEOUT): bool
    {
        static::boot();

        $notification = new DataTableNotification(DataTableNotification::SUCCESS, $message, $timeout);

        static::$notifications->push($notification);

        static::remember();

        return true;
    }

    /**
     * @static
     * @return void
     */
    private static function boot(): void
    {
        if (!isset(static::$notifications)) static::$notifications = NotificationsCollection::make(Session::get(self::NOTIFICATIONS_KEY, []));
    }

    /**
     * @static
     * @return void
     */
    private static function remember(): void
    {
        static::$notifications = static::$notifications->reorder();
        Session::put(self::NOTIFICATIONS_KEY, static::$notifications);
    }

    /**
     * @static
     * @param string $message
     * @param int $timeout
     * @return bool
     * @throws \Throwable
     */
    public static function warning(string $message, int $timeout = DataTableNotification::DEFAULT_TIMEOUT): bool
    {
        static::boot();

        $notification = new DataTableNotification(DataTableNotification::WARNING, $message, $timeout);

        static::$notifications->add($notification);

        static::remember();

        return true;
    }

    /**
     * @static
     * @return \Modules\DataTable\Core\Collections\NotificationsCollection
     */
    public static function getNotifications(): NotificationsCollection
    {
        static::boot();

        return static::$notifications ?? NotificationsCollection::make();
    }

    /**
     * @static
     * @param int $level
     * @return void
     */
    public static function removeNotification(int $level): void
    {
        static::boot();

        static::$notifications = static::$notifications->reject(fn($notification) => $notification->level === $level);

        static::remember();
    }

    /**
     * @static
     * @param int|null $minLevel
     * @param int|null $maxLevel
     * @return void
     */
    public static function removeNotifications(?int $minLevel = null, ?int $maxLevel = null): void
    {
        static::boot();

        static::$notifications = static::$notifications->reject(function ($notification) use ($minLevel, $maxLevel) {
            return
                $notification->level >= (is_null($minLevel) || $minLevel < 0 ? static::$notifications->min('level') ?? 0 : $minLevel) &&
                $notification->level <= (is_null($maxLevel) || $maxLevel <= 0 ? static::$notifications->max('level') ?? 0 : $maxLevel);
        });

        static::remember();
    }
}