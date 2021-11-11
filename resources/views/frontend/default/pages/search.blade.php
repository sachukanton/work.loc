@extends('frontend.default.index')

@section('content')
    <article class="uk-article">
        @include('frontend.default.partials.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
        {{--<h1 class="uk-article-title uk-heading-medium uk-heading-divider">--}}
        {{--{!! $_wrap['page']['title'] !!}--}}
        {{--</h1>--}}
        {{--@if($_item->sub_title)--}}
        {{--<div class="uk-h4 uk-margin-remove-top uk-text-muted uk-margin-medium-bottom">--}}
        {{--{!! $_item->sub_title !!}--}}
        {{--</div>--}}
        {{--@endif--}}
        <div class="uk-article-content block-products">
            <div class="uk-container">
                @if($_item->_items->isNotEmpty())
                    <div id="uk-items-list"
                         class="uk-grid-match uk-items uk-grid-collapse uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2@s uk-margin-bottom uk-grid"
                         uk-height-match="target: .title">
                        {!! $_item->productOutput !!}
                    </div>
                    <div id="uk-items-list-pagination">
                        @if(method_exists($_item->_items, 'links'))
                            {!! $_item->_items->links('backend.base.pagination') !!}
                        @endif
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        @lang('frontend.no_items')
                    </div>
                @endif
                <div id="uk-items-list-body">
                    @if((is_null($_wrap['seo']['page_number']) || ($_wrap['seo']['page_number'] < 2)) && $_item->body)
                        <div class="uk-article-content uk-margin-medium-top uk-margin-medium-bottom">
                            @if($_item->body)
                                <div class="uk-content-body">
                                    {!! $_item->body !!}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </article>
@endsection

@push('edit_page')
@if($_authUser && $_authUser->can('pages_update'))
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
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.pages.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    @else
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.pages.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    @endif
                </li>
            </ul>
        </div>
    </div>
@endif
@endpush