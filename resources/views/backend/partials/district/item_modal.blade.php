@php
    $_default_locale = env('LOCALE');
    $_locales = config('laravellocalization.supportedLocales');
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      id="modal-district-item-form"
      method="POST"
      action="{{ _r('oleus.pharm_city.item', ['entity' => $entity, 'action' => 'save', 'id' => ($_item->id ?? NULL)]) }}">
    <input type="hidden"
           value="{{ $_item->exists ? $_item->id : NULL }}"
           name="item[id]">
    <input type="hidden"
           value="{{ $entity->id }}"
           name="item[pharm_city_id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">
            {!! $_item->exists ? 'Редактирование района города' : 'Добавление района города' !!}
        </h2>
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
                    @formField("item.full_name.{$_default_locale}", ['label'=> 'Полное название', 'value' => $_item->full_name, 'required' => TRUE])
                    @formField("item.short_name.{$_default_locale}", ['label'=> 'Короткое название', 'value' => $_item->short_name])
                    @formField('item.sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0])
                </li>
                @foreach(config('laravellocalization.supportedLocales') as $_locale => $_data)
                    @if($_locale != config('app.default_locale'))
                        <li>
                            @formField("item.full_name.{$_locale}", ['label'=> 'Полное название ', 'value' => $_item->getTranslation('full_name', $_locale)])
                            @formField("item.short_name.{$_locale}", ['label'=> 'Короткое название', 'value' => $_item->getTranslation('short_name', $_locale)])
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            @formField("item.full_name.{$_default_locale}", ['label'=> 'Полное название', 'value' => $_item->full_name, 'required' => TRUE])
            @formField("item.short_name.{$_default_locale}", ['label'=> 'Короткое название', 'value' => $_item->short_name])
            @formField('item.sort', ['type' => 'number', 'label' => 'Порядок сортировки', 'value' => $_item->exists ? $_item->sort : 0])
        @endif
    </div>
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
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-success use-ajax uk-margin-small-right">
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
    <form action="{{ _r('oleus.pharm_city.item', ['entity' => $entity, 'action' => 'destroy', 'id' => $_item->id]) }}"
          id="form-delete-modal-{{ $_item->id }}-object"
          class="use-ajax"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif