<div class="uk-margin">
    <div id="list-filter-pages-items">
        @isset($_items)
            @if($_items->isNotEmpty())
                @include('backend.partials.shop.filter_page.page_item', compact('_items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        @l('Добавить страницу', 'oleus.shop_filter_pages.page', ['p' => ['shop_filter_page' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
    </div>
</div>