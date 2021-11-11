@if($_items->isNotEmpty())
    <div class="uk-container-list-product view-lists-product front-products">
        <div class="uk-container uk-container-xlarge">
            <div class="uk-flex-middle uk-grid">
                @isset($_title)
                    <div class="uk-width-expand@s">
                        <h2 class="title-02 uk-position-relative uk-position-z-index">
                            {!! $_title !!}
                        </h2>
                    </div>
                @endisset
                <div class="uk-width-auto@s uk-flex uk-flex-wrap uk-flex-middle icons-full">
                    <div class="uk-label-box icon-img-full uk-position-relative">
                        <label>
                            <input class="uk-checkbox" type="checkbox">
                            <span>
                            @lang('frontend.titles.image')
                        </span>
                        </label>
                    </div>
                    <div class="uk-label-box icon-consist-full uk-position-relative">
                        <label>
                            <input class="uk-checkbox" type="checkbox">
                            <span>
                            @lang('frontend.titles.consist')
                        </span>
                        </label>
                    </div>
                </div>
            </div>
            <ul class="uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2 uk-grid-column-large uk-grid"
                uk-height-match="target: .preview-fid">
                @foreach($_items as $_product)
                    @include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => 'uk-height-1-1'])
                @endforeach
            </ul>
        </div>
    </div>
@endif
