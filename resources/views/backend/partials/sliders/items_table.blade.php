<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: more_horiz"></span>
            </th>
            <th>
                Заголовок
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: sort_by_alpha"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: laptop_windows"></span>
            </th>
            <th class="uk-width-xsmall"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $_item)
            <tr>
                <td class="uk-text-center uk-text-bold">
                    {{ $_item->id }}
                </td>
                <td>
                    {{ $_item->getTranslation('title', env('LOCALE')) }}
                </td>
                <td class="uk-text-center">
                    <input type="number"
                           class="uk-input uk-form-width-xsmall uk-form-small uk-input-number-spin-hide uk-input-sort-item"
                           name="items_sort[{{ $_item->id }}]"
                           data-id="{{ $_item->id }}"
                           value="{{ $_item->sort }}">
                </td>
                <td class="uk-text-center">
                    {!! $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
                </td>
                <td>
                    @l('', 'oleus.sliders.item', ['p' => ['slider' => $_item->slider_id, 'action' => 'edit', 'id' => $_item->id],  'attributes' => ['class' => 'use-ajax uk-button-primary uk-button uk-button-small', 'uk-icon' => 'icon: createmode_editedit']])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>