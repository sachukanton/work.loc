@if($_item->_items)
<section class="advantages">
    <div class="container">
        @if(isset($_accessEdit['block']) && $_accessEdit['block'])
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                @if($_locale == DEFAULT_LOCALE)
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                @else
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                @endif
            </div>
        @endif
        <div class="wrapper">
             @foreach($_item->_items as $_advantage)
            <div class="advantages__item">
                {!! image_render($_advantage->_icon, 'thumb_250') !!}
                <div class="advantages__item--info">
                    <h6>{!! $_advantage->title !!}</h6>
                    @if($_advantage->sub_title)
                        <p>{!! $_advantage->sub_title !!}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif