@if($_item->_items->isNotEmpty())
    <div {{ $_item->style_id ? "id=\"{$_item->style_id}\"" : NULL }}
         class="uk-position-relative{{ $_item->style_class ? " {$_item->style_class}" : NULL }}"
         uk-slideshow="{{ $_item->options }}">
        <ul class="uk-slideshow-items">
            @foreach($_item->_items as $_slide)
                <li {{ $_slide->attributes }}>
                    @php
                        if($_slide->link){
                            $_slide_link = _u($_slide->link);
                            echo "<a href=\"{$_slide_link}\" class=\"uk-display-block uk-height-1-1 uk-cover-container uk-position-relative\">";
                        }else{
                            echo "<div class=\"uk-height-1-1 uk-cover-container uk-position-relative\">";
                        }
                    @endphp
                    <img data-src="{{ $_slide->_background_asset($_item->preset, ['only_way' => TRUE]) }}"
                         uk-cover
                         uk-img
                         alt="{!! $_slide->title !!}">
                    <div class="uk-container-large uk-position-relative uk-margin-auto-left uk-margin-auto-right uk-height-1-1">
                        @if(!$_slide->hidden_title)
                            <div class="uk-position-center uk-position-small uk-text-center slider-content">
                                <h2 class="uk-text-uppercase">
                                    {!! $_slide->title !!}
                                </h2>
                                @if($_slide->sub_title)
                                    <div class="uk-text-meta uk-margin-bottom">
                                        {!! $_slide->sub_title !!}
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if($_slide->body )
                            <div class="uk-slider-content uk-position-absolute uk-position-bottom-center">
                                <div class="uk-card uk-card-body uk-card-small">
                                    {!! $_slide->body !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    @php
                        if($_slide->link){
                            echo "</a>";
                        }else{
                            echo "</div>";
                        }
                    @endphp
                </li>
            @endforeach
        </ul>
        @if($_item->slidenav)
            <a class="uk-position-center-left uk-position-small uk-hidden-hover"
               href="#"
               uk-slidenav-previous
               uk-slideshow-item="previous"></a>
            <a class="uk-position-center-right uk-position-small uk-hidden-hover"
               href="#"
               uk-slidenav-next
               uk-slideshow-item="next"></a>
        @endif
        @if($_item->dotnav)
            <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin"></ul>
        @endif
        @if(isset($_accessEdit['advantage']) && $_accessEdit['advantage'])
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                @if($_locale == DEFAULT_LOCALE)
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.sliders.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                @else
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.sliders.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                @endif
            </div>
        @endif
    </div>
@endif