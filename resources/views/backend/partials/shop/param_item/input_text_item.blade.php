@php
    $_default_locale = env('LOCALE');
@endphp
@formField("param_item.unit_value", ['label' => 'Ед. измерения', 'value' => $item->getTranslation('unit_value', $_default_locale)])
<h3 class="uk-heading-line">
    <span>Стиль оформления</span>
</h3>
@formField('param_item.style_id', ['label'=> 'ID элемента', 'value' => $item->style_id])
@formField('param_item.style_class', ['label'=> 'CLASS элемента', 'value' => $item->style_class])
@formField('param_item.attribute', ['type'   => 'textarea', 'label'  => 'Дополнительные атрибуты', 'value' => $item->attribute,'attributes' => ['rows' => 3]])
