@php
    $_default_locale = env('LOCALE');
@endphp
<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: more_horiz"></span>
            </th>
            <th>
                Название товара или категории товаров
            </th>
            <th class="uk-width-medium">
                Тип
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: laptop_windows"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: delete"></span>
            </th>
    </thead>
    <tbody>
        @foreach($_items as $_key => $_item)
            @php
                $_type_item = $_item instanceof \App\Models\Shop\Product ? 'product' : 'category';
            @endphp
            <tr>
                <td class="uk-text-center uk-text-bold">
                    {{ $_item->id }}
                </td>
                <td>
                    {{ $_item->getTranslation('title', $_default_locale) }}
                </td>
                <td>
                    @if($_type_item == 'product')
                        Товар
                    @else
                        Категория товара
                    @endif
                </td>
                <td class="uk-text-center">
                    {!! $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
                </td>
                <td>
                    @l('', 'oleus.shop_products.related', ['p' => ['type' => $type, 'shop_product' => $entity, 'action' => 'destroy', 'shop_product_related'=> $_key], 'attributes' => ['class' => 'use-ajax uk-button-danger uk-button uk-button-small', 'uk-icon' => 'icon: delete']])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>