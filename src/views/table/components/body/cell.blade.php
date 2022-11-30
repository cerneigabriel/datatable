<td {{ $attributes->class(['table-body-cell'])->except('text') }}>
    @if(isset($attributes['text']))
        {{ $attributes['text'] }}
    @else
        {{ $slot }}
    @endif
</td>