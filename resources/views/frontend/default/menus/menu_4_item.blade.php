@php
    $_level = $_level ?? 0;
    $_level ++;
@endphp
<li {!! render_attributes($_item['item']['wrapper']) !!}>
    {!! $_item['item']['prefix'] !!}
    @if($_item['item']['active'] || is_null($_item['item']['path']))
        <span class="uk-navbar-toggle uk-text-primary">{!! $_item['item']['title'] !!}</span>
    @else
        <a {!! render_attributes($_item['item']['attributes']) !!}>
            {{ $_item['item']['title'] }}
        </a>
    @endif
    @if($_item['children']->isNotEmpty())
        <ul class="sub-nav">
            @foreach($_item['children'] as $_sub_item_menu)
                @include('backend.base.menu_item', ['_item' => $_sub_item_menu, '_level' => $_level])
            @endforeach
        </ul>
    @endif
    {!! $_item['item']['suffix'] !!}
</li>
