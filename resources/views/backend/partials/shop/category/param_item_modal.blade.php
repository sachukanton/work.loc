<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      method="POST"
      id="modal-category-param-item-form"
      action="{{ _r('oleus.shop_categories.param', ['category' => $entity, 'action' => 'save']) }}">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">Добавить параметры в категорию</h2>
    </div>
    <div class="uk-modal-body">
        @formField('params', ['type' => 'select', 'label' => 'Доступные параметры', 'value' => 0, 'values' => $_items, 'form_id' => 'modal-category-param-item-form', 'class' => 'uk-select2', 'multiple' => TRUE, 'options'  => 'data-minimum-results-for-search="5"'])
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