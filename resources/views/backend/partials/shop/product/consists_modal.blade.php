@php
    $_default_locale = env('LOCALE');
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      method="POST"
      id="modal-product-consist-item-form"
      action="{{ _r('oleus.shop_products.consist', [ 'shop_product' => $entity, 'action' => 'save']) }}">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">Добавить товар</h2>
    </div>
    <div class="uk-modal-body">
        @formField('consist_product', ['type' => 'autocomplete', 'label' => 'Товар', 'class' => 'uk-autocomplete', 'attributes' => ['data-url' => _r('oleus.shop_products.consist_entity', ['shop_product' => $entity]), 'data-value' => 'name'], 'help' => 'Начните вводить название товара', 'form_id' => 'modal-product-consist-item-form'])
        @formField('quantity', ['type' => 'number', 'label' => 'Количество в составе, шт', 'attributes' => ['min' => 1, 'step' => 1], 'value' => 1, 'form_id' => 'modal-product-consist-item-form'])
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
