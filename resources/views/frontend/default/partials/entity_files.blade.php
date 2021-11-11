@if($_item->relatedFiles && $_item->relatedFiles->isNotEmpty())
    <div
            class="uk-grid-medium uk-child-width-1-5@l uk-child-width-1-4@m uk-child-width-1-3@s uk-child-width-1-2 uk-text-center uk-grid"
            uk-height-match="target: .title">
        @foreach($_item->relatedFiles as $_file)
            <div>
                @php
                    $_file_extension = preg_replace('/.+\./', '', $_file->filename);
                    $_file_title = $_file->title ?: $_file->base_name;
                    $_file_size = round($_file->filesize/1000);
                @endphp
                <a href="{{ formalize_path("storage/{$_file->filename}")  }}"
                   target="_blank"
                   title="{{ $_file->title ? $_file->title : '' }}"
                   class="file-type-{{ $_file_extension }} item-file uk-display-block icon-{{ $_file_extension }}">
                    <img data-src="{{ formalize_path('template/images/icon-file.png') }}"
                         alt=""
                         uk-img>
                    <div class="title">
                        {{ $_file_title }}
                    </div>
                    <div class="filesize">
                        {!! $_file_size !!} Kbyte
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif