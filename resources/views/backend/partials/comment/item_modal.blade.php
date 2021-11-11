<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      id="modal-comment-item-form"
      method="POST"
      action="{{ _r('oleus.comments.item', ['comment' => $entity, 'action' => 'save', 'id' => ($_item->id ?? NULL)]) }}">
    <input type="hidden"
           value="{{ $_item->exists ? $_item->id : NULL }}"
           name="item[id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $_item->exists ? 'Редактировать ответ' : 'Добавить ответ' }}</h2>
    </div>
    <div class="uk-modal-body">
        <div class="uk-card uk-card-body uk-border-rounded uk-box-shadow-small uk-background-color-blue uk-light uk-card-small">
            <p>
                {{ strip_tags($entity->comment) }}
            </p>
        </div>
        @formField('item.comment', ['label' => 'Текст', 'type' => 'textarea', 'value' => $_item->comment, 'attributes' => ['rows' => 3]])
        <hr class="uk-divider-icon">
        @formField('item.status', ['type' => 'checkbox', 'values' => [1 => 'опубликовано'], 'selected' => $_item->exists ? $_item->status : 1])
        @if($_item->exists)
            <div id="form-delete-modal-{{ $_item->id }}-box"
                 hidden
                 class="uk-border-danger uk-margin-top uk-border-rounded uk-padding-small uk-text-center uk-text-danger">
                Вы уверены, что хотите удалить элемент?
                <a href="javascript:void(0);"
                   data-item="modal-{{ $_item->id }}"
                   class="uk-button uk-button-danger uk-text-uppercase uk-margin-small-left uk-button-delete-entity">Да</a>
                <a href="javascript:void(0);"
                   data-item="{{ $_item->id }}"
                   uk-toggle="target: #form-delete-modal-{{ $_item->id }}-box; animation: uk-animation-fade"
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
        @if($_item->exists)
            <button type="button"
                    name="delete"
                    href="#toggle-animation"
                    value="1"
                    uk-icon="icon: delete"
                    uk-toggle="target: #form-delete-modal-{{ $_item->id }}-box; animation: uk-animation-fade"
                    class="uk-button uk-button-danger uk-button-icon uk-margin-small-right">
            </button>
        @endif
        <button class="uk-button uk-button-secondary uk-modal-close uk-button-icon uk-border-rounded"
                uk-icon="icon: clearclose"
                type="button"></button>
    </div>
</form>
@if($_item->exists)
    <form action="{{ _r('oleus.menus.item', ['menu' => $entity, 'action' => 'destroy', 'id' => $_item->id]) }}"
          id="form-delete-modal-{{ $_item->id }}-object"
          class="use-ajax uk-hidden"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif