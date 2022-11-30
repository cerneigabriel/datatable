<div
    x-cloak
    x-data="{ mobileView: tailwind.currentBreakpoint !== null && tailwind.currentBreakpointIsLessThan('lg') }"
    x-on:resize.window="mobileView = tailwind.currentBreakpoint !== null && tailwind.currentBreakpointIsLessThan('lg')"
    class="table-layout"
    :class="{ 'table-layout': !mobileView, 'table-mobile-layout': mobileView }"
>
    {{-- Table --}}
    <table class="table">
        @if(isset($thead))
            <thead {{ $tbody->attributes->class(['table-head']) }}>
                {{ $thead }}
            </thead>
        @endif

        @if(isset($tbody))
            <tbody {{ $tbody->attributes->class(['table-body']) }}>
                {{ $tbody }}
            </tbody>
        @endif

        @if(isset($tfoot))
            <tbody {{ $tfoot->attributes->class(['table-head']) }}>
                {{ $tfoot }}
            </tbody>
        @endif
    </table>
</div>

