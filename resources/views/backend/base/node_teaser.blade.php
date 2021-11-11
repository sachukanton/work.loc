<div class="{{ isset($_class) ? " {$_class} " : NULL }}">
    <div class="uk-card uk-card-small uk-card-default uk-border-rounded uk-box-shadow-small uk-height-1-1 uk-position-relative">
        <div class="uk-card-media-top">
            <a href="{{ $_item->generate_url }}"
               rel="nofollow">
                <div class="uk-cover-container uk-height-small">
                    @if($_item->preview_fid)
                        {!! $_item->_preview_asset('nodeTeaser_300_150', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-cover' => TRUE]]) !!}
                    @else
                        {!! image_render(NULL, 'nodeTeaser_300_150', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-cover' => TRUE]]) !!}
                    @endif
                </div>
            </a>
        </div>
        <div class="uk-card-body">
            <div class="uk-text-small uk-text-primary">
                {{ $_item->published_at->format('d.m.Y') }}
            </div>
            <div class="uk-h3 uk-card-title uk-margin-remove-top uk-margin-small-bottom">
                @l(str_limit(strip_tags($_item->title), 20), $_item->generate_url, ['attributes' => ['title' => strip_tags($_item->title), 'class' => 'uk-text-color-teal']])
            </div>
            {!! $_item->teaser !!}
            @if($_item->_tags)
                <hr>
                @foreach($_item->_tags as $_tag)
                    @l($_tag->title, $_tag->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'uk-link-color-pink uk-text-small uk-margin-small-right']])
                @endforeach
            @endif
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
</div>