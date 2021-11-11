@extends('frontend.default.index')

@section('content')
    <div class='product_block'>
        <div class="page_container">
            @include('frontend.default.partials.breadcrumbs', ['_items' => $_wrap['page']['breadcrumb']])
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <h1 class="page_name">
                    {!! $_wrap['page']['title'] !!}
                </h1>
                @if($_item->sub_title)
                    <div class="">
                        {!! $_item->sub_title !!}
                    </div>
                @endif
                @if(session('resent'))
                    <div class="alert alert-warning">
                        @lang('forms.messages.verify_email.resent')
                    </div>
                @endif
                <div class="content-body">
                    <div id="profile-tabs">
                        <div class="dropdown visible-sm visible-xs">
                            <button id="dLabel"
                                    type="button"
                                    class="btn btn-default"
                                    data-toggle="dropdown">
                                @lang('frontend.tab.account_reviews')
                                <span class="menu"></span>
                            </button>
                            <ul class="dropdown-menu nav">
                                <li>
                                    @l(trans('frontend.tab.account_checkout'), 'personal_area')
                                </li>
                                <li>
                                    @l(trans('frontend.tab.account_wish_list'), 'personal_area.wish_list')
                                </li>
                                <li>
                                    @l(trans('frontend.tab.account_profile'), 'personal_area.edit')
                                </li>
                                <li>
                                    @l(trans('frontend.logout'), 'logout', ['attributes' => ['class' => 'logout-link']])
                                </li>
                            </ul>
                        </div>
                        <ul class="nav nav-tabs hidden-sm hidden-xs">
                            <li>
                                @l(trans('frontend.tab.account_checkout'), 'personal_area')
                            </li>
                            <li>
                                @l(trans('frontend.tab.account_wish_list'), 'personal_area.wish_list')
                            </li>
                            <li class="active">
                                <span>
                                    @lang('frontend.tab.account_reviews')
                                </span>
                            </li>
                            <li>
                                @l(trans('frontend.tab.account_profile'), 'personal_area.edit')
                            </li>
                            <li>
                                @l(trans('frontend.logout'), 'logout', ['attributes' => ['class' => 'logout-link']])
                            </li>
                        </ul>
                        <div class="tab-content">
                            @if($_item->_items->isNotEmpty())
                                <div id="uk-items-list">
                                    @foreach($_item->_items as $_comment)
                                        @include('frontend.default.user.account.review_item', compact('_comment'))
                                    @endforeach
                                </div>
                                <div id="uk-items-list-pagination">
                                    @if(method_exists($_item->_items, 'links'))
                                        {!! $_item->_items->links('frontend.default.partials.pagination') !!}
                                    @endif
                                </div>
                            @else
                                <div class="col-sm-12">
                                    <div class="alert alert-warning">
                                        @lang('frontend.no_items')
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
