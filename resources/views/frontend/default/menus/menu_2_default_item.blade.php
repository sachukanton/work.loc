@php
    $_level = $_level ?? 0;
    $_level ++;
 $_device_type = wrap()->get('device.type');
@endphp
<li {!! render_attributes($_item['item']['wrapper']) !!}>
    {!! $_item['item']['prefix'] !!}
    @if($_item['item']['active'] || is_null($_item['item']['path']))
        <span class="uk-navbar-toggle uk-text-uppercase">
            {!! $_item['item']['title'] !!}
        </span>
    @else
        <a {!! render_attributes($_item['item']['attributes']) !!}>
            {{ $_item['item']['title'] }}
        </a>
    @endif
    {!! $_item['item']['suffix'] !!}
</li>
