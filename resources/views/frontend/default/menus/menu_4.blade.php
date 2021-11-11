<div class="uk-position-relative">
    <ul {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }} class="uk-nav uk-flex uk-flex-wrap {{ $_item->style_class }}">
        @foreach($_item->menu_items as $_menu_item)
            @include('frontend.default.menus.menu_4_item', ['_item' => $_menu_item])
        @endforeach
    </ul>
    @if(isset($_accessEdit['menu']) && $_accessEdit['menu'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-position-z-index">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.menus.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.menus.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
</div>
