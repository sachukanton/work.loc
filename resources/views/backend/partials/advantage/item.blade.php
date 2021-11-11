<tr>
    <td class="uk-text-center uk-text-bold">
        {{ $_item->id }}
    </td>
    <td>
        {!! $_item->getTranslation('title', env('LOCALE')) !!}
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
    <td class="uk-text-center">
        @l('', 'oleus.advantages.item', ['p' => ['entity' => $_item->advantage_id, 'action' => 'edit', 'id' => $_item->id], 'attributes' => ['class' => 'use-ajax uk-button-primary uk-button uk-button-small', 'uk-icon' => 'icon: createmode_editedit']])
    </td>
</tr>