@if($_categories)
    <div class="uk-margin">
        <h3 class="uk-heading-line uk-text-uppercase">
            <span>
                Категории
            </span>
        </h3>
        <div class="uk-grid">
            <div class="uk-width-4-5">
                @formField('categories', ['type' => 'select', 'label' => 'Доступные категории', 'value' =>
                $entity->_category->pluck('id'), 'values' => $_categories, 'class' => 'uk-select2', 'multiple' => TRUE,
                'options' => 'data-minimum-results-for-search="5"'])
            </div>
            <div class="uk-width-1-5">
                <button type="button"
                        id="form-field-categories-selection-button"
                        disabled
                        data-path="{{ _r('oleus.shop_products.categories_selection', ['shop_product' => $entity->id]) }}"
                        class="uk-button uk-button-color-amber uk-margin-medium-top uk-width-1-1">
                    <span uk-icon="icon: refresh"></span> Обновить
                </button>
            </div>
        </div>
    </div>
    <div class="uk-margin">
        {{--@formField('categories[]', ['type' => 'hidden', 'value' => 1])--}}
        <h3 class="uk-heading-line uk-text-uppercase">
            <span>
                Список параметров
            </span>
        </h3>
        <div id="list-category-params-items">
            @if($_params = $entity->getParamItemsFields())
                @php
                    $_items_output = $_params->sortByDesc('in_filter')->map(function ($_item) {
                    return $_item['markup'];
                    })->implode('');
                @endphp
                {!! $_items_output !!}
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Для заполнения параметров товара выберите категорию к которой он относится.
                </div>
            @endif
        </div>
    </div>
@else
    <div class="uk-margin uk-text-warning">Для указания параметров для товаров необходимо добавить категории</div>
@endif
