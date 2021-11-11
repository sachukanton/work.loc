<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked uk-form-horizontal use-ajax"
      method="POST"
      action="{{ $item->exists ? _r('oleus.shop_categories.additional_item', ['category' => $category, 'action' => 'update', 'id' => $item->id]) : _r('oleus.shop_categories.additional_item', ['category' => $category, 'action' => 'save']) }}">
    <input type="hidden"
           value="{{ $item->exists ? $item->id : NULL }}"
           name="item[id]">
    <input type="hidden"
           value="{{ $category->id }}"
           name="item[category_id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $item->exists ? 'Редактирование дополнительных игрединентов' : 'Добавление дополнительных игрединентов' }}</h2>
    </div>
    <div class="uk-modal-body">
        @formField('item.item_id', ['type' => 'select', 'label' => 'Инредиент', 'values' => $category->_additional_ingredients(), 'selected' => $item->item_id, 'class' => 'uk-select2','required' => TRUE])
        @formField('item.sku', ['label' => 'Артикул', 'value' => $item->sku, 'required' => TRUE])
        @formField('item.value', ['label' => 'Вес/Объем', 'value' => $item->value, 'required' => TRUE, 'attributes' => ['step' => 1]])
        @formField('item.sort', ['label' => 'Порядок сортировки', 'value' => $item->sort ?: 0, 'type' => 'number', 'attributes' => ['step' => 1]])
        @formField('item.price', ['label' => 'Цена за порцию', 'value' => $item->price, 'type' => 'number', 'required' => TRUE, 'attributes' => ['step' => 0.01]])
        @formField('item.default', ['values' => [1 => 'в карточке товара по умолчанию'], 'type' => 'checkbox', 'selected' => $item->exists ? $item->default : 0])
        @if($item->exists)
            <div id="form-delete-modal-{{ $item->id }}-box"
                 hidden
                 class="uk-border-danger uk-margin-top uk-border-rounded uk-padding-small uk-text-center uk-text-danger">
                Вы уверены, что хотите удалить элемент?
                <a href="javascript:void(0);"
                   data-item="modal-{{ $item->id }}"
                   class="uk-button uk-button-danger uk-text-uppercase uk-margin-small-left uk-button-delete-entity">Да</a>
                <a href="javascript:void(0);"
                   data-item="{{ $item->id }}"
                   uk-toggle="target: #form-delete-modal-{{ $item->id }}-box; animation: uk-animation-fade"
                   class="uk-button uk-button-secondary uk-text-uppercase uk-margin-small-left uk-button-delete-action">Нет</a>
            </div>
        @endif
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-success use-ajax uk-border-rounded uk-margin-small-right">
            Сохранить
        </button>
        @if($item->exists)
            <button type="button"
                    name="delete"
                    href="#toggle-animation"
                    value="1"
                    uk-icon="icon: delete"
                    uk-toggle="target: #form-delete-modal-{{ $item->id }}-box; animation: uk-animation-fade"
                    class="uk-button uk-button-danger uk-button-icon uk-margin-small-right">
            </button>
        @endif
        <button class="uk-button uk-button-secondary uk-modal-close uk-button-icon uk-border-rounded"
                uk-icon="icon: clearclose"
                type="button"></button>
    </div>
</form>
@if($item->exists)
    <form action="{{ _r('oleus.shop_categories.additional_item', ['category' => $category, 'action' => 'destroy', 'id' => $item->id]) }}"
          id="form-delete-modal-{{ $item->id }}-object"
          class="use-ajax uk-hidden"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif
