@php
    $_level = $_level ?? 0;
    $_level ++;
//  if($_level == 1) {
//        $_item['item']['attributes']['class'][] = 'uk-text-bold uk-text-uppercase';
//    }
    $_item['item']['wrapper']['class'][] = "uk-flex";
    $_item['item']['attributes']['class'][] = "category__item";
    $_device_type = wrap()->get('device.type');
@endphp
    @if($_item['item']['active'] || is_null($_item['item']['path']))
        <div class="category__item">
            <h3>
                {!! $_item['item']['title'] !!}
            </h3>
            <div class="category__item--img">
                <img data-src="{{ $_item['item']['icon'] }}" uk-img uk-svg class="menu-icon uk-preserve"
                     alt="{{ $_item['item']['title'] }}">
            </div>
        </div>
    @else
        <a {!! render_attributes($_item['item']['attributes']) !!}>
            <h3>
                {!! $_item['item']['title'] !!}
            </h3>
            <div class="category__item--img">
                <img data-src="{{ $_item['item']['icon'] }}" uk-img uk-svg class="menu-icon uk-preserve"
                     alt="{{ $_item['item']['title'] }}">
            </div>
        </a>
    @endif
    {!! $_item['item']['suffix'] !!}
</li>
