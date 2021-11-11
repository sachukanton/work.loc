<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: more_horiz"></span>
            </th>
            <th>
                Заголовок поля
            </th>
            <th class="uk-width-medium">
                Тип поля
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: priority_high"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: sort_by_alpha"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center">
                <span uk-icon="icon: laptop_windows"></span>
            </th>
            <th class="uk-width-xsmall"></th>
    </thead>
    <tbody>
        @foreach($items as $_item)
            @include('backend.partials.form.item', compact('_item'))
        @endforeach
    </tbody>
</table>