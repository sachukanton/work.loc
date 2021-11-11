@php
    $_default_locale = env('LOCALE');
    $_locales = config('laravellocalization.supportedLocales');
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<div class="uk-content">
    <form class="uk-form uk-form-stacked use-ajax"
          method="POST"
          action="{{ _r('oleus.advantages.item', ['entity' => $entity, 'action' => 'save', 'id' => ($_item->id ?? NULL)]) }}">
        <input type="hidden"
               value="{{ $_item->exists ? $_item->id : NULL }}"
               name="item[id]">
        <input type="hidden"
               value="{{ $entity->id }}"
               name="item[advantage_id]">
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">{!! $_item->exists ? "Редактирование пункта <strong>{$_item->title}</strong>" : 'Добавление пункта' !!}</h2>
        </div>
        <div class="uk-modal-body">
            @if(config('os_seo.use.multi_language'))
                <ul uk-tab="active: 0; connect: #uk-tab-modal-body; swiping: false;">
                    <li>
                        <a href="#">
                            {{ config("laravellocalization.supportedLocales.{$_default_locale}.native") }}
                        </a>
                    </li>
                    @foreach($_locales as $_locale => $_data)
                        @if($_locale != $_default_locale)
                            <li>
                                <a href="#">
                                    {{ $_data['native'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <ul id="uk-tab-modal-body"
                    class="uk-switcher uk-margin">
                    <li>
                        <div uk-grid>
                            <div class="uk-width-1-3">
                                @formField('item.icon_fid', ['type' => 'file', 'label' => 'Иконка', 'allow' => 'jpg|jpeg|gif|png|svg', 'view' => 'avatar', 'values' => $_item->exists && $_item->_icon? [$_item->_icon] : NULL])
                            </div>
                            <div class="uk-width-2-3">
                                @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок', 'value' => $_item->title, 'required' => TRUE])
                                @formField("item.sub_title.{$_default_locale}", ['label'=> 'Под заголовок', 'value' => $_item->sub_title])
                            </div>
                        </div>
                        @formField("item.body.{$_default_locale}", ['label' => 'Содержимое', 'type' => 'textarea','editor' => TRUE, 'class' => 'editor-short', 'value' => $_item->body, 'attributes' => ['rows' => 3]])
                        <hr class="uk-divider-icon">
                        @formField('item.sort', ['type' => 'number', 'label' => 'Позиция описания', 'selected' => $_item->exists ? $_item->sort : 0])
                        @formField('item.status', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->status : 1, 'values' => [1 => 'Опубликовано']])
                    </li>
                    @foreach(config('laravellocalization.supportedLocales') as $_locale => $_data)
                        @if($_locale != $_default_locale)
                            <li>
                                @formField("item.title.{$_locale}", ['label'=> 'Заголовок', 'value' => $_item->getTranslation('title', $_locale)])
                                @formField("item.sub_title.{$_locale}", ['label'=> 'Под заголовок', 'value' => $_item->getTranslation('sub_title', $_locale)])
                                @formField("item.body.{$_locale}", ['label' => 'Содержимое', 'type' => 'textarea','editor' => TRUE, 'class' => 'editor-short', 'value' => $_item->getTranslation('body', $_locale), 'attributes' => ['rows' => 3]])
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div uk-grid>
                    <div class="uk-width-1-3">
                        @formField('item.icon_fid', ['type' => 'file', 'label' => 'Иконка', 'allow' => 'jpg|jpeg|gif|png|svg', 'view' => 'avatar', 'values' => $_item->exists && $_item->_icon? [$_item->_icon] : NULL])
                    </div>
                    <div class="uk-width-2-3">
                        @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок', 'value' => $_item->title, 'required' => TRUE])
                        @formField("item.sub_title.{$_default_locale}", ['label'=> 'Под заголовок', 'value' => $_item->sub_title])
                    </div>
                </div>
                @formField("item.body.{$_default_locale}", ['label' => 'Содержимое', 'type' => 'textarea','editor' => TRUE, 'class' => 'editor-short', 'value' => $_item->body, 'attributes' => ['rows' => 3]])
                <hr class="uk-divider-icon">
                @formField('item.sort', ['type' => 'number', 'label' => 'Позиция описания', 'value' => $_item->exists ? $_item->sort : 0])
                @formField('item.status', ['type' => 'checkbox', 'label' => 'Опубликовано', 'selected' => $_item->exists ? $_item->status : 1, 'values' => [1 => 'Опубликовано']])
            @endif
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
                    class="uk-button uk-button-success uk-margin-small-right">
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
            <button class="uk-button uk-button-secondary uk-modal-close uk-button-icon"
                    uk-icon="icon: reply"
                    type="button"></button>
        </div>
    </form>
    @if($_item->exists)
        <form
            action="{{ _r('oleus.advantages.item', ['entity' => $entity, 'action' => 'destroy', 'id' => $_item->id]) }}"
            id="form-delete-modal-{{ $_item->id }}-object"
            class="use-ajax"
            method="POST">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
        </form>
    @endif
</div>