<div class="uk-margin">
    @if($entity->modify)
        <div class="uk-padding-small uk-padding-remove-horizontal">
            Основной товар: @l($entity->_parent_modify()->title, 'oleus.shop_products.edit', ['p' => ['id' => $entity->_parent_modify()->id], 'attributes' => ['class' => 'uk-text-bold']])
        </div>
    @endif
    <div id="list-additionally-ingredients-select-items">
        @include('backend.partials.shop.product.modifications_table', ['_items' => $entity->_modifications(), 'product' => $entity])
    </div>
    @if($entity->modify == $entity->id)
        <div class="uk-clearfix uk-text-right">
            <div class="uk-button-group">
                @l('Добавить модификацию товара', 'oleus.shop_products.modify', ['p' => ['product' => $entity->id,
                'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success
                use-ajax']])
            </div>
        </div>
    @endif
</div>
