<div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
     class="container {{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
    @if(isset($_accessEdit['banner']) && $_accessEdit['banner'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right main_banner">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.banners.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.banners.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
    {!! $_item->link ? '<a href="'. _u($_item->link) .'" '. ($_item->link_attributes ?: NULL) .' class="uk-display-block">' : NULL !!}
    @if($_item->background_fid)
        {!! $_item->_background_asset(NULL, ['only_way' => FALSE, 'attributes' => ['class' => 'uk-display-block uk-width-1-1']]) !!}
    @else
        {!! content_render($_item) !!}
    @endif
    {!! $_item->link ? '</a>' : NULL !!}
</div>
