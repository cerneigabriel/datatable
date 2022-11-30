<div {{ $attributes }}>
    @if (isset($title) || isset($header))
        <div class="flex items-center justify-between gap-4 pb-7">
            <div class="flex items-center gap-4">
                <h2 class="page_title w-max whitespace-nowrap">{{ $title ?? '' }}</h2>
            </div>

            @if(isset($header))
                <div {{ $header->attributes->class(['flex', 'items-center', 'gap-4']) }}>
                    {{ $header }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}

    @if (isset($footer))
        <div {{ $footer->attributes->class(['flex', 'items-center', 'justify-between', 'gap-4', 'pb-7']) }}>
            {{ $footer }}
        </div>
    @endif
</div>
