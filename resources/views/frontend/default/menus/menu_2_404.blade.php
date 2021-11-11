@php
    global $wrap;
    $_device_type = $wrap['device']['type'];
@endphp
   <section class="category">
    <div class="container">
        <div class="wrapper">
            @foreach($_item->menu_items as $_menu_item)
                @include('frontend.default.menus.menu_2_item', ['_item' => $_menu_item])
            @endforeach
        </div>
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
</section>
