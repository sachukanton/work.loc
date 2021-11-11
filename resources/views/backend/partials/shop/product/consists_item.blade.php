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
                Название товара
            </th>
            <th class="uk-width-small uk-text-center">
                Количество в составе, шт
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
            <tr>
                <td class="uk-text-center uk-text-bold">
                    {{ $_item->id }}
                </td>
                <td>
                    {!! _l($_item->getTranslation('title', $_default_locale), 'oleus.shop_products.edit', ['p' => [$_item], 'attriibutes' => ['target' => '_blank']]) !!}
                </td>
                <td class="uk-text-center uk-text-bold">
                    {{ $_item->quantity }}
                </td>
                <td class="uk-text-center">
                    {!! $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
                </td>
                <td>
                    @l('', 'oleus.shop_products.consist', ['p' => ['shop_product' => $entity, 'action' => 'destroy', 'shop_product_consist'=> $_key], 'attributes' => ['class' => 'use-ajax uk-button-danger uk-button uk-button-small', 'uk-icon' => 'icon: delete']])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
