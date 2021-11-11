@php
    $_default_locale = env('LOCALE');
@endphp
<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th>
                Заголовок страницы
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: sort_by_alpha"></span>
            </th>
            <th class="uk-width-xsmall"></th>
    </thead>
    <tbody>
        @foreach($_items as $_item)
            <tr>
                <td>
                    <a href="{{ $_item->generate_url }}"
                       target="_blank">
                        {{ $_item->getTranslation('title', $_default_locale) }}
                    </a>
                </td>
                <td>
                    @formField("page.{$_item->id}.sort", ['type' => 'number', 'value' => $_item->pivot->sort, 'attributes' => ['class' => ['uk-form-small uk-input uk-width-50']]])
                </td>
                <td>
                    @l('', 'oleus.shop_filter_pages.page', ['p' => ['shop_filter_page' => $entity->id, 'action' => 'destroy', 'id' => $_item], 'attributes' => ['class' => 'use-ajax uk-button-danger uk-button uk-button-small', 'uk-icon' => 'icon: delete']])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>