@php
   global $wrap;
   $_basket = app('basket');
   $locale = $wrap['locale'] ?? DEFAULT_LOCALE;
   $_device_type = $wrap['device']['type'] ?? 'pc';
@endphp

<div id="basket-box">
    <a href="{{ $wrap['seo']['base_url'] . _r('page.shop_checkout') }}"
       rel="nofollow"
       class="bag {{ $_basket->exists ? ' not-empty' : ' uk-disabled' }}">
        {{--{!! $_basket->amount['format']['price'] !!}--}}
        <svg>
            <use xlink:href="#bag"></use>
        </svg>
        @if($_basket->exists)
            <span>
                {!! $_basket->quantity_in !!}
            </span>
            @else
            <span>0</span>
        @endif
    </a>
</div>
