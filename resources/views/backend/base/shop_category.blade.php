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
            <div class="uk-grid uk-margin-bottom">
                @if($_item->filterOutput)
                    <div class="uk-width-1-4">
                        <div id="uk-items-list-filter">
                            {!! $_item->filterOutput !!}
                        </div>
                    </div>
                    <div class="uk-width-3-4">
                        {!! $_item->subCategoriesOutput !!}
                        @if($_item->_items)
                            {!! $_item->sortOutput !!}
                            @if($_item->viewItem == 'module')
                                <div id="uk-items-list"
                                     class="uk-margin-bottom uk-child-width-1-3"
                                     uk-grid
                                     uk-height-match="row: false">
                                    {!! $_item->productOutput !!}
                                </div>
                            @else
                                <div id="uk-items-list"
                                     class="uk-margin-bottom">
                                    {!! $_item->productOutput !!}
                                </div>
                            @endif
                            <div id="uk-items-list-pagination">
                                @if(method_exists($_item->_items, 'links'))
                                    {!! $_item->_items->links('backend.base.pagination') !!}
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="uk-width-1-1">
                        <div class="uk-alert uk-alert-warning">
                            @lang('frontend.no_items')
                        </div>
                    </div>
                @endif
            </div>
            <div id="uk-items-list-body">
                @if($_item->body)
                    <div class="uk-content-body uk-margin-medium-bottom">
                        {!! $_item->body !!}
                    </div>
                @endif
            </div>
        </article>
    </div>
@endsection

@push('edit_page')
    @if(isset($_accessEdit['shop_category']) && $_accessEdit['shop_category'])
        <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <button class="uk-button uk-button-color-amber"
                    type="button">
                <span uk-icon="icon: settings"></span>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click"
                 class="uk-box-shadow-small uk-padding-small">
                <ul class="uk-nav uk-dropdown-nav">
                    <li>

                        @if($_item->filterPage === TRUE)
                            @l('<span uk-icon="icon: add; ratio: .7" class="uk-margin-small-right"></span>добавить страницу', 'oleus.shop_filter_pages.create', ['p' => ['category' => $_item->id, 'alias' => request()->path()], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-success']])
                        @elseif($_item->filterPage)
                            @if($_locale == DEFAULT_LOCALE)
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.shop_filter_pages.edit', ['p' => ['shop_filter_page' => $_item->filterPage->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                            @else
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.shop_filter_pages.translate', ['p' => ['shop_filter_page' => $_item->filterPage->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                            @endif
                        @else
                            @if($_locale == DEFAULT_LOCALE)
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.shop_categories.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                            @else
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.shop_categories.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                            @endif
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    @endif
@endpush