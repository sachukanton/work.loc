@php
    $_get_alias = $_item->exists ? $_item->_get_alias() : NULL;
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      id="modal-redirect-item-form"
      method="POST"
      action="{{ _r('oleus.redirects.item', ['action' => 'save', 'id' => ($_item->id ?? NULL)]) }}">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $_item->exists ? 'Редактирование редиректа' : 'Создание редиректа' }}</h2>
    </div>
    <div class="uk-modal-body">
        @formField('item.redirect', ['type' => 'textarea', 'label' => 'URL перенаправления', 'required' => TRUE, 'value' => $_item->exists ? $_item->redirect : NULL, 'attributes' => ['rows' => 3]])
        @formField('item.link', ['type' => 'autocomplete', 'label' => 'Ссылка на материал', 'value' => $_item->exists && is_numeric($_item->alias_id) && $_get_alias ? $_item->alias_id : NULL, 'selected' => $_item->exists && $_get_alias ? $_get_alias->name : NULL, 'class' => 'uk-autocomplete', 'attributes' => ['data-url' => _r('oleus.redirects.link'), 'data-value' => 'name'], 'required' => TRUE, 'help' => 'URL станицы, с которой будет производиться перенаправление. Правила формирования:<ul><li>articles/article-1 - конкретный URL</li><li>/ - ссылка на главную страницу</li></ul>'])
        <hr>
        @formField('item.status', ['type' => 'radio', 'values' => [301 => '301 редирект', 302 => '302 редирект'], 'selected' => $_item->exists ? $_item->status : 301])
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
    <form action="{{ _r('oleus.redirects.item', ['action' => 'destroy', 'id' => $_item->id]) }}"
          id="form-delete-modal-{{ $_item->id }}-object"
          class="use-ajax uk-hidden"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif