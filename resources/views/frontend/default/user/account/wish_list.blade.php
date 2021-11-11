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
                                @lang('frontend.tab.account_wish_list')
                                <span class="menu"></span>
                            </button>
                            <ul class="dropdown-menu nav">
                                <li>
                                    @l(trans('frontend.tab.account_checkout'), 'personal_area')
                                </li>
                                <li>
                                    @l(trans('frontend.tab.account_reviews'), 'personal_area.reviews')
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
                            <li class="active">
                                <span>
                                    @lang('frontend.tab.account_wish_list')
                                </span>
                            </li>
                            <li>
                                @l(trans('frontend.tab.account_reviews'), 'personal_area.reviews')
                            </li>
                            <li>
                                @l(trans('frontend.tab.account_profile'), 'personal_area.edit')
                            </li>
                            <li>
                                @l(trans('frontend.logout'), 'logout', ['attributes' => ['class' => 'logout-link']])
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="wish-list-form">
                                <button class="btn add-wish-list"
                                        type="button"
                                        data-toggle="collapse"
                                        data-target="#collapse-add-new-list">
                                    @lang('forms.buttons.wish_list.add_new_list')
                                </button>
                                <div class="clearfix"></div>
                                <div class="collapse"
                                     id="collapse-add-new-list">
                                    @php
                                        $_form_id = 'ads-new-wish-list';
                                    @endphp
                                    <form method="post"
                                          enctype="multipart/form-data"
                                          id="{{ $_form_id }}"
                                          action="{{ _r('ajax.add_wish_list') }}">
                                        {!! csrf_field() !!}
                                        {!! method_field('POST') !!}
                                        <input type="hidden"
                                               name="form_id"
                                               value="{{ $_form_id }}">
                                        <div class="row">
                                            <div class="form-group col-sm-10 col-xs-7">
                                                <input type="text"
                                                       name="name"
                                                       class="form-control nrml"
                                                       id="{{ "{$_form_id}-name" }}"
                                                       placeholder="{{ trans('forms.fields.wish_list.name') }}">
                                            </div>
                                            <div class="form-group col-sm-2 col-xs-5">
                                                <button type="submit"
                                                        class="btn_green submit_form">
                                                    @lang('forms.buttons.wish_list.submit')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @include('frontend.default.user.account.wish_list_items')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
