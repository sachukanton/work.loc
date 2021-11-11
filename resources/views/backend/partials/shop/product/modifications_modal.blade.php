<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      method="POST"
      action="{{ _r('oleus.shop_products.modify', ['product' => $product, 'action' => 'save']) }}">
    <input type="hidden"
           value="{{ $product->id }}"
           name="item[product_id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ 'Добавление модификации товара' }}</h2>
    </div>
    <div class="uk-modal-body">
        @formField('item.type', ['values' => ['exists' => 'Выбрать существующий'], 'type' => 'radio'])
        @formField('item.exists', ['type' => 'select', 'label' => 'Созданные товары', 'values' =>
        $product->_categories_products(), 'class' => 'uk-select2'])
        <hr class="uk-divider-icon">
        @formField('item.type', ['values' => ['new' => 'Создать новый'], 'selected' => 'new', 'type' => 'radio'])
        @formField('item.new_title', ['label' => 'Название нового товара'])
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-success use-ajax uk-border-rounded uk-margin-small-right">
            Сохранить
        </button>
        <button class="uk-button uk-button-secondary uk-modal-close uk-button-icon uk-border-rounded"
                uk-icon="icon: clearclose"
                type="button"></button>
    </div>
</form>
