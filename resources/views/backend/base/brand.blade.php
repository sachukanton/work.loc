@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-margin-auto-left uk-margin-auto-right">
        <article class="uk-article">
            <h1 class="uk-article-title uk-heading-medium uk-heading-divider">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="uk-h4 uk-margin-remove-top uk-text-muted uk-margin-medium-bottom">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            @include('backend.base.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
            <div class="uk-article-content">
                @if($_item->_items->isNotEmpty())
                    <div class="uk-grid uk-margin-bottom">
                        <div class="uk-width-1-4">
                            <div class="uk-h3">
                                @lang('frontend.block.brand_categories_title')
                            </div>
                            <ul class="uk-nav-default uk-nav-parent-icon"
                                uk-nav>
                                @foreach($_item->_categories as $_category)
                                    @if($_category['use'])
                                        @if($_category['children'])
                                            <li class="uk-parent">
                                                @l($_category['title'], $_category['alias'] . "-cfp-brands-{$_item->name}")
                                                <ul class="uk-nav-sub">
                                                    @foreach($_category['children'] as $_sub_category)
                                                        @if($_sub_category['use'])
                                                            <li>
                                                                @l($_sub_category['title'], $_sub_category['alias'] . "-cfp-brands-{$_item->name}")
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
                        <div class="uk-width-3-4">
                            <div class="uk-items-list uk-margin-bottom">
                                <div id="uk-items-list"
                                     class="uk-margin-bottom uk-child-width-1-3"
                                     uk-grid
                                     uk-height-match="row: false">
                                    @foreach($_item->_items as $_product)
                                        @include('backend.base.shop_product_teaser', ['_item' => $_product])
                                    @endforeach
                                </div>
                            </div>
                            @if(method_exists($_item->_items, 'links'))
                                {!! $_item->_items->links('backend.base.pagination') !!}
                            @endif
                        </div>
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        No items
                    </div>
                @endif
                @if((is_null($_wrap['seo']['page_number']) || ($_wrap['seo']['page_number'] < 2)) && $_item->body)
                    <div class="uk-article-content">
                        @if($_item->body)
                            <div class="uk-content-body uk-margin-medium-bottom">
                                {!! $_item->body !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </article>
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