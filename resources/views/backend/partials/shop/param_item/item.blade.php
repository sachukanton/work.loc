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
                Название элемента списка
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: sort_by_alpha"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: laptop_windows"></span>
            </th>
            <th class="uk-width-xsmall">
                <span uk-icon="icon: createmode_editedit"></span>
            </th>
    </thead>
    <tbody>
        @foreach($items as $_item)
            <tr>
                <td class="uk-text-center uk-text-bold">
                    {{ $_item->id }}
                </td>
                <td>
                    {{ $_item->title }}
                </td>
                <td class="uk-text-center">
                    {{ $_item->sort }}
                </td>
                <td class="uk-text-center">
                    {!! $_item->visible_in_filter ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
                </td>
                <td>
                    @l('', 'oleus.shop_params.item', ['p' => ['param' => $_item->param_id, 'action' => 'edit', 'id' => $_item], 'attributes' => ['class' => 'use-ajax uk-button-primary uk-button uk-button-small', 'uk-icon' => 'icon: createmode_editedit']])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>