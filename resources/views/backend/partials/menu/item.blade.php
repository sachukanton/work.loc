@php
    $_level = isset($_level) ? $_level + 1 : 0;
    $_level_padding_left = $_level * 25;
@endphp
<tr>
    <td class="uk-text-center uk-text-bold">
        {{ $_item->id }}
    </td>
    <td style="padding-left: {{ "{$_level_padding_left}px" }}">
        @if($_level)
            <span uk-icon="icon : subdirectory_arrow_right; ratio: .8"
                  class="uk-position-relative"
                  style="top: -3px;"></span>
        @endif
        {!! $_item->title !!}
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
        @l('', 'oleus.menus.item', ['p' => ['entity' => $_item->menu_id, 'action' => 'edit', 'id' => $_item->id], 'attributes' => ['class' => 'use-ajax uk-button-primary uk-button uk-button-small', 'uk-icon' => 'icon: createmode_editedit']])
    </td>
</tr>
@if($_item->_children->isNotEmpty())
    @foreach($_item->_children as $_child)
        @include('backend.partials.menu.item', [
            '_item' => $_child,
            '_level' => $_level
        ])
    @endforeach
@endif