<div class="blogs__item">
    <a href="{{ $_item->generate_url }}"
           rel="nofollow">
                @if($_item->preview_fid)
                    <div class="blogs__item--img">
                        {!! $_item->_preview_asset('nodeTeaser_400_300', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title)]]) !!}
                    </div>
                @endif
    </a>
    <div class="node-teaser">
        <h6>
            <a href="{{ $_item->generate_url }}"
               rel="nofollow">
            {!! $_item->title !!}
            </a>
        </h6>
        <span class="blogs__item_date">
            {{ $_item->published_at->format('d.m.Y') }}
        </span>
        {{--<div class="teaser">--}}
            {{--{!! str_limit(strip_tags($_item->teaser), 520) !!}--}}
        {{--</div>--}}
        {{--@if($_item->_tags->isNotEmpty())--}}
        {{--@foreach($_item->_tags as $_tag)--}}
        {{--@l($_tag->title, $_tag->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'uk-link-color-pink uk-text-small uk-margin-small-right']])--}}
        {{--@endforeach--}}
        {{--@endif--}}
    </div>
    @if(isset($_accessEdit['node']) && $_accessEdit['node'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.nodes.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.nodes.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
</div>