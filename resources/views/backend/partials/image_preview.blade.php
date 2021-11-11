@php
    $_file_style = $_options['view'] == 'gallery' ? NULL : NULL;
@endphp
<div class="file"
     style="{{ $_file_style }}">
    @if($_options['view'] == 'gallery')
        <div style="border: 1px solid #e5e5e5; padding: 5px; margin-bottom: 10px;"
             class="uk-position-relative uk-border-rounded">
            <input type="hidden"
                   name="{{ $_options['field'] }}[{{ $file->id }}][id]"
                   value="{{ $file->id }}">
            <div class="uk-image uk-border-rounded">
                <div class="uk-button-group uk-position-absolute uk-position-z-index"
                     style="top: 5px; right: 5px;">
                    <button type="button"
                            uk-icon="icon: info"
                            data-path="{{ _r('ajax.file.update', ['file' => $file->id]) }}"
                            class="uk-button uk-button-icon uk-button-primary uk-button-small use-ajax">
                    </button>
                    <button type="button"
                            data-fid="{{ $file->id }}"
                            uk-icon="icon: delete"
                            class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-button-small">
                    </button>
                </div>
                {!! image_render($file, 'thumb_image') !!}
            </div>
            <div uk-icon="icon: grid"
                 class="uk-button uk-button-icon uk-position-absolute uk-sortable-handle uk-button-default"
                 style="top: 5px; left: 5px;">
            </div>
        </div>
    @elseif($_options['view'] == 'background')
        <div>
            <div class="uk-position-relative uk-width-1-1 uk-height-small uk-image">
                <input type="hidden"
                       name="{{ $_options['field'] }}[{{ $file->id }}][id]"
                       value="{{ $file->id }}">
                <button type="button"
                        data-fid="{{ $file->id }}"
                        uk-icon="icon: delete"
                        class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-position-absolute uk-position-z-index uk-button-small"
                        style="top: 5px; right: 5px;">
                </button>
                {!! image_render($file, 'thumb_200') !!}
            </div>
        </div>
    @elseif($_options['view'] == 'avatar')
        <div>
            <div class="uk-position-relative uk-width-1-1 uk-height-1-1 uk-image">
                <input type="hidden"
                       name="{{ $_options['field'] }}[{{ $file->id }}][id]"
                       value="{{ $file->id }}">
                <button type="button"
                        data-fid="{{ $file->id }}"
                        uk-icon="icon: delete"
                        class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-position-absolute uk-position-z-index uk-button-small"
                        style="top: 5px; right: 5px;">
                </button>
                {!! image_render($file, 'thumb_250') !!}
            </div>
        </div>
    @else
        <div>
            <div class="uk-position-relative uk-width-1-1 uk-height-1-1 uk-image">
                <input type="hidden"
                       name="{{ $_options['field'] }}[{{ $file->id }}][id]"
                       value="{{ $file->id }}">
                <div class="uk-button-group uk-position-absolute uk-position-z-index"
                     style="top: 5px; right: 5px;">
                    <button type="button"
                            uk-icon="icon: info"
                            data-path="{{ _r('ajax.file.update', ['file' => $file->id]) }}"
                            class="uk-button uk-button-icon uk-button-primary uk-button-small use-ajax">
                    </button>
                    <button type="button"
                            data-fid="{{ $file->id }}"
                            uk-icon="icon: delete"
                            class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-button-small">
                    </button>
                </div>
                {!! image_render($file, 'thumb_250') !!}
            </div>
        </div>
    @endif
</div>