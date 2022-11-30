<div x-data="{ showNotification: @entangle('showNotifications'), hideNotifications() { $wire.showNotifications = false; } }">
    @if($showNotifications && $notifications->isNotEmpty())
        @foreach($notifications as $notification)
            <x-notification
                xshow="showNotification"
                wire:key="notification_{{ $notification->level }}"
                after-disappear="{{ $notification->level === $notifications->max('level') ? 'hideNotifications();' : '' }}"
                :type="$notification->type"
                :message="$notification->message"
                :level="$notification->level"
                :delay="($notification->level * 200)"
                :duration="$notification->timeout"
            />
        @endforeach
    @endif
</div>