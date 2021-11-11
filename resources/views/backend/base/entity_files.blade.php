@if($_item->relatedFiles && $_item->relatedFiles->isNotEmpty())
    <div class="uk-card uk-card-default uk-margin-medium-top uk-margin-medium-bottom uk-border-rounded uk-border-double-add uk-border-color-amber">
        <div class="uk-card-body">
            <div class="uk-h3 uk-margin-remove-top uk-heading-divider">
                @lang('frontend.block.related_files')
            </div>
            <ul class="uk-list">
                @foreach($_item->relatedFiles as $_file)
                    <li>
                        @php
                            $_file_extension = preg_replace('/.+\./', '', $_file->filename);
                            $_file_title = $_file->title ?: $_file->filename;
                        @endphp
                        <a href="{{ formalize_path("uploads/{$_file->filename}")  }}"
                           target="_blank"
                           title="{{ $_file->title ? $_file->title : '' }}"
                           class="file-type-{{ $_file_extension }} icon-{{ $_file_extension }}">
                            <span uk-icon="icon: attach_file"></span>
                            {{ $_file_title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif