<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      method="POST"
      id="modal-filter-page-item-form"
      action="{{ _r('oleus.shop_filter_pages.page', ['shop_filter_page' => $entity, 'action' => 'save']) }}">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">Добавить связанную страницу</h2>
    </div>
    <div class="uk-modal-body">
        @formField('pages', ['type' => 'select', 'label' => 'Доступные страницы', 'value' => 0, 'values' => $_items, 'form_id' => 'modal-filter-page-item-form', 'class' => 'uk-select2', 'multiple' => TRUE, 'options'  => 'data-minimum-results-for-search="5"'])
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