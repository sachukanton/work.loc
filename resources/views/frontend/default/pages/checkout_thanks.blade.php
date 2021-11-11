@extends('frontend.default.index')

@section('content')
    <section class="thank_you">
    <div class="container">
        <div class="thank_you__wrapper">
            <h2>{{variable('tnx_title')}}</h2>
            <img uk-img="data-src:{{ formalize_path('template/images/thank_you.png') }}" alt="tnx">
            <p>{{variable('tnx_text')}}</p>
            <div class="thank_you--social">
                @foreach($_wrap['loads']['contacts']['socials'] as $_key => $_social)
                    @if($_social)
                        <a href="{{ $_social }}"
                           class=" social__item {{ $_key }}"
                           target="_blank">
                            <svg>
                                <use xlink:href="#{{ $_key }}"></use>
                            </svg>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
    @if($_item->order)
        <script>
            if (typeof gtag == 'function') {
                gtag('event', 'conversion', {
                    'send_to': 'AW-467931757/nIpVCLy80u0BEO2kkN8B',
                    'value': {{ (float) ($_item->order->discount ? $_item->order->amount_less_discount : $_item->order->amount) }},
                    'currency': 'UAH',
                    'transaction_id': '{{ $_item->order->id }}'
                });
            }
            @if($_item->fb)
                {!! $_item->fb !!}
            @endif
            @if($_item->eCommerce)
            if (typeof gtag == 'function') {
                gtag('event', 'purchase', {!! json_encode($_item->eCommerce) !!});
            }
            @endif
        </script>
    @endif
@endsection
