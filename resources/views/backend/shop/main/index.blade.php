@extends('backend.index')

@section('content')
    <article class="uk-article">
        @include('pharmacy.partials.box_new_orders')
        @include('pharmacy.partials.box_last_complete_orders')
        <div id="load-update-order-lists"></div>
        <script>
            window.update_order_lists = true;
            window.update_order_lists_last_create_at = null;
        </script>
    </article>
@endsection