@php
    $_device_type = $_wrap['device']['type'];
@endphp

@extends('frontend.default.index')

@section('content')
        <script>
            window.catalogViewProducts = [];

            function catalogViewPush(p) {
                for (const [k, v] of Object.entries(p)) {
                    if (window.catalogViewProducts[k] == undefined) window.catalogViewProducts[k] = v
                }
            }

            @if($_item->_eCommerce->isNotEmpty())
            if (typeof gtag == "function") {
                gtag("event", "view_item_list", {items: {!! $_item->_eCommerce->toJson() !!} })
            }
            @endif
        </script>
@menuRender('2')
<section class="catalog__wrapper">
    <div class="container">
        <h2>{!! $_item->title !!}</h2>
            {{--@include('frontend.default.partials.breadcumb_node', ['_items' => $_wrap['page']['breadcrumb']])--}}
            {{--@if($_item->sub_title)--}}
            {{--<div id="uk-items-list-subtitle">--}}
            {{--<div class="sub-title uk-margin-medium-top uk-margin-small-bottom uk-text-uppercase">--}}
            {{--{!! $_item->sub_title !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}



                {{--<div class="@if(!$_item->filterPage) uk-width-auto@m @else  @endif">--}}
                        <!-- <div id="uk-items-list-title"
                             class="uk-flex uk-flex-middle">
                            @if($_item->preview_fid)
                                <div class="uk-margin-right uk-position-relative icon-preview-fid">
                                {!! image_render($_item->_preview, '', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE, 'uk-cover' => TRUE]]) !!}
                                </div>
                            @endif
                            <h1 class="title-02 uk-position-relative @if($_item->filterPage) filter-page-title @endif">
                              {!! $_wrap['page']['title'] !!}
                            </h1>
                        </div> -->
                {{--</div>--}}


                <section class="filter">
                    @if($_item->_items->isNotEmpty())
                        {!! $_item->sortOutput !!}
                    @endif


                    @if($_item->filterOutput)
                        <div id="uk-items-list-filter">
                            {!! $_item->filterOutput !!}
                        </div>
                    @endif
                </section>
           <!--  <div class="uk-grid">
                <div class="uk-width-auto">
                </div>
                <div class="uk-width-expand">
                    <div class="filter-catalog">
                        @if($_item->filterOutput)
                            <div id="uk-items-list-filter" class="uk-margin-medium-bottom open">
                            </div>
                        @endif
                   </div>
                </div>
            </div> -->
            <div class="tabs__content active">
                    {{--  {!! $_item->subCategoriesOutput !!}--}}
                    @if($_item->_items->isNotEmpty())
                        {{--{!! $_item->sortOutput !!}--}}
                        @if($_item->viewItem == 'module')
                            <div class="wrapper" id="uk-items-list">
                                {!! $_item->productOutput !!}
                            </div>
                        @else
                            <div class="wrapper" id="uk-items-list">
                                {!! $_item->productOutput !!}
                            </div>
                        @endif
                        @if(method_exists($_item->_items, 'links'))
                            <div class="pagination">
                            {!! $_item->_items->links('frontend.default.partials.pagination') !!}
                        </div>
                        @endif
                    @else
                        <div class="uk-alert uk-alert-warning">
                            @lang('frontend.no_items')
                        </div>
                    @endif
            </div>
    </div>
</section>
    @if($_item->body)
        <section class="seo__text">
            <div class="container">
                {!! $_item->body !!}
            </div>
        </section>
    @endif
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
                            @l('<span uk-icon="icon: add; ratio: .7"
                                      class="uk-margin-small-right"></span>добавить страницу',
                            'oleus.shop_filter_pages.create', ['p' => ['category' => $_item->id, 'alias' =>
                            request()->path()], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-success']])
                        @elseif($_item->filterPage)
                            @if($_locale == DEFAULT_LOCALE)
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_filter_pages.edit', ['p' => ['shop_filter_page' => $_item->filterPage->id],
                                'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                            @else
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_filter_pages.translate', ['p' => ['shop_filter_page' =>
                                $_item->filterPage->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank',
                                'class' => 'uk-link-primary']])
                            @endif
                        @else
                            @if($_locale == DEFAULT_LOCALE)
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_categories.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' =>
                                '_blank', 'class' => 'uk-link-primary']])
                            @else
                                @l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_categories.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale],
                                'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                            @endif
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    @endif
@endpush

@push('scripts')
<script src="/template/js/vue.js"
        type="text/javascript"></script>
@endpush

@push('schema')
    <script type="application/ld+json">
        {!! $_item->schema !!}
    </script>
@endpush
