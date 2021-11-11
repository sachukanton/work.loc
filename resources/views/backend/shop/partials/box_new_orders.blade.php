<div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
    <h2 class="uk-heading-line">
        <span>
            @lang('shop.titles.new_orders')
        </span>
    </h2>
    @include('backend.shop.partials.items_new_orders', ['_items' => $_others['new_orders']])
</div>
