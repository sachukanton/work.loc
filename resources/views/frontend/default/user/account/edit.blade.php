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
                                @lang('frontend.tab.account_profile')
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
                                    @l(trans('frontend.tab.account_reviews'), 'personal_area.reviews')
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
                            <li>
                                @l(trans('frontend.tab.account_reviews'), 'personal_area.reviews')
                            </li>
                            <li class="active">
                                <span>
                                    @lang('frontend.tab.account_profile')
                                </span>
                            </li>
                            <li>
                                @l(trans('frontend.logout'), 'logout', ['attributes' => ['class' => 'logout-link']])
                            </li>
                        </ul>
                        <div class="tab-content">
                            @php
                                $_form_id = 'profile-edit'
                            @endphp
                            <form method="post"
                                  enctype="multipart/form-data"
                                  id="{{ $_form_id }}"
                                  action="{{ _r('ajax.profile_edit') }}">
                                {!! csrf_field() !!}
                                {!! method_field('POST') !!}
                                <input type="hidden"
                                       name="form_id"
                                       value="{{ $_form_id }}">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div id="{{ "{$_form_id}-avatar" }}">
                                            @if($_authUser->_profile->avatar_fid)
                                                {!! $_authUser->_profile->_avatar_asset('account_avatar_big',['only_way' => FALSE, 'attributes' => ['style' => 'width:100%']]) !!}
                                            @endif
                                        </div>
                                        <div class="form-group profile-file-field">
                                            <input type="file"
                                                   name="file"
                                                   value=""
                                                   autocomplete="off">
                                            <input class="uk-input"
                                                   type="text"
                                                   placeholder="{{ trans('forms.fields.profile.file') }}"
                                                   disabled="">
                                            <input type="hidden"
                                                   name="avatar"
                                                   value="{{ $_authUser->_profile->avatar_fid }}"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <div class="form-group">
                                                    <label for="{{ "{$_form_id}-name" }}">
                                                        @lang('forms.fields.profile.name')
                                                        <sup>*</sup>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control nrml"
                                                           name="name"
                                                           value="{{ $_authUser->_profile->name }}"
                                                           id="{{ "{$_form_id}-name" }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="{{ "{$_form_id}-surname" }}">
                                                        @lang('forms.fields.profile.surname')
                                                    </label>
                                                    <input type="text"
                                                           class="form-control"
                                                           name="surname"
                                                           value="{{ $_authUser->_profile->surname }}"
                                                           id="{{ "{$_form_id}-surname" }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="{{ "{$_form_id}-surname" }}">
                                                        E-mail
                                                        <sup>*</sup>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control nrml"
                                                           name="email"
                                                           value="{{ $_authUser->email }}"
                                                           id="{{ "{$_form_id}-email" }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="password hidden-xs">
                                                    <div class="password-fields">
                                                        <input type="hidden"
                                                               name="password_change"
                                                               value="0">
                                                        <div class="form-group">
                                                            <label for="{{ "{$_form_id}-surname" }}">
                                                                @lang('forms.fields.profile.password')
                                                                <sup>*</sup>
                                                            </label>
                                                            <input type="password"
                                                                   class="form-control nrml"
                                                                   name="password"
                                                                   id="{{ "{$_form_id}-email" }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="{{ "{$_form_id}-surname" }}">
                                                                @lang('forms.fields.profile.password_confirmation')
                                                                <sup>*</sup>
                                                            </label>
                                                            <input type="password"
                                                                   class="form-control nrml"
                                                                   name="password_confirmation"
                                                                   id="{{ "{$_form_id}-password_confirmation" }}">
                                                        </div>
                                                    </div>
                                                    <div class="password-label">
                                                        <i class="icon-password"></i>
                                                        <div>
                                                            @lang('forms.labels.profile.change_password')
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="visible-xs">
                                                    <div class="password">
                                                        <div class="password-label">
                                                            <i class="icon-password"></i>
                                                            <div>
                                                                @lang('forms.labels.profile.change_password')
                                                            </div>
                                                        </div>
                                                        <div class="password-fields">
                                                            <input type="hidden"
                                                                   name="password_change"
                                                                   value="0">
                                                            <div class="form-group">
                                                                <label for="{{ "{$_form_id}-surname" }}">
                                                                    @lang('forms.fields.profile.password')
                                                                    <sup>*</sup>
                                                                </label>
                                                                <input type="password"
                                                                       class="form-control nrml"
                                                                       name="password"
                                                                       id="{{ "{$_form_id}-email" }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="{{ "{$_form_id}-surname" }}">
                                                                    @lang('forms.fields.profile.password_confirmation')
                                                                    <sup>*</sup>
                                                                </label>
                                                                <input type="password"
                                                                       class="form-control nrml"
                                                                       name="password_confirmation"
                                                                       id="{{ "{$_form_id}-password_confirmation" }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="header">
                                            @lang('forms.labels.profile.additional_data')
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <div class="form-group">
                                                    <label for="{{ "{$_form_id}-surname" }}">
                                                        @lang('forms.fields.profile.company')
                                                    </label>
                                                    <input type="text"
                                                           class="form-control nrml"
                                                           name="company"
                                                           value="{{ $_authUser->_profile->company }}"
                                                           id="{{ "{$_form_id}-company" }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="{{ "{$_form_id}-surname" }}">
                                                        @lang('forms.fields.profile.phone')
                                                        <sup>*</sup>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control nrml phone-mask"
                                                           name="phone"
                                                           id="{{ "{$_form_id}-phone" }}"
                                                           value="{{ $_authUser->_profile->phone }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-5">

                                            </div>
                                        </div>
                                        <div class="form-action text-right">
                                            <button type="submit"
                                                    class="btn_green submit_application">
                                                @lang('forms.buttons.profile.submit')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
