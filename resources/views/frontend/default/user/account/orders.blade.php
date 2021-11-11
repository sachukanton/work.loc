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
            <div class="uk-content-body uk-margin-medium-bottom">
                @if($_item->_items->isNotEmpty())
                    <div id="uk-items-list"
                         class="panel-group"
                         role="tablist"
                         aria-multiselectable="true">
                        @foreach($_item->_items as $_order)
                            @include('frontend.default.user.account.order_item', compact('_order'))
                        @endforeach
                    </div>
                    <div id="uk-items-list-pagination">
                        @if(method_exists($_item->_items, 'links'))
                            {!! $_item->_items->links('frontend.default.partials.pagination') !!}
                        @endif
                    </div>
                @endif
            </div>
        </article>
    </div>
@endsection
