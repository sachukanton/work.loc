<div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
     class="uk-block uk-position-relative {{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
    @if(isset($_accessEdit['block']) && $_accessEdit['block'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
    <div class="uk-container uk-container-xlarge">
        <div class="uk-child-width-1-2@m uk-grid-collapse uk-grid uk-grid-block">
            <div>
                <div class="item-info uk-position-relative uk-position-z-index">
                    @if($_item->hidden_title == 0)
                        <div class="uk-grid-small uk-flex-bottom uk-grid">
                            <div class="title uk-width-expand@s">
                                {!! $_item->title !!}
                            </div>
                            @if($_item->sub_title)
                                <div class="sub-title uk-width-auto@s">
                                    {!! $_item->sub_title !!}
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="persons-img">
                        <img uk-img="data-src:{{ formalize_path('template/images/persons.png') }}"
                             alt="">
                    </div>
                    @if($_item->body)
                        <div class="uk-content-body">
                            {!! $_item->body !!}
                        </div>
                    @endif
                </div>
            </div>
            <div></div>
        </div>
    </div>
    @if($_item->relatedMedias && $_item->relatedMedias->isNotEmpty())
        <div class="block-medias uk-position-right uk-position-z-index uk-width-1-2@m uk-height-1-1 uk-overflow-hidden" tabindex="-1" uk-slider>
            <ul class=" uk-slider-items uk-height-1-1 uk-child-width-1-1 uk-grid-collapse uk-grid">
                @foreach($_item->relatedMedias as $_file)
                    <li>
                        {!! image_render($_file, 'thumb_960_725', ['only_way' => FALSE, 'attributes' => ['uk-cover' => TRUE]]) !!}
                    </li>
                @endforeach
            </ul>
            <a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
            <a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
        </div>
    @endif
</div>
