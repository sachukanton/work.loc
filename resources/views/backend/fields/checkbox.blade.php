@php
    $selected = $params->get('selected');
@endphp
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
    <div class="uk-form-controls{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
         id="{{ $params->get('id') }}">
        @foreach($params->get('values') as $item_key => $item_value)
            <div class="uk-margin-small">
                <label for="{{ $params->get('id') ."-{$item_key}" }}"
                       class="uk-display-inline-block">
                    <input name="{{ $params->get('name') }}[{{ $item_key }}]"
                           type="checkbox"
                           id="{{ $params->get('id')."-{$item_key}" }}"
                           class="uk-checkbox"
                           data-key="{{ $item_key }}"
                           value="1"
                        {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                        {{ ($selected && is_array($selected) && in_array($item_key, $selected)) || ($selected && $selected == $item_key) ? ' checked' : '' }}>
                    @if(is_array($item_value))
                        <span class="uk-display-inline-block">{!! $item_value[0] !!}
                            @if($params->get('required'))
                                <span class="uk-text-danger">*</span>
                            @endif
                        </span>
                        @isset($item_value[1])
                            <span class="uk-help-form-label">{!! $item_value[1] !!}</span>
                        @endisset
                    @else
                        <span class="uk-display-inline-block">{!! $item_value !!}
                            @if($params->get('required'))
                                <span class="uk-text-danger">*</span>
                            @endif
                        </span>
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
