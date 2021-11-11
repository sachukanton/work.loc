@php
    $_default_locale = env('LOCALE');
    $_locales = config('laravellocalization.supportedLocales');
    $_get_alias = $_item->exists ? $_item->_get_alias() : NULL;
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      id="modal-menu-item-form"
      method="POST"
      action="{{ _r('oleus.menus.item', ['menu' => $entity, 'action' => 'save', 'id' => ($_item->id ?? NULL)]) }}">
    <input type="hidden"
           value="{{ $_item->exists ? $_item->id : NULL }}"
           name="item[id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $_item->exists ? 'Редактирование пункта меню' : 'Создание пункта меню' }}</h2>
    </div>
    <div class="uk-modal-body">
        @if(USE_MULTI_LANGUAGE)
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
                    <div class="uk-margin">
                        <div uk-grid
                             class="uk-child-width-1-2">
                            <div>
                                @formField("item.title.{$_default_locale}", ['label'=> 'Название пункта меню', 'value' => $_item->title, 'required' => TRUE])
                            </div>
                            <div>
                                @formField("item.sub_title.{$_default_locale}", ['label'=> 'Подпись пункта меню', 'value' => $_item->sub_title])
                            </div>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <div uk-grid
                             class="uk-child-width-1-2">
                            <div>
                                @formField('item.link', ['type' => 'autocomplete', 'label' => 'Ссылка на материал', 'value' => $_item->exists && is_numeric($_item->alias_id) && $_get_alias ? $_item->alias_id : NULL, 'selected' => $_item->exists && $_get_alias ? $_get_alias->name : NULL, 'class' => 'uk-autocomplete', 'attributes' => ['data-url' => _r('oleus.menus.link'), 'data-value' => 'name'], 'required' => TRUE, 'help' => '<span class="uk-text-bold">&lt;front&gt;</span> - ссылка на главную страницу<br><span class="uk-text-bold">&lt;none&gt;</span> - пустая ссылка<br>либо вручную вписать путь'])
                            </div>
                            <div>
                                @formField('item.anchor', ['label'=> 'Якорь', 'value' => $_item->anchor])
                            </div>
                        </div>
                    </div>
                    @if($_parents->isNotEmpty())
                        <div class="uk-margin">
                            <div uk-grid
                                 class="uk-child-width-1-2">
                                <div>
                                    @formField('item.parent_id', ['type' => 'select', 'label' => 'Родительский пункт', 'selected' => $_item->parent_id, 'values' => $_parents, 'class' => 'uk-select2'])
                                </div>
                                <div>
                                    @formField('item.sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0])
                                </div>
                            </div>
                        </div>
                    @endif
                    <hr class="uk-divider-icon">
                    @formField('item.status', ['type' => 'checkbox', 'values' => [1 => 'опубликовано'], 'selected' => $_item->exists ? $_item->status : 1])
                    <h3 class="uk-heading-line">
                        <span>
                        Стиль оформления
                        </span>
                    </h3>
                    @php($_data = $_item->exists && $_item->data ? unserialize($_item->data) : NULL)
                    <div class="uk-margin">
                        <div class="uk-grid">
                            <div class="uk-width-1-3">
                                @formField('item.icon_fid', ['type' => 'file', 'view' => 'avatar','label' => 'Иконка', 'allow' => 'jpg|jpeg|gif|png|ico|svg', 'values' => $_item->icon_fid ? [f_get($_item->icon_fid)] : NULL])
                                {{--@formField('item.preview_fid', ['type' => 'file', 'view' => 'avatar','label' => 'Изображение', 'allow' => 'jpg|jpeg|gif|png|ico|svg', 'values' => $_item->preview_fid ? [f_get($_item->preview_fid)] : NULL])--}}
                            </div>
                            <div class="uk-width-2-3">
                                @formField('item.data.item_class', ['label' => '&lt;li class=\'...\'', 'value' => $_data && isset($_data['item_class']) ? $_data['item_class'] : NULL,])
                                @formField('item.data.id', ['label' => '&lt;a id=\'...\'', 'value' => $_data && isset($_data['id']) ? $_data['id'] : NULL,])
                                @formField('item.data.class', ['label' => '&lt;a class=\'...\'', 'value' => $_data && isset($_data['class']) ? $_data['class'] : NULL,])
                            </div>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <div class="uk-grid uk-child-width-1-2">
                            <div>
                                @formField('item.data.prefix', ['label' => 'Prefix', 'type' => 'textarea', 'help' => 'Код выводимый до элемента', 'value' => $_data && isset($_data['prefix']) ? $_data['prefix'] : NULL, 'attributes' => ['rows' => 3]])
                            </div>
                            <div>
                                @formField('item.data.suffix', ['label' => 'Suffix', 'type' => 'textarea', 'help' => 'Код выводимый после элемента', 'value' => $_data && isset($_data['suffix']) ? $_data['suffix'] : NULL, 'attributes' => ['rows' => 3]])
                            </div>
                        </div>
                    </div>
                    @formField('item.data.attributes', ['type' => 'textarea', 'label' => 'Дополнительные атрибуты', 'value' => $_data && isset($_data['attributes']) ? $_data['attributes'] : NULL, 'attributes' => ['rows' => 3]])
                </li>
                @foreach(config('laravellocalization.supportedLocales') as $_locale => $_data)
                    @if($_locale != config('app.default_locale'))
                        <li>
                            @formField("item.title.{$_locale}", ['label'=> 'Название пункта меню', 'value' => $_item->getTranslation('title', $_locale)])
                            @formField("item.sub_title.{$_locale}", ['label'=> 'Подпись пункта меню', 'value' => $_item->getTranslation('sub_title', $_locale)])
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            <div class="uk-margin">
                <div uk-grid
                     class="uk-child-width-1-2">
                    <div>
                        @formField("item.title.{$_default_locale}", ['label'=> 'Название пункта меню', 'value' => $_item->title, 'required' => TRUE])
                    </div>
                    <div>
                        @formField("item.sub_title.{$_default_locale}", ['label'=> 'Подпись пункта меню', 'value' => $_item->sub_title])
                    </div>
                </div>
            </div>
            <div class="uk-margin">
                <div uk-grid
                     class="uk-child-width-1-2">
                    <div>
                        @formField('item.link', ['type' => 'autocomplete', 'label' => 'Ссылка на материал', 'value' => $_item->exists && is_numeric($_item->alias_id) && $_get_alias ? $_item->alias_id : NULL, 'selected' => $_item->exists && $_get_alias ? $_get_alias->name : NULL, 'class' => 'uk-autocomplete', 'attributes' => ['data-url' => _r('oleus.menus.link'), 'data-value' => 'name'], 'required' => TRUE, 'help' => '<span class="uk-text-bold">&lt;front&gt;</span> - ссылка на главную страницу<br><span class="uk-text-bold">&lt;none&gt;</span> - пустая ссылка<br>либо вручную вписать путь'])
                    </div>
                    <div>
                        @formField('item.anchor', ['label'=> 'Якорь', 'value' => $_item->anchor])
                    </div>
                </div>
            </div>
            @if($_parents->isNotEmpty())
                <div class="uk-margin">
                    <div uk-grid
                         class="uk-child-width-1-2">
                        <div>
                            @formField('item.parent_id', ['type' => 'select', 'label' => 'Родительский пункт', 'selected' => $_item->parent_id, 'values' => $_parents, 'class' => 'uk-select2'])
                        </div>
                        <div>
                            @formField('item.sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0])
                        </div>
                    </div>
                </div>
            @endif
            <hr class="uk-divider-icon">
            @formField('item.status', ['type' => 'checkbox', 'values' => [1 => 'опубликовано'], 'selected' => $_item->exists ? $_item->status : 1])
            <h3 class="uk-heading-line">
                        <span>
                        Стиль оформления
                        </span>
            </h3>
            @php($_data = $_item->exists && $_item->data ? unserialize($_item->data) : NULL)
            <div class="uk-margin">
                <div class="uk-grid">
                    <div class="uk-width-1-3">
                        @formField('item.icon_fid', ['type' => 'file', 'view' => 'avatar','label' => 'Иконка', 'allow' => 'jpg|jpeg|gif|png|ico|svg', 'values' => $_item->icon_fid ? [f_get($_item->icon_fid)] : NULL])

                    </div>
                    <div class="uk-width-2-3">
                        @formField('item.data.item_class', ['label' => '&lt;li class=\'...\'', 'value' => $_data && isset($_data['item_class']) ? $_data['item_class'] : NULL,])
                        @formField('item.data.id', ['label' => '&lt;a id=\'...\'', 'value' => $_data && isset($_data['id']) ? $_data['id'] : NULL,])
                        @formField('item.data.class', ['label' => '&lt;a class=\'...\'', 'value' => $_data && isset($_data['class']) ? $_data['class'] : NULL,])
                    </div>
                </div>
            </div>
            <div class="uk-margin">
                <div class="uk-grid uk-child-width-1-2">
                    <div>
                        @formField('item.data.prefix', ['label' => 'Prefix', 'type' => 'textarea', 'help' => 'Код выводимый до элемента', 'value' => $_data && isset($_data['prefix']) ? $_data['prefix'] : NULL, 'attributes' => ['rows' => 3]])
                    </div>
                    <div>
                        @formField('item.data.suffix', ['label' => 'Suffix', 'type' => 'textarea', 'help' => 'Код выводимый после элемента', 'value' => $_data && isset($_data['suffix']) ? $_data['suffix'] : NULL, 'attributes' => ['rows' => 3]])
                    </div>
                </div>
            </div>
            @formField('item.data.attributes', ['type' => 'textarea', 'label' => 'Дополнительные атрибуты', 'value' => $_data && isset($_data['attributes']) ? $_data['attributes'] : NULL, 'attributes' => ['rows' => 3]])
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