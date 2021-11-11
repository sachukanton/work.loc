@php
    $_default_locale = env('LOCALE');
@endphp
<div class="uk-grid uk-child-width-1-2">
    <div>
        @formField('param_item.min_value', ['type' => 'number', 'label' => '<span class="uk-text-bold">MIN</span> значение', 'value' => $_item->min_value])
    </div>
    <div>
        @formField('param_item.max_value', ['type' => 'number', 'label' => '<span class="uk-text-bold">MAX</span> значение', 'value' => $_item->max_value])
    </div>
</div>
@formField('param_item.step_value', ['type' => 'number', 'label' => 'Шаг изменения', 'value' => $_item->step_value, 'attributes' => ['step' => 0.01]])
@formField("param_item.unit_value", ['label' => 'Ед. измерения', 'value' => $_item->getTranslation('unit_value', $_default_locale)])
<h3 class="uk-heading-line">
    <span>Стиль оформления</span>
</h3>
@formField('param_item.style_id', ['label'=> 'ID элемента', 'value' => $_item->style_id])
@formField('param_item.style_class', ['label'=> 'CLASS элемента', 'value' => $_item->style_class])
@formField('param_item.attribute', ['type'   => 'textarea', 'label'  => 'Дополнительные атрибуты', 'value' => $_item->attribute,'attributes' => ['rows' => 3]])