@php
    $selected = $params->get('selected');
@endphp
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
    <div class="uk-form-controls{{ ($class = $params->get('class')) ? " " : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
         id="{{ $params->get('id') }}">
        @foreach($params->get('values') as $item_key => $item_value)
            <div class="uk-margin-small">
                <label for="{{ $params->get('id') ."-{$item_key}" }}"
                       class="uk-display-block">
                    <input name="{{ $params->get('name') }}"
                           type="radio"
                           id="{{ $params->get('id')."-{$item_key}" }}"
                           class="uk-radio{{ ($class = $params->get('class')) ? " {$class}" : '' }}"
                           data-key="{{ $item_key }}"
                           value="{{ $item_key }}"
                        {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                        {{ $selected == $item_key ? ' checked' : '' }}>
                    @if(is_array($item_value))
                        <span class="uk-display-inline-block">{!! $item_value[0] !!}</span>
                        @isset($item_value[1])
                            <span class="uk-help-form-label">{!! $item_value[1] !!}</span>
                        @endisset
                    @else
                        <span class="uk-display-inline-block">{!! $item_value !!}</span>
                    @endif
                </label>
            </div>
        @endforeach
        @if($help = $params->get('help'))
            <div class="uk-help-block">
                {!! $help !!}
            </div>
        @endif
    </div>
</div>