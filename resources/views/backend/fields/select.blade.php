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
    <div class="uk-form-controls">
        <select id="{{ $params->get('id') }}"
                name="{{ $params->get('name') }}"
                class="uk-select{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
            {!! $params->get('multiple') ? ' multiple' : '' !!}
            {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
            {!! $params->get('options') ? " {$params->get('options')}" : '' !!}>
            @foreach($params->get('values') as $key => $value)
                @if(is_array($selected) || is_object($selected))
                    @php
                        if(is_object($selected)) $selected = $selected->toArray();
                        $_selected = in_array($key, $selected) ? ' selected' : '';
                    @endphp
                    @if(is_array($value))
                        <option value="{{ $key ? $key : NULL }}" {{ $_selected }}>{!! $value[0] . $value[1] !!}</option>
                    @else
                        <option value="{{ $key ? $key : NULL }}" {{ $_selected }}>{!! $value !!}</option>
                    @endif
                @else
                    @php
                        $_selected = !is_null($selected) ? ((string)$selected == (string)$key ? ' selected' : '') : '';
                    @endphp
                    @if(is_array($value))
                        <option value="{{ !is_null($key) ? $key : NULL }}" {{ $_selected }}>{!! $value[0].$value[1] !!}</option>
                    @else
                        <option value="{{ !is_null($key) ? $key : NULL }}" {{ $_selected }}>{!! $value !!}</option>
                    @endif
                @endif
            @endforeach
        </select>
        @if($help = $params->get('help'))
            <div class="uk-help-block">
                {!! $help !!}
            </div>
        @endif
    </div>
</div>
