@php
    $_default_locale = env('LOCALE');
@endphp
@foreach($_items as $_item)
    <div class="uk-margin">
        <h3 class="uk-heading-line uk-text-uppercase">
            <span>
                {{ $_item->getTranslation('title', $_default_locale) }}
                @switch($_item->type)
                    @case('select')
                    <span class="uk-text-color-orange uk-text-thin">(Выбор из списка)</span>
                    @break
                    @case('input_number')
                    <span class="uk-text-color-orange uk-text-thin">(Числовое поле)</span>
                    @break
                    @case('input_text')
                    <span class="uk-text-color-orange uk-text-thin">(Текстовое поле)</span>
                    @break
                @endswitch
            </span>
        </h3>
        <div class="uk-grid">
            <div class="uk-width-expand">
                <div class="uk-grid uk-child-width-1-2 uk-grid-divider">
                    @if($_item->type == 'select')
                        <div>
                            @formField("params.{$_item->id}.type", ['type' => 'radio', 'label' => 'Выбор параметра', 'selected' => $_item->pivot->type, 'values' => ['one' => 'Одиночный (выбор только одной опции)', 'multiple' => 'Множественный (выбор нескольких опций)']])
                            @formField("params.{$_item->id}.condition", ['type' => 'radio', 'label' => 'Оператор используемый при фильтрации', 'selected' => $_item->pivot->condition, 'values' => ['or' => 'ИЛИ (товары имеющие одну из выбранных опций параметра)', 'and' => 'И (товары имеющие все выбранные опции параметра)']])
                        </div>
                        <div>
                            @formField("params.{$_item->id}.sort", ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->pivot->sort])
                            @formField("params.{$_item->id}.visible_in_filter", ['type' => 'checkbox', 'values' => [1 => 'Использовать в фильтре'], 'selected' => $_item->pivot->visible_in_filter])
                            @formField("params.{$_item->id}.collapse", ['type' => 'checkbox', 'values' => [1 => 'Свернуть в фильтре изначально'], 'selected' => $_item->pivot->collapse])
                        </div>
                    @else
                        <div>
                            @formField("params.{$_item->id}.sort", ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->pivot->sort])
                        </div>
                    @endif
                </div>
            </div>
            <div class="uk-width-auto">
                @l('', 'oleus.shop_categories.param', ['p' => ['shop_category' => $entity->id, 'action' => 'destroy', 'id' => $_item], 'attributes' => ['class' => 'use-ajax uk-button-danger uk-button', 'uk-icon' => 'icon: delete']])
            </div>
        </div>
    </div>
@endforeach