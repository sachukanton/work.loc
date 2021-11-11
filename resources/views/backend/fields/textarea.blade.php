<div class="uk-margin"
     id="{{ $params->get('id') }}-form-field-box">
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label">{!! $label !!}
            @if($params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="uk-form-controls {{$params->get('item_class')}}">
        <textarea id="{{ $params->get('id') }}"
                  name="{{ $params->get('name') }}"
                  {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                  class="uk-textarea {{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}">{!! $params->get('selected') !!}</textarea>
        @if($params->get('class') == 'uk-codeMirror')
            <div class="uk-text-right uk-margin-small-top">
                <button type="button"
                        data-id="{{ $params->get('id') }}"
                        class="uk-button uk-button-color-amber uk-button-small uk-button-use-ckEditor">
                    <span uk-icon="text_format"></span>
                    Редактор текста
                </button>
                <button type="button"
                        data-id="{{ $params->get('id') }}"
                        class="uk-button uk-button-color-indigo uk-button-small uk-button-use-code-mirror">
                    <span uk-icon="settings_ethernet"></span>
                    Редактор кода
                </button>
            </div>
        @endif
        @if($help = $params->get('help'))
            <div class="uk-help-block">
                {!! $help !!}
            </div>
        @endif
    </div>
</div>