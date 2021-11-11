<div class="uk-margin">
    <div id="list-category-params-items">
        @isset($_items)
            @if($_items->isNotEmpty())
                @php
                    $_select_items = $_items->filter(function($p){
                        return $p->type == 'select';
                    })->pluck('title', 'id');
                @endphp
                @if($_select_items->isNotEmpty())
                    @php($_select_items->prepend('- Выбрать -', ''))
                    <div class="uk-margin">
                        <h3 class="uk-heading-line uk-text-uppercase">
                            <span>Параметр для модификаций</span>
                        </h3>
                        <div>
                            @formField('modify_param', ['type' => 'select', 'selected' => $entity->modify_param,
                            'values' => $_select_items, 'class' => 'uk-select2'])
                        </div>
                    </div>
                    <hr class="uk-divider-icon">
                @endif
                @include('backend.partials.shop.category.param_item', compact('_items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        @l('Добавить параметр', 'oleus.shop_categories.param', ['p' => ['shop_category' => $entity->id, 'action' =>
        'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
    </div>
</div>
