<tr class="{{ $_item->type == 'break' ? 'uk-background-muted' : NULL }}">
    <td class="uk-text-center uk-text-bold">{{ $_item->id }}</td>
    <td class="{{ $_item->type == 'break' ? 'uk-text-bold uk-text-primary' : NULL }}">
        {!! $_item->title !!}
    </td>
    <td>
        {{ $_item->get_field_data()['name'] }}
    </td>
    <td class="uk-text-center">
        @if($_item->type != 'break' && $_item->type != 'markup')
            {!! ($_item->required || $_item->other_rules) ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
        @endif
    </td>
    <td class="uk-text-center">{{ $_item->sort }}</td>
    <td class="uk-text-center">
        {!! $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>' !!}
    </td>
    <td class="uk-text-center">
        @l('', 'oleus.forms.field', ['p' => ['entity' => $_item->form_id, 'action' => 'edit', 'id' => $_item->id], 'attributes' => ['class' => 'use-ajax uk-button-primary uk-button uk-button-small', 'uk-icon' => 'icon: createmode_editedit']])
    </td>
</tr>