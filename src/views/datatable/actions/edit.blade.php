<a
    href="{{ $action->getRoute($entity) }}"
    target="{{ $action->getTarget() }}"
    {!! $action->addClass('whitespace-nowrap')->renderAttributes() !!}
>
    {!! $action->text !!}
</a>