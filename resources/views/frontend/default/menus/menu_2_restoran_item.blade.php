@php
    $_level = $_level ?? 0;
    $_level ++;
    $_device_type = wrap()->get('device.type');
    $_item['item']['attributes']['class'][] = 'uk-position-relative uk-overflow-hidden uk-flex uk-flex-center uk-flex-middle uk-flex-column uk-overflow-hidden uk-item';
@endphp

<div {!! render_attributes($_item['item']['wrapper']) !!}>
    {!! $_item['item']['prefix'] !!}
    {{--@if($_item['item']['active'] || is_null($_item['item']['path']))--}}
        {{--<span class="uk-navbar-toggle uk-text-uppercase">--}}
             {{--@if($_item['item']['icon'])--}}
             {{--<img data-src="{{ $_item['item']['icon'] }}" uk-img @if($_device_type != 'mobile') uk-svg @endif class="menu-icon uk-preserve uk-margin-small-right"--}}
                  {{--alt="{{ $_item['item']['title'] }}" height="30px" width="30px">--}}
            {{--@endif--}}
            {{--{!! $_item['item']['title'] !!}--}}
        {{--</span>--}}
    {{--@else--}}
        <a {!! render_attributes($_item['item']['attributes']) !!}>
            @if($_item['item']['preview_385'])
                {!! $_item['item']['preview_385'] !!}
            @endif
                {{--@if($_item['item']['preview'])--}}
                    {{--{!! $_item['item']['preview'] !!}--}}
                    {{--<img data-src="{{ $_item['item']['preview'] }}" uk-img uk-cover @if($_device_type != 'mobile') uk-svg @endif class="menu-icon uk-preserve uk-margin-small-right"--}}
                         {{--alt="{{ $_item['item']['title'] }}">--}}
                {{--@endif--}}
                @if($_item['item']['icon'])
                    <span class="uk-flex uk-flex-center uk-flex-middle" @if($_device_type == 'mobile') style="background-color: rgba(254, 222, 161, .76);" @endif>
                        <img data-src="{{ $_item['item']['icon'] }}" uk-img @if($_device_type != 'mobile') uk-svg @endif class="menu-icon uk-preserve"
                             alt="{{ $_item['item']['title'] }}">
                    </span>
                @endif
                <span class="uk-position-relative uk-text-uppercase title">
                    {{ $_item['item']['title'] }}
                </span>
        </a>
    {{--@endif--}}
    {{--@if($_item['children']->isNotEmpty())--}}
        {{--<ul class="sub-nav">--}}
            {{--@foreach($_item['children'] as $_sub_item_menu)--}}
                {{--@include('frontend.default.menus.menu_item', ['_item' => $_sub_item_menu, '_level' => $_level])--}}
            {{--@endforeach--}}
        {{--</ul>--}}
    {{--@endif--}}
    {!! $_item['item']['suffix'] !!}
</div>
