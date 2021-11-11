@foreach($_items as $_item)
    <div class="uk-flex uk-flex-middle">
        <div class="uk-width-expand">
            {!! _l($_item->title, 'oleus.banners.edit', ['p' => [$_item], 'attributes' => ['target' => '_blank']]) !!}
        </div>
        <div class="uk-width-auto">
            @l('', 'oleus.shop_categories.banner', ['p' => ['shop_category' => $entity->id, 'action' => 'destroy', 'id' => $_item], 'attributes' => ['class' => 'use-ajax uk-button-danger uk-button', 'uk-icon' => 'icon: delete']])
        </div>
    </div>
@endforeach
