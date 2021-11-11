<div class="file uk-position-relative">
    <div uk-grid
         class="uk-grid-collapse">
        @if($file->filemime == 'image/svg+xml')
            <div class="uk-position-relative uk-width-1-1 uk-height-1-1 uk-image">
                <input type="hidden"
                       name="{{ $_options['field'] }}[{{ $file->id }}][id]"
                       value="{{ $file->id }}">
                <button type="button"
                        data-fid="{{ $file->id }}"
                        uk-icon="icon: delete"
                        class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-position-absolute uk-position-z-index"
                        style="top: 5px; right: 5px;">
                </button>
                {!! image_render($file, 'thumb_image', ['attributes' => ['uk-svg' => TRUE, 'height' => '100']]) !!}
            </div>
        @else
            <input type="hidden"
                   name="{{ $_options['field'] }}[{{ $file->id }}][id]"
                   value="{{ $file->id }}">
            <div class="uk-width-expand">
                @l(str_limit($file->filename, 40), $file->base_url, ['attributes' => ['target' => '_blank', 'title' => $file->filename]])
                ({{ $file->filesize }}KB)
            </div>
            <div class="uk-width-auto">
                <div class="uk-button-group">
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
            </div>
        @endif
    </div>
</div>