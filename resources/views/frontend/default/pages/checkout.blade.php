@php
    global $wrap;
@endphp

@extends('frontend.default.index')
@section('content')
    <div class="container">
        {{--@include('frontend.default.partials.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])--}}
        <section class="cart">
            <a href="{{ url()->previous() }}"
               class="goback">
                <svg>
                    <use xlink:href="#left"></use>
                </svg>
                <h6>{!! variable('back') !!}</h6>
            </a>
            @include('frontend.default.shops.gifts')
            <div class="cart__main">
                <div class="cart__main--items">
                    <h4>@lang('shop.titles.your_order')</h4>
                    {!! $_item->checkoutProductsOutput !!}
                    {{-- <div id="checkout-order-delivery-amount">
                        @if($_basket->quantity_in && ($delivery_string = $_basket->showDeliveryString()))
                            {!! $delivery_string !!}
                        @endif
                    </div> --}}

               </div>
                <div class="cart__main--form">
                    <h4>{!! $wrap['page']['title'] !!}</h4>
                    {!! $_item->checkoutFormOutput !!}
                </div>
            </div>


        </section>
        <div class="recommend-order">
            <load-component
                entity="shop_product_view_list_recommended_checkout"
                options=""></load-component>
        </div>
    </div>
@endsection

@push('scripts')
    {{--<link href="/template/css/air-datepicker.min.css" rel="stylesheet">--}}

    <link href="/dashboard/css/air-datepicker.min.css"
          rel="stylesheet">
    {{--<script src="/template/js/select2.min.js"--}}
    {{--type="text/javascript"></script>--}}
    {{--<script src="/dashboard/js/air-datepicker.min.js"--}}
    {{--type="text/javascript"></script>--}}
    {{--<script src="resources/js/jquery.inputmask.bundle.min.js"--}}
    {{--type="text/javascript"></script>--}}
    {{--<script src="/template/js/app.js"--}}
    {{--type="text/javascript"></script>--}}
    <script src="/template/js/vue.js"
            type="text/javascript"></script>
    {{--    <script src="/template/js/checkout_part.js"--}}
    {{--            type="text/javascript"></script>--}}
    <script type="text/javascript">
        if (typeof fbq == 'function') {
            var a = {};
            if (typeof FbData == 'object') a = Object.assign(a, FbData);
            a.content_type = 'product';
            a.content_ids = {!! isset($_basket->sku_list) ? $_basket->sku_list : NULL !!};
            a.value = {{ isset($_basket->amount) ? $_basket->amount['original']['price'] : NULL }};
            a.currency = 'UAH';
            a.num_items = {{ isset($_basket->quantity_in) ? $_basket->quantity_in : NULL }};
            fbq('track', 'InitiateCheckout', a);
        }
    </script>
@endpush
