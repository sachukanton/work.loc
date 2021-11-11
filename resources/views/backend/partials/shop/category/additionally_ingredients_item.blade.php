<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th>Ингредиент</th>
            <th class="uk-width-small">Артикул</th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: local_pizza"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: attach_money"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: sort_by_alpha"></span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($_items as $_item)
            <tr>
                <td>
                    {!! _l($_item->_ingredient->title, 'oleus.shop_categories.additional_item', ['p' => ['category' => $_item->category_id, 'action' => 'edit', 'id' => $_item->id], 'attributes' => ['class' => 'use-ajax', ]]) !!}
                </td>
                <td>
                    {{ $_item->sku }}
                </td>
                <td>
                    {{ $_item->value }}г
                </td>
                <td>
                    +{{ $_item->price }}
                </td>
                <td class="uk-text-center">{{ $_item->sort }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
