<a
    href="{{ $action->getRoute($entity) }}"
    target="{{ $action->getTarget() }}"
    {!! $action->renderAttributes() !!}
>
    {!! $action->text !!}
</a>
