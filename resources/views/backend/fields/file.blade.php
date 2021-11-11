@php
    $files = ($_files = session($params->get('old'))) ? json_decode($_files) : (($_files = $params->get('values')) ? $_files : NULL);
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
    @if($params->get('ajax_url'))
        <div
            class="uk-display-block uk-form-controls-file uk-form-controls {{ $params->get('multiple') ? 'uk-multiple-file' : 'uk-one-file' }}{{ !$params->get('multiple') && $files ? ' loaded-file' : '' }}"
            data-view="{{ $params->get('upload_view') }}">
            <div class="uk-width-1-1 uk-position-relative">
                <input type="hidden"
                       name="{{ $params->get('name') }}">
                <div class="uk-preview">
                    @if($params->get('upload_view') == 'gallery')
                        <div class="uk-grid uk-grid-small uk-child-width-1-3"
                             uk-sortable="handle: .uk-sortable-handle">
                            @endif
                            @if($files)
                                @php
                                    $_options = [
                                    'field' => $params->get('name'),
                                    'view' => $params->get('upload_view')
                                    ];
                                @endphp
                                @foreach($files as $file)
                                    {!! preview_file_render($file, $_options) !!}
                                @endforeach
                            @endif
                            @if($params->get('upload_view') == 'gallery')
                        </div>
                    @endif
                </div>
                <div class="uk-field uk-text-right">
                    <div
                        class="js-upload uk-placeholder uk-text-center uk-border-rounded{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                        id="{{ $params->get('id') }}">
                    <span uk-icon="icon: cloud_upload"
                          class="uk-text-muted"></span>
                        <span class="uk-text-middle uk-text-small">
                        Перетяните файл или воспользуйтесь
                    </span>
                        @php($_upload_allow = $params->get('upload_allow'))
                        <div data-url="{{ $params->get('ajax_url') }}"
                             data-allow="{{ $_upload_allow }}"
                             data-field="{{ $params->get('name') }}"
                             data-multiple="{{ $params->get('multiple') ? 1 : 0 }}"
                             data-view="{{ $params->get('upload_view') }}"
                             class="uk-field file-upload-field{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                             uk-form-custom>
                            <input type="file"{{ $params->get('multiple') ? ' multiple' : '' }}>
                            <span class="uk-link uk-text-lowercase uk-text-small">
                            выбором
                        </span>
                        </div>
                        @php($_upload_allow_view = str_replace('*.(', '.', str_replace(')', '', str_replace('|', ' .', $_upload_allow))))
                        <div class="uk-text-small uk-text-muted">
                            В поле можно загрузить файлы следующих форматов:
                            <div class="uk-text-bold">{{ $_upload_allow_view }}</div>
                            @if($help = $params->get('help'))
                                <span class="uk-help-block uk-display-block">{!! $help !!}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="uk-progress-preloader">
                    <div class="uk-progress-loader"></div>
                </div>
            </div>
        </div>
    @else
        <div class="uk-form-controls">
            <div uk-form-custom="target: true"
                 class="uk-width-1-1">
                <input type="file"
                       name="{{ $params->get('name') }}"
                       value="{{ $params->get('selected') }}"
                       {{ $params->get('multiple') ? 'multiple' : NULL }}
                       autocomplete="off">
                <input
                    class="uk-input{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                    id="{{ $params->get('id') }}"
                    type="text"
                    {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                    disabled>
            </div>
            @if($help = $params->get('help'))
                <div class="uk-help-block">
                    {!! $help !!}
                </div>
            @endif
        </div>
    @endif
</div>
