@php
    $_default_locale = config('app.default_locale');
    $_locales = config('laravellocalization.supportedLocales');
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      id="modal-forms-field-form"
      method="POST"
      action="{{ _r('oleus.forms.field', ['form' => $entity, 'action' => 'save', 'id' => ($_item->id ?? NULL)]) }}">
    <input type="hidden"
           value="{{ $_item->exists ? $_item->id : NULL }}"
           name="item[id]">
    <input type="hidden"
           value="{{ $_item->type ?? NULL }}"
           name="item[type]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $_item->exists ? 'Редактирование поля формы' : 'Создание поля формы' }}</h2>
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
                    @php
                        $_data = $_item->data
                    @endphp
                    @if($_item->type != 'markup' && $_item->type != 'break')
                        @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок поля', 'value' => $_item->title, 'required' => TRUE])
                        @formField("item.help.{$_default_locale}", ['type' => 'textarea', 'label' => 'Описание поля', 'value' => $_item->help, 'attributes' => ['rows' => 2]])
                        <hr class="uk-divider-icon">
                        <div class="uk-form-row">
                            <div uk-grid
                                 class="uk-child-width-1-2 uk-grid-divider uk-grid-small">
                                <div>
                                    @formField("item.value", ['label'=> 'Значение поля по умолчанию', 'value' => $_item->value])
                                    @formField('item.data.attributes', ['type' => 'textarea', 'label' => 'Дополнительные атрибуты', 'value' => $_data && isset($_data['attributes']) ? $_data['attributes'] : NULL, 'attributes' => ['rows' => 3]])

                                </div>
                                <div>
                                    @formField('item.sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0, 'form_id' => 'modal-param-item-form'])
                                    @formField('item.hidden_label', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->hidden_label : 0, 'values' => [1 => 'Скрыть название поля']])
                                    @formField('item.placeholder_label', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->placeholder_label : 0, 'values' => [1 => 'Использовать заголовок как PLACEHOLDER']])
                                    @formField('item.status', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->status : 1, 'values' => [1 => 'Отображать поле в форме']])
                                </div>
                            </div>
                        </div>
                        <hr class="uk-divider-icon">
                        @if($_item->type == 'select' || $_item->type == 'checkbox' || $_item->type == 'radio')
                            <h3 class="uk-heading-line">
                                <span>
                                    Дополнительные настройки
                                </span>
                            </h3>
                            @formField("item.options.{$_default_locale}", ['label' => 'Список пунктов', 'type' => 'textarea', 'value' => $_item->options, 'attributes' => ['rows' => 8]])
                            @if($_item->type == 'select')
                                @formField('item.multiple', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->multiple : 0,'values' =>[1 => 'Множественный выбор']])
                            @endif
                        @endif
                        @if($_item->type == 'file')
                            <h3 class="uk-heading-line">
                                <span>
                                    Дополнительные настройки
                                </span>
                            </h3>
                            @formField('item.multiple', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->multiple : 0,'values' =>[1 => 'Множественный выбор']])
                        @endif
                        <h3 class="uk-heading-line">
                            <span>
                            Проверка поля
                            </span>
                        </h3>
                        @formField('item.required', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->required : 0, 'values' => [1 => 'Обязательно для заполнения'], 'help' => 'Кроме проверки отмечает поле звездочкой, как обязательное.'])
                        @formField('item.other_rules', ['type' => 'textarea', 'label' => 'Свои правила проверки', 'help' => 'Тут можну указать вручном режиме правило проверки для поля. При этом выше указанное правило становится не действительным.', 'value' => $_item->other_rules, 'attributes' => ['rows' => 2]])
                        <h3 class="uk-heading-line">
                            <span>
                            Стиль оформления
                            </span>
                        </h3>
                        <div class="uk-form-row">
                            <div class="uk-grid uk-grid-small uk-grid-divider">
                                <div class="uk-width-1-3 uk-first-column">
                                    @formField('item.data.item_class', ['label' => '&lt;div class=\'...\'', 'value' => $_data && isset($_data['item_class']) ? $_data['item_class'] : NULL,])
                                    @formField('item.data.class', ['label' => '&lt;input class=\'...\'', 'value' => $_data && isset($_data['class']) ? $_data['class'] : NULL,])
                                </div>
                                <div class="uk-width-2-3">
                                    @formField('item.data.prefix', ['label' => 'Prefix code', 'value' => $_data && isset($_data['prefix']) ? $_data['prefix'] : NULL,])
                                    @formField('item.data.suffix', ['label' => 'Suffix code', 'value' => $_data && isset($_data['suffix']) ? $_data['suffix'] : NULL,])
                                </div>
                            </div>
                        </div>
                    @elseif($_item->type == 'markup')
                        @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок поля', 'value' => $_item->title, 'required' => TRUE])
                        @formField("item.markup.{$_default_locale}", ['label' => 'Значение поля', 'type' => 'textarea', 'editor' => TRUE, 'class' => 'editor-short', 'value' => $_item->markup, 'attributes' => ['rows' => 12]])
                        <hr class="uk-divider-icon">
                        <div class="uk-form-row">
                            <div uk-grid
                                 class="uk-child-width-1-2 uk-grid-divider">
                                <div>
                                    @formField('item.sort', ['type' => 'select', 'label' => 'Порядок сортировки', 'selected' => $_item->exists ? $_item->sort : 0, 'values' => sort_values(), 'class' => 'uk-select2'])
                                </div>
                                <div>
                                    @formField('item.status', ['type' => 'checkbox', 'label' => 'Отображать поле в форме', 'selected' => $_item->exists ? $_item->status : 1])
                                </div>
                            </div>
                        </div>
                    @elseif($_item->type == 'break')
                        @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок шага формы', 'value' => $_item->title, 'required' => TRUE])
                        <hr class="uk-divider-icon">
                        @formField('item.sort', ['type' => 'select', 'label' => 'Порядок сортировки', 'selected' => $_item->exists ? $_item->sort : 0, 'values' => sort_values(), 'class' => 'uk-select2'])
                        @formField('item.hidden_label', ['type' => 'checkbox', 'label' => 'Скрыть название поля', 'selected' => $_item->exists ? $_item->hidden_label : 0])
                    @endif
                </li>
                @foreach(config('laravellocalization.supportedLocales') as $_locale => $_data)
                    @if($_locale != config('app.default_locale'))
                        <li>
                            @formField("item.title.{$_locale}", ['label'=> 'Заголовок поля', 'value' => $_item->getTranslation('title', $_locale)])
                            @if($_item->type != 'markup' && $_item->type != 'break')
                                @formField("item.help.{$_locale}", ['label'=> 'Описание поля', 'type' => 'textarea', 'value' => $_item->getTranslation('help', $_locale)])
                            @endif
                            @if($_item->type == 'select' || $_item->type == 'checkboxes' || $_item->type == 'radios')
                                @formField("item.options.{$_locale}", ['label' => 'Список пунктов', 'type' => 'textarea', 'value' => $_item->getTranslation('options', $_locale), 'attributes' => ['rows' => 8]])
                            @endif
                            @if($_item->type == 'markup')
                                @formField("item.markup.{$_locale}", ['label' => 'Значение поля', 'type' => 'textarea', 'value' => $_item->getTranslation('markup', $_locale), 'attributes' => ['rows' => 12]])
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            @php
                $_data = $_item->data
            @endphp
            @if($_item->type != 'markup' && $_item->type != 'break')
                @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок поля', 'value' => $_item->title, 'required' => TRUE])
                @formField("item.help.{$_default_locale}", ['type' => 'textarea', 'label' => 'Описание поля', 'value' => $_item->help, 'attributes' => ['rows' => 2]])
                <hr class="uk-divider-icon">
                <div class="uk-form-row">
                    <div uk-grid
                         class="uk-child-width-1-2 uk-grid-divider uk-grid-small">
                        <div>
                            @formField("item.value", ['label'=> 'Значение поля по умолчанию', 'value' => $_item->value])
                            @formField('item.data.attributes', ['type' => 'textarea', 'label' => 'Дополнительные атрибуты', 'value' => $_data && isset($_data['attributes']) ? $_data['attributes'] : NULL, 'attributes' => ['rows' => 3]])

                        </div>
                        <div>
                            @formField('item.sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0, 'form_id' => 'modal-param-item-form'])
                            @formField('item.hidden_label', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->hidden_label : 0, 'values' => [1 => 'Скрыть название поля']])
                            @formField('item.placeholder_label', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->placeholder_label : 0, 'values' => [1 => 'Использовать заголовок как PLACEHOLDER']])
                            @formField('item.status', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->status : 1, 'values' => [1 => 'Отображать поле в форме']])
                        </div>
                    </div>
                </div>
                <hr class="uk-divider-icon">
                @if($_item->type == 'select' || $_item->type == 'checkboxes' || $_item->type == 'radios')
                    <h3 class="uk-heading-line">
                                <span>
                                    Дополнительные настройки
                                </span>
                    </h3>
                    @formField("item.options.{$_default_locale}", ['label' => 'Список пунктов', 'type' => 'textarea', 'value' => $_item->options, 'attributes' => ['rows' => 8]])
                    @if($_item->type == 'select')
                        @formField('item.multiple', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->multiple : 0,'values' =>[1 => 'Множественный выбор']])
                    @endif
                @endif
                @if($_item->type == 'file')
                    <h3 class="uk-heading-line">
                                <span>
                                    Дополнительные настройки
                                </span>
                    </h3>
                    @formField('item.multiple', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->multiple : 0,'values' =>[1 => 'Множественный выбор']])
                @endif
                <h3 class="uk-heading-line">
                            <span>
                            Проверка поля
                            </span>
                </h3>
                @formField('item.required', ['type' => 'checkbox', 'selected' => $_item->exists ? $_item->required : 0, 'values' => [1 => 'Обязательно для заполнения'], 'help' => 'Кроме проверки отмечает поле звездочкой, как обязательное.'])
                @formField('item.other_rules', ['type' => 'textarea', 'label' => 'Свои правила проверки', 'help' => 'Тут можну указать вручном режиме правило проверки для поля. При этом выше указанное правило становится не действительным.', 'value' => $_item->other_rules, 'attributes' => ['rows' => 2]])
                <h3 class="uk-heading-line">
                            <span>
                            Стиль оформления
                            </span>
                </h3>
                <div class="uk-form-row">
                    <div class="uk-grid uk-grid-small uk-grid-divider">
                        <div class="uk-width-1-3 uk-first-column">
                            @formField('item.data.item_class', ['label' => '&lt;div class=\'...\'', 'value' => $_data && isset($_data['item_class']) ? $_data['item_class'] : NULL,])
                            @formField('item.data.class', ['label' => '&lt;input class=\'...\'', 'value' => $_data && isset($_data['class']) ? $_data['class'] : NULL,])
                        </div>
                        <div class="uk-width-2-3">
                            @formField('item.data.prefix', ['label' => 'Prefix code', 'value' => $_data && isset($_data['prefix']) ? $_data['prefix'] : NULL,])
                            @formField('item.data.suffix', ['label' => 'Suffix code', 'value' => $_data && isset($_data['suffix']) ? $_data['suffix'] : NULL,])
                        </div>
                    </div>
                </div>
            @elseif($_item->type == 'markup')
                @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок поля', 'value' => $_item->title, 'required' => TRUE])
                @formField("item.markup.{$_default_locale}", ['label' => 'Значение поля', 'type' => 'textarea', 'editor' => TRUE, 'class' => 'editor-short', 'value' => $_item->markup, 'attributes' => ['rows' => 12]])
                <hr class="uk-divider-icon">
                <div class="uk-form-row">
                    <div uk-grid
                         class="uk-child-width-1-2 uk-grid-divider">
                        <div>
                            @formField('item.sort', ['type' => 'select', 'label' => 'Порядок сортировки', 'selected' => $_item->exists ? $_item->sort : 0, 'values' => sort_values(), 'class' => 'uk-select2'])
                        </div>
                        <div>
                            @formField('item.status', ['type' => 'checkbox', 'label' => 'Отображать поле в форме', 'selected' => $_item->exists ? $_item->status : 1])
                        </div>
                    </div>
                </div>
            @elseif($_item->type == 'break')
                @formField("item.title.{$_default_locale}", ['label'=> 'Заголовок шага формы', 'value' => $_item->title, 'required' => TRUE])
                <hr class="uk-divider-icon">
                @formField('item.sort', ['type' => 'select', 'label' => 'Порядок сортировки', 'selected' => $_item->exists ? $_item->sort : 0, 'values' => sort_values(), 'class' => 'uk-select2'])
                @formField('item.hidden_label', ['type' => 'checkbox', 'label' => 'Скрыть название поля', 'selected' => $_item->exists ? $_item->hidden_label : 0])
            @endif
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
    <form action="{{ _r('oleus.forms.field', ['form' => $entity, 'action' => 'destroy', 'id' => $_item->id]) }}"
          id="form-delete-modal-{{ $_item->id }}-object"
          class="use-ajax"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif
