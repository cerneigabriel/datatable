<td {{ $attributes->class(['table-head-cell'])->except('text') }}>
    @if(isset($attributes['text']))
        {{ $attributes['text'] }}
    @else
        {{ $slot }}
    @endif
</td>