<div class="uk-margin{{ $params->get('required') ? ' uk-form-required' : NULL }}"
     id="{{ $params->get('id') }}-form-field-box">
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label uk-margin-remove-top">{!! $label !!}
            @if($params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="uk-form-controls">
        {!! nl2br($params->get('html')) !!}
    </div>
</div>
