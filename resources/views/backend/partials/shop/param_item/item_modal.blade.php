@php
    $_default_locale = env('LOCALE');
    $_locales = config('laravellocalization.supportedLocales');
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      method="POST"
      id="modal-param-item-form"
      action="{{ _r('oleus.shop_params.item', ['param' => $entity, 'action' => 'save', 'shop_param_item' => $_item]) }}">
    <input type="hidden"
           value="{{ $_item->id }}"
           name="id">
    <input type="hidden"
           value="{{ $entity->id }}"
           name="param_id">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $_item->exists ? 'Редактирование элемента списка' : 'Создание элемента списка' }}</h2>
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
                    @formField("title.{$_default_locale}", ['label'=> 'Название элемента списка', 'value' => $_item->getTranslation('title', $_default_locale), 'required' => TRUE, 'form_id' => 'modal-param-item-form'])
                    @formField("sub_title.{$_default_locale}", ['label'=> 'Описание элемента списка', 'value' => $_item->getTranslation('sub_title', $_default_locale), 'form_id' => 'modal-param-item-form'])
                    @formField("meta_title.{$_default_locale}", ['label'=> 'Название элемента списка для SEO', 'value' => $_item->getTranslation('meta_title', $_default_locale), 'form_id' => 'modal-param-item-form', 'help' => 'Используетсяв формировании заголовка страницы фильтра и META тегов.'])
                    @if(!$_item->exists)
                        @formField("name", ['label'=> 'Машинное имя элемента списка', 'value' => $_item->name, 'help'  => 'Используется для обозначения элемента параметра в <span class="uk-text-bold uk-text-primary">URL</span>.<br>При заполнении можно использовать символы латиского алфавита и знак подчеркивания. Если поле оставить пустым будет сгенерирован ключ из названия параметра.<br><span class="uk-text-danger">Учтите, что значение поля в дальнейшем отредактировать нельзя!</span>', 'form_id' => 'modal-param-item-form'])
                    @endif



                    {{--field_render('product', [--}}
                    {{--'type'       => 'autocomplete',--}}
                    {{--'label'      => 'Товар',--}}
                    {{--'value'      => $entity->_product->id,--}}
                    {{--'selected'   => $entity->_product->getTranslation('title', $this->defaultLocale),--}}
                    {{--'class'      => 'uk-autocomplete',--}}
                    {{--'attributes' => [--}}
                    {{--'data-url'   => _r('oleus.shop_product_list.product'),--}}
                    {{--'data-value' => 'name'--}}
                    {{--],--}}
                    {{--'required'   => TRUE,--}}
                    {{--'help'       => 'Начните вводить название товара'--}}
                    {{--]),--}}
                    {{--@formField('alias', [--}}
                                    {{--'type' => 'autocomplete',--}}
                                    {{--'label' => trans('forms.label_composed_link_product'),--}}
                                    {{--'value' => $_item->exists && $_item->_alias ? $_item->_alias->model_id : NULL,--}}
                                    {{--'selected' => $_item->exists && $_item->_alias ? $_item->_alias->entity : NULL,--}}
                                    {{--'class' => 'uk-autocomplete',--}}
                                    {{--'attributes' => [--}}
                                        {{--'data-url' => _r('oleus.shop_params.alias', ['param' => $entity, 'shop_param_item' => $_item]),--}}
                                        {{--'data-value' => 'name'--}}
                                    {{--]--}}
                                {{--])--}}


                    <hr class="uk-divider-icon">
                    <div class="uk-margin">
                        <div uk-grid
                             class="uk-child-width-1-2">
                            <div>
                                @formField('sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0, 'form_id' => 'modal-param-item-form'])
                            </div>
                            <div class="uk-margin-medium-top">
                                @formField('visible_in_filter', ['type' => 'checkbox', 'values' => [1 => 'Отображать в фильтре'], 'selected' => $_item->exists ? $_item->visible_in_filter : 1, 'form_id' => 'modal-param-item-form'])
                            </div>
                        </div>
                    </div>
                    <h3 class="uk-heading-line">
                        <span>
                        Стиль оформления
                        </span>
                    </h3>
                    @php($_data = $_item->exists && $_item->data ? unserialize($_item->data) : NULL)
                    <div class="uk-margin">
                        <div class="uk-grid">
                            <div class="uk-width-1-3">
                                @formField('icon_fid', ['type' => 'file', 'view' => 'avatar','label' => 'Иконка', 'allow' => 'jpg|jpeg|gif|png|ico|svg', 'values' => $_item->exists && $_item->_icon ? [$_item->_icon] : NULL, 'form_id' => 'modal-param-item-form'])

                            </div>
                            <div class="uk-width-2-3">
                                @formField('style_id', ['label' => 'ID элемента', 'value' => $_item->style_id, 'form_id' => 'modal-param-item-form'])
                                @formField('style_class', ['label' => 'CLASS элемента', 'value' => $_item->style_class, 'form_id' => 'modal-param-item-form'])
                                @formField('attribute', ['type' => 'textarea', 'label' => 'Дополнительные атрибуты', 'value' => $_item->attribute, 'attributes' => ['rows' => 5], 'form_id' => 'modal-param-item-form'])
                            </div>
                        </div>
                    </div>
                </li>
                @foreach(config('laravellocalization.supportedLocales') as $_locale => $_data)
                    @if($_locale != config('app.default_locale'))
                        <li>
                            @formField("title.{$_locale}", ['label'=> 'Название элемента списка', 'value' => $_item->getTranslation('title', $_locale), 'form_id' => 'modal-param-item-form'])
                            @formField("sub_title.{$_locale}", ['label'=> 'Описание элемента списка', 'value' => $_item->getTranslation('sub_title', $_locale), 'form_id' => 'modal-param-item-form'])
                            @formField("meta_title.{$_locale}", ['label'=> 'Название элемента списка для SEO', 'value' => $_item->getTranslation('meta_title', $_locale), 'form_id' => 'modal-param-item-form'])
                        </li>
                    @endif
                @endforeach
            </ul>
        @else

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
    <form
        action="{{ _r('oleus.shop_params.item', ['param' => $entity, 'action' => 'destroy', 'id' => $_item->id]) }}"
        id="form-delete-modal-{{ $_item->id }}-object"
        class="use-ajax uk-hidden"
        method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif