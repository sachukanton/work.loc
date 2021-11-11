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
    <div class="uk-form-controls">
        <label
               class="uk-flex uk-flex-middle">
        <input type="checkbox"
               id="{{ $params->get('id') }}"
               name="{{ $params->get('name') }}"
               value="{{ $params->get('selected') }}"
               autocomplete="off"
               {{--{!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}--}}
               class="uk-checkbox {{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}">
                <span class="uk-display-inline-block uk-flex-1 uk-margin-left">
                    {!! $params->get('base_label') !!}
                    @if($params->get('required'))
                        <span class="uk-text-danger">*</span>
                    @endif
                </span>
        </label>
        @if($help = $params->get('help'))
            <div class="uk-help-block">
                {!! $help !!}
            </div>
        @endif
    </div>
</div>
