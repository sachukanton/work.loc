<div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
    <h2 class="uk-heading-line">
        <span>
            @lang('shop.titles.last_complete_orders')
        </span>
    </h2>
    @include('backend.shop.partials.items_last_complete_orders', ['_items' => $_others['last_complete_orders']])
</div>
