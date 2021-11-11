<div class="uk-margin{{ $params->get('required') ? ' uk-form-required' : NULL }}"
     id="{{ $params->get('id') }}-form-field-box">
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label">{!! $label !!}
            @if($params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="uk-form-controls uk-form-controls-autocomplete">
        <input type="{{ $params->get('type') }}"
               id="{{ $params->get('id') }}"
               name="{{ $params->get('name') }}"
               value="{{ $params->get('selected') }}"
               {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
               class="uk-input uk-border-rounded{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}">
        <input type="hidden"
               value="{{ $params->get('value') }}"
               name="{{ $params->get('autocomplete_name') }}">
        @if($help = $params->get('help'))
            <div class="uk-help-block">
                {!! $help !!}
            </div>
        @endif
    </div>
</div>
