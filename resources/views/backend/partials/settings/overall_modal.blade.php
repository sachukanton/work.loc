@php
    $_default_locale = env('LOCALE');
    $entity = config('os_seo');
    $_locales = config('laravellocalization.supportedLocales');
@endphp
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked use-ajax"
      id="modal-menu-item-form"
      method="POST"
      action="{{ _r('oleus.settings.translate', ['setting' => 'overall', 'action' => 'save']) }}">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">Добавить перевод</h2>
    </div>
    <div class="uk-modal-body">
        <ul uk-tab="active: 0; connect: #uk-tab-modal-body; swiping: false;">
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
            @foreach(config('laravellocalization.supportedLocales') as $_locale => $_data)
                @if($_locale != $_default_locale)
                    <li>
                        @formField("settings.{$_locale}.site_name", ['label'=> 'Название сайта', 'value' => config_data_load($entity, 'settings.*.site_name', $_locale)])
                        @formField("settings.{$_locale}.site_slogan", ['label'=> 'Слоган сайта', 'value' => config_data_load($entity, 'settings.*.site_slogan', $_locale)])
                        @formField("settings.{$_locale}.site_copyright", ['label'=> 'Копирайт в подвале', 'value' => config_data_load($entity, 'settings.*.site_copyright', $_locale), 'help'  => ':year - автоматически подставит текущий год'])
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-success use-ajax uk-border-rounded uk-margin-small-right">
            Сохранить
        </button>
        <button class="uk-button uk-button-secondary uk-modal-close uk-button-icon uk-border-rounded"
                uk-icon="icon: clearclose"
                type="button"></button>
    </div>
</form>