<div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
     class="uk-card uk-card-body uk-card-small uk-box-shadow-small uk-border-rounded uk-margin-bottom uk-margin-top{{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
    @if(isset($_accessEdit['block']) && $_accessEdit['block'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
    @if($_item->hidden_title == 0)
        <div class="uk-h2 uk-text-bold uk-heading-divider uk-margin-remove-top">
            {!! $_item->title !!}
        </div>
        @if($_item->sub_title)
            <div class="uk-text-meta uk-margin-bottom uk-text-center">
                {!! $_item->sub_title !!}
            </div>
        @endif
    @endif
    @if($_item->body)
        <div class="uk-card-content uk-margin-top uk-content-body">
            {!! $_item->body !!}
        </div>
    @endif
</div>
