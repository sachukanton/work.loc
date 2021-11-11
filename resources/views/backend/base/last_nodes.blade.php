@isset($_items)
    <div class="uk-position-relative uk-margin-medium-top uk-margin-medium-bottom">
        @isset($_title)
            <div class="uk-flex uk-flex-middle">
                <div class="uk-flex-1 uk-h2 uk-text-bold uk-margin-remove-top">
                    {!! $_title !!}
                </div>
            </div>
        @endisset
        <div uk-slider>
            <div class="uk-slider-container">
                <ul class="uk-slider-items uk-child-width-1-3"
                    uk-grid
                    uk-height-match="row: false">
                    @foreach($_items as $_node)
                        <li>
                            @include('backend.base.node_teaser', ['_item' => $_node, '_class' => 'uk-height-1-1'])
                        </li>
                    @endforeach
                </ul>
                <div class="uk-light">
                    <a class="uk-position-center-left uk-position-small uk-hidden-hover"
                       href="#"
                       uk-slidenav-previous
                       uk-slider-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover"
                       href="#"
                       uk-slidenav-next
                       uk-slider-item="next"></a>
                </div>
            </div>
        </div>
    </div>
@endisset