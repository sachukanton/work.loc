<div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
     class="uk-position-relative {{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
    @if(isset($_accessEdit['banner']) && $_accessEdit['banner'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.banners.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.banners.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
    {!! $_item->link ? '<a href="'. _u($_item->link) .'" '. ($_item->link_attributes ?: NULL) .'>' : NULL !!}
    @if($_banner->background_fid)
        {!! $_banner->_background_asset(NULL, ['only_way' => FALSE]) !!}
    @else
        {!! content_render($_banner) !!}
    @endif
    {!! $_banner->link ? '</a>' : NULL !!}
</div>
