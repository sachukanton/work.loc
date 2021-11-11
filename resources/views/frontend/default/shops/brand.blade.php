@php
    $_device_type = $_wrap['device']['type'];
@endphp

@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-container-expand">
        @include('frontend.default.partials.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
        <div>
            <h1 class="title-category uk-text-uppercase">
                {!! $_wrap['page']['title'] !!}
            </h1>
        </div>
        <div uk-grid>
            <div class="uk-width-350">
                @if($_device_type != 'mobile')
                <div class="filter-catalog uk-margin-medium-bottom">
                    @if($_item->_items->isNotEmpty())
                        <div>
                            <ul class="uk-nav nav-bar-menu uk-nav-default uk-nav-parent-icon"
                                uk-nav="multiple: true">
                                @foreach($_item->_categories as $_category)
                                    @if($_category['use'])
                                        @if($_category['children'])
                                            <li class="uk-parent">
                                                @l($_category['title'], $_category['alias'] . "-cfp-brands-{$_item->name}", ['attributes' => ['class' => 'level-item-1 uk-text-uppercase']])
                                                <ul class="uk-nav-sub">
                                                    @foreach($_category['children'] as $_sub_category)
                                                        @if($_sub_category['use'])
                                                            <li>
                                                                @l($_sub_category['title'], $_sub_category['alias'] . "-cfp-brands-{$_item->name}", ['attributes' => ['class' => 'level-item-2 uk-text-uppercase']])
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li>
                                                @l($_category['title'], $_category['alias'])
                                            </li>
                                        @endif
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @menuRender('8', ['view' => 'frontend.default.menus.catalog_menu_on_catalog'])
                    <load-component
                        entity="shop_product_last_view"></load-component>
                </div>
                @endif
            </div>
            <div class="uk-width-expand@s">
                <article class="uk-article uk-position-relative">
                    @if($_item->_items->isNotEmpty())
                        <div id="uk-items-list"
                             class="uk-margin-bottom uk-child-width-1-3@l uk-child-width-1-2@m uk-grid-small"
                             uk-grid
                             uk-height-match="row: false">
                            @foreach($_item->_items as $_product)
                                @include('frontend.default.shops.product_teaser', ['_item' => $_product])
                            @endforeach
                        </div>
                        <div id="uk-items-list-pagination">
                            @if(method_exists($_item->_items, 'links'))
                                {!! $_item->_items->links('frontend.default.partials.pagination') !!}
                            @endif
                        </div>
                    @else
                        <div class="uk-alert uk-alert-warning">
                            @lang('frontend.no_items')
                        </div>
                    @endif
                    <div id="uk-items-list-body">
                        @if($_item->body)
                            <div class="seo-text">
                                {!! $_item->body !!}
                            </div>
                        @endif
                    </div>

                        @if($_device_type == 'mobile')
                            <div class="filter-catalog uk-margin-medium-bottom">
                                @if($_item->_items->isNotEmpty())
                                    <div>
                                        <ul class="uk-nav nav-bar-menu uk-nav-default uk-nav-parent-icon"
                                            uk-nav="multiple: true">
                                            @foreach($_item->_categories as $_category)
                                                @if($_category['use'])
                                                    @if($_category['children'])
                                                        <li class="uk-parent">
                                                            @l($_category['title'], $_category['alias'] . "-cfp-brands-{$_item->name}", ['attributes' => ['class' => 'level-item-1 uk-text-uppercase']])
                                                            <ul class="uk-nav-sub">
                                                                @foreach($_category['children'] as $_sub_category)
                                                                    @if($_sub_category['use'])
                                                                        <li>
                                                                            @l($_sub_category['title'], $_sub_category['alias'] . "-cfp-brands-{$_item->name}", ['attributes' => ['class' => 'level-item-2 uk-text-uppercase']])
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li>
                                                            @l($_category['title'], $_category['alias'])
                                                        </li>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @menuRender('8', ['view' => 'frontend.default.menus.catalog_menu_on_catalog'])
                                <load-component
                                        entity="shop_product_last_view"></load-component>
                            </div>
                        @endif

                </article>
            </div>
        </div>
    </div>
@endsection

@push('edit_page')
    @isset($_accessEdit['brand'])
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
                            @l('Редактировать', 'oleus.shop_brands.edit', ['p' => ['shop_brand' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                        @else
                            @l('Редактировать', 'oleus.shop_brands.translate', ['p' => ['shop_brand' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    @endisset
@endpush

@push('schema')
    <script type="application/ld+json">
        {!! $_item->schema !!}
    </script>
@endpush