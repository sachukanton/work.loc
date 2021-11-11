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
        @php
            $name = $params->get('name');
            $options = $params->get('options');
            $cols = $options['cols'];
            $thead = isset($options['thead']) ? $options['thead'] : NULL;
            $tbody = (($_value = $params->get('value')) ? json_decode($_value) : NULL);
        @endphp
        <div class="uk-inline uk-width-1-1">
            <table class="uk-table uk-table-small uk-table-divider">
                @if($thead)
                    <thead class="uk-background-muted">
                        <tr>
                            @for($i = 0; $i < $cols; $i++)
                                <th class="uk-width-1-{{ $cols }} uk-padding-small">
                                    {!! $thead[$i] !!}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                @endif
                <tbody id="field-table-items">
                    @if($tbody)
                        @foreach($tbody as $td)
                            @include('backend.fields.table_item', ['cols' => $cols, 'name' => $name, 'td_item' => $td])
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="uk-clearfix uk-text-right">
                @l('Добавить строку в таблицу', 'ajax.fields_item', ['p' => ['type' => 'table'], 'attributes' => ['class' => 'uk-button uk-button-success use-ajax uk-border-rounded', 'data-cols' => $cols, 'data-name' => $name]])
            </div>
        </div>
        @if($help = $params->get('help'))
            <div class="uk-help-block">
                {!! $help !!}
            </div>
        @endif
    </div>
</div>
