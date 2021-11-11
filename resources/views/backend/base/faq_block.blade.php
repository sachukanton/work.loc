<div class="uk-card uk-card-body uk-card-default uk-card-small uk-margin-medium-bottom uk-margin-top uk-border-rounded uk-box-shadow-small uk-position-relative">
    @if(isset($_accessEdit['block']) && $_accessEdit['block'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.faqs', ['attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
        </div>
    @endif
    <div class="uk-h2 uk-text-bold uk-heading-divider uk-margin-remove-top">
        FAQ
    </div>
    <div class="uk-card-content">
        <ul uk-accordion="">
            @foreach($_items as $_faq)
                <li>
                    <a class="uk-accordion-title"
                       href="#">
                        {!! strip_tags($_faq->question) !!}
                    </a>
                    <div class="uk-accordion-content uk-position-relative">
                        @if(isset($_accessEdit['faq']) && $_accessEdit['faq'])
                            <div class="uk-position-absolute uk-position-top-right">
                                @if($_locale == DEFAULT_LOCALE)
                                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.faqs.edit', ['p' => ['id' => $_faq->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                                @else
                                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.faqs.translate', ['p' => ['id' => $_faq->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                                @endif
                            </div>
                        @endif
                        {!! $_faq->answer !!}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>