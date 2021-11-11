@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-margin-auto-left uk-margin-auto-right">
        <article class="uk-article uk-position-relative">
            <h1 class="uk-article-title uk-heading-medium uk-heading-divider">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            @include('backend.base.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
            <div uk-grid
                 class="uk-child-width-1-2 uk-margin-bottom uk-grid-divider">
                <div>
                    @if($_item->slideShow && count($_item->slideShow['slide']) > 1)
                        <div class="uk-position-relative uk-visible-toggle uk-light"
                             tabindex="-1"
                             id="shop-product-slideshow"
                             uk-slideshow="animation: scale">
                            <ul class="uk-slideshow-items"
                                uk-lightbox>
                                @foreach($_item->slideShow['slide'] as $_slide)
                                    <li>
                                        {!! $_slide !!}
                                    </li>
                                @endforeach
                            </ul>
                            <a class="uk-position-center-left uk-position-small uk-hidden-hover"
                               href="#"
                               uk-slidenav-previous
                               uk-slideshow-item="previous"></a>
                            <a class="uk-position-center-right uk-position-small uk-hidden-hover"
                               href="#"
                               uk-slidenav-next
                               uk-slideshow-item="next"></a>
                        </div>
                        <div uk-slider
                             class="uk-position-relative uk-visible-toggle uk-light uk-margin-top"
                             id="shop-product-slideshow-nav"
                             tabindex="-1">
                            <ul class="uk-slider-items uk-child-width-1-5">
                                @foreach($_item->slideShow['nav'] as $_ind => $_nav)
                                    <li class="uk-padding-small-left uk-padding-small-right uk-position-relative uk-text-center uk-cursor-pointer"
                                        data-index_slide="{{ $_ind }}">
                                        {!! $_nav !!}
                                    </li>
                                @endforeach
                            </ul>
                            <a class="uk-position-center-left uk-position-small uk-hidden-hover"
                               href="#"
                               uk-slidenav-previous
                               uk-slider-item="previous"></a>
                            <a class="uk-position-center-right uk-position-small uk-hidden-hover"
                               href="#"
                               uk-slidenav-next
                               uk-slider-item="next"></a>
                        </div>
                    @else
                        {!! image_render($_item->_preview, 'slideShow_600_600', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title]]) !!}
                    @endif
                </div>
                <div>
                    <div class="uk-margin">
                        <span class="uk-text-bold">
                            SKY:
                        </span>
                        {{ $_item->sky }}
                    </div>
                    @if($_item->_category)
                        <div class="uk-margin">
                        <span class="uk-text-bold">
                            @lang('shop.labels.categories'):
                        </span>
                            @php
                                $_categories = [];
                                foreach ($_item->_category as $_category) {
                                    $_categories[] = _l($_category->title, $_category->generate_url, ['attributes' => ['target' => '_blank']]);
                                }
                                echo implode(', ', $_categories);
                            @endphp
                        </div>
                    @endif
                    @if($_item->_brand->exists)
                        <div class="uk-margin">
                            <span class="uk-text-bold">
                                @lang('shop.labels.brands'):
                            </span>
                            @l($_item->_brand->title, $_item->_brand->generate_url, ['attributes' => ['target' => '_blank']])
                        </div>
                    @endif
                    @if($_item->paramOptions)
                        @foreach($_item->paramOptions as $_param)
                            <div class="uk-margin">
                            <span class="uk-text-bold">
                                 {{ $_param['title'] }}:
                            </span>
                                {!! implode(', ', $_param['options']) !!}
                            </div>
                        @endforeach
                    @endif
                    @include('backend.base.shop_product_price')
                </div>
            </div>
            <div class="card-descriptions uk-margin-medium-top">
                @php
                    $_active_tab = request()->get('tab', 'description');
                @endphp
                <ul class="uk-tab"
                    uk-tab="connect: #uk-tab-product-information; swiping: false;">
                    <li class="{{ $_active_tab == 'description' ? 'uk-active' : NULL }}">
                        <a href="#"
                           rel="nofollow"
                           class="uk-h3 uk-margin-remove uk-position-relative uk-padding-remove">
                            @lang('frontend.tab.product_description')
                        </a>
                    </li>
                    <li class="{{ $_item->specification ? ($_active_tab == 'specifications' ? 'uk-active' : NULL) : 'uk-disabled' }}">
                        <a href="#"
                           rel="nofollow"
                           class="uk-h3 uk-margin-remove uk-position-relative uk-padding-remove">
                            @lang('frontend.tab.product_specifications', ['product' => $_item->title])
                        </a>
                    </li>
                    <li class="{{ $_active_tab == 'comments' ? 'uk-active' : NULL }}">
                        <a href="#"
                           rel="nofollow"
                           class="uk-h3 uk-margin-remove uk-position-relative uk-padding-remove">
                            @lang('frontend.tab.product_comments')
                        </a>
                    </li>
                    @if($_item->hasAttribute('relatedFiles') && $_item->relatedFiles->isNotEmpty())
                        <li>
                            <a href="#"
                               rel="nofollow"
                               class="uk-h3 uk-margin-remove uk-position-relative uk-padding-remove">
                                @lang('frontend.tab.additional_files')
                            </a>
                        </li>
                    @endif
                </ul>
                <ul id="uk-tab-product-information"
                    class="uk-switcher uk-margin">
                    <li>
                        @if($_item->body)
                            <div class="uk-content-body">
                                {!! $_item->body !!}
                            </div>
                        @else
                            <div class="uk-alert uk-alert-warning">
                                @lang('notifications.information_is_not_filled')
                            </div>
                        @endif
                    </li>
                    <li>
                        @if($_item->specifications)
                            @foreach($_item->specification as $_specification)
                                @if($_specification[1])
                                    <div class="uk-grid-small uk-grid-divider"
                                         uk-grid>
                                        <div class="uk-width-1-3 product-param-name">
                                            {!! $_specification[0] !!}
                                        </div>
                                        <div class="uk-width-2-3 product-param-value">
                                            {!! $_specification[1] !!}
                                        </div>
                                    </div>
                                @else
                                    <div class="uk-h3">
                                        {!! $_specification[0] !!}
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="uk-alert uk-alert-warning">
                                @lang('frontend.information_is_not_filled')
                            </div>
                        @endif
                    </li>
                    <li>
                        <load-component
                            entity="shop_product_comments"
                            id="{{ $_item->id }}"></load-component>
                    </li>
                    @if($_item->hasAttribute('relatedFiles') && $_item->relatedFiles->isNotEmpty())
                        <li>
                            @include('backend.base.entity_files')
                        </li>
                    @endif
                </ul>
            </div>
            @include('backend.base.shop_product_related')
        </article>
    </div>
@endsection

@push('edit_page')
    @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
        <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <button class="uk-button uk-button-color-amber"
                    type="button">
                <span uk-icon="icon: settings"></span>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click"
                 class="uk-box-shadow-small uk-padding-small">
                <ul class="uk-nav uk-dropdown-nav">
                    <li>
                        @if($_locale == DEFAULT_LOCALE)
                            @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.shop_products.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                        @else
                            @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.shop_products.translate', ['p' => ['shop_product' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    @endif
@endpush