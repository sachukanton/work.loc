<div class="uk-margin{{ $params->get('required') ? ' uk-form-required' : NULL }}">
    <div class="uk-grid uk-child-width-1-2 uk-grid-small">
        <div id="{{ $params->get('id') }}-form-field-box">
            @if($label = $params->get('label'))
                <label for="{{ $params->get('id') }}"
                       class="uk-form-label">{!! $label !!}
                    @if($params->get('required'))
                        <span class="uk-text-danger">*</span>
                    @endif
                </label>
            @endif
            <div class="uk-form-controls">
                <input type="password"
                       id="{{ $params->get('id') }}"
                       name="{{ $params->get('name') }}"
                       value=""
                       autocomplete="off"
                       {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                       class="uk-input{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}">
            </div>
        </div>
        <div id="{{ $params->get('id') }}-confirmation-form-field-box">
            @if($label = $params->get('label_confirmation'))
                <label for="{{ $params->get('id') }}_confirmation"
                       class="uk-form-label">
                    {!! $label !!}
                </label>
            @endif
            <div class="uk-form-controls">
                <input type="password"
                       id="{{ $params->get('id') }}-confirmation"
                       name="{{ $params->get('name_confirmation') }}"
                       value=""
                       autocomplete="off"
                       {!! $params->get('attributes_confirmation') ? " {$params->get('attributes_confirmation')}" : '' !!}
                       class="uk-input{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}">
            </div>
        </div>
    </div>
    @if($help = $params->get('help'))
        <div class="uk-help-block">
            {!! $help !!}
        </div>
    @endif
</div>