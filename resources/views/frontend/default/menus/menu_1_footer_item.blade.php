@php
    $_level = $_level ?? 0;
    $_level ++;
 $_device_type = wrap()->get('device.type');
@endphp
<li {!! render_attributes($_item['item']['wrapper']) !!}>
    {!! $_item['item']['prefix'] !!}
    @if($_item['item']['active'] || is_null($_item['item']['path']))
        <span class="">
             @if($_item['item']['icon'])
             <img data-src="{{ $_item['item']['icon'] }}" uk-img @if($_device_type != 'mobile') uk-svg @endif class="menu-icon uk-preserve uk-margin-small-right"
                  alt="{{ $_item['item']['title'] }}" height="30px" width="30px">
            @endif
            {!! $_item['item']['title'] !!}
        </span>
    @else
        <a {!! render_attributes($_item['item']['attributes']) !!}>
            @if($_item['item']['icon'])
            <img data-src="{{ $_item['item']['icon'] }}" uk-img @if($_device_type != 'mobile') uk-svg @endif class="menu-icon uk-preserve uk-margin-small-right"
                 alt="{{ $_item['item']['title'] }}" height="30px" width="30px">
            @endif
            {{ $_item['item']['title'] }}
        </a>
    @endif
    {{--@if($_item['children']->isNotEmpty())--}}
        {{--<ul class="sub-nav">--}}
            {{--@foreach($_item['children'] as $_sub_item_menu)--}}
                {{--@include('frontend.default.menus.menu_item', ['_item' => $_sub_item_menu, '_level' => $_level])--}}
            {{--@endforeach--}}
        {{--</ul>--}}
    {{--@endif--}}
    {!! $_item['item']['suffix'] !!}
</li>
