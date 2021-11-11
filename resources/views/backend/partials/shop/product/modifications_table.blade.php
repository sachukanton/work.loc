@php
    $_user = Auth::user();
@endphp
@if($_items->isNotEmpty())
    <table
        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
        <thead>
            <tr>
                <th class="uk-width-xsmall">ID</th>
                <th>Название товара</th>
                <th class="uk-width-xsmall uk-text-center">
                    <span uk-icon="icon: sort_by_alpha"></span>
                </th>
                <th class="uk-width-xsmall uk-text-center">
                    <span uk-icon="icon: laptop_windows"></span>
                </th>
                @if ($_user->hasPermissionTo('shop_products_update'))
                    <th class="uk-width-xsmall"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($_items as $_modification)
                <tr>
                    <td class="uk-text-center uk-text-bold">{{ $_modification->id }}</td>
                    <td>{!! _l($_modification->title, 'oleus.shop_products.edit', ['p' => ['id' => $_modification->id]]) !!}</td>
                    <td class="uk-text-center">
                        {{ $_modification->sort }}
                    </td>
                    <td class="uk-text-center">
                        {!! $_modification->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
                    </td>
                    @if ($_user->hasPermissionTo('shop_products_update'))
                        <th class="uk-width-xsmall">
                            {!! _l('', 'oleus.shop_products.modify', ['p' => ['product' => $product, 'action' => 'destroy', $_modification->id], 'attributes' => ['class' => 'use-ajax uk-icon-button uk-button-danger uk-overflow-hidden', 'uk-icon' => 'icon: clearclose']]) !!}
                        </th>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="uk-alert uk-alert-warning uk-border-rounded uk-margin-small-top">
        Список пуст
    </div>
@endif
