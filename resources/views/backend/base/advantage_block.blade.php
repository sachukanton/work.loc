<div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
     class="uk-card uk-margin-large-bottom uk-margin-top{{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
    @if(isset($_accessEdit['advantage']) && $_accessEdit['advantage'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.advantages.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.advantages.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
    <div class="uk-h2 uk-text-center uk-heading-divider uk-margin-remove-top">
        {!! $_item->title !!}
    </div>
    @if($_item->sub_title)
        <div class="uk-text-meta uk-margin-bottom uk-text-center">
            {!! $_item->sub_title !!}
        </div>
    @endif
    <div class="uk-grid-small uk-child-width-1-4@m uk-child-width-1-2@s uk-flex-center uk-text-center"
         uk-grid>
        @foreach($_item->_items as $_advantage)
            <div>
                <div class="uk-card uk-card-default uk-card-body uk-card-small">
                    @if($_advantage->icon_fid)
                        <div class="uk-text-center">
                            {!! image_render($_advantage->_icon, 'thumb_250') !!}
                        </div>
                    @endif
                    <div class="uk-text-center uk-heading-divider">
                        <h3 class="uk-text-bold">
                            {!! $_advantage->title !!}
                        </h3>
                        @if($_advantage->sub_title)
                            <div class="uk-text-meta uk-margin-bottom uk-text-center uk-margin-remove">
                                {!! $_advantage->sub_title !!}
                            </div>
                        @endif
                    </div>
                    @if($_advantage->body)
                        <div class="uk-card-content uk-margin-small-top uk-content-body uk-text-bold">
                            {!! $_advantage->body !!}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @if($_item->body)
        <div class="uk-card-content uk-margin-top uk-content-body uk-text-center">
            {!! $_item->body !!}
        </div>
    @endif
</div>