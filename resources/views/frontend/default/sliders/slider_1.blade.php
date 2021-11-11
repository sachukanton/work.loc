@php
    global $wrap;
    $_device_type = $wrap['device']['type'];
@endphp

<section class="intro">
    <div class="container">
@if($_item->_items->isNotEmpty())
    <div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
             class="swiper-container {{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
        <div class="swiper-wrapper">
            @foreach($_item->_items as $_slide)
            <div class="swiper-slide">
                @if(!$_slide->hidden_title)
                    <div class="wrapper">
                        <div class="intro__info">
                            <h1>{!! $_slide->title !!}</h1>
                            @if($_slide->body)
                                {!! $_slide->body !!}
                            @endif
                        </div>  
                        <div class="intro__img">
                            {!! $_slide->_background_asset(NULL, ['only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title)]]) !!}
                        </div>
                    </div>
                @else
                <div class="wrappers">
                    <div class="intro__img">
                        {!! $_slide->_background_asset(NULL, ['only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title)]]) !!}
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if($_item->dotnav)
            <div class="swiper-pagination"></div>
        @endif
        @if(isset($_accessEdit['slider']) && $_accessEdit['slider'])
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                @if($_locale == DEFAULT_LOCALE)
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.sliders.edit', ['p' => ['id'
                    => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block
                    uk-line-height-1']])
                @else
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.sliders.translate', ['p' =>
                    ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class'
                    => 'uk-display-block uk-line-height-1']])
                @endif
            </div>
        @endif
    </div>
@endif
    </div>
</section>

