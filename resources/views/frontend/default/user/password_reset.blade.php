@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-container-large">
        @include('backend.base.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
        <article class="uk-article uk-position-relative">
            <h1 class="uk-article-title uk-heading-medium uk-heading-divider">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            <div class="uk-content-body uk-margin-medium-bottom">
                @if(session('resent'))
                    <div class="uk-alert uk-alert-warning">
                        @lang('forms.messages.verify_email.resent')
                    </div>
                @else
                    <p>
                        @lang('pages.bodies.verification.line_1')
                    </p>
                    <p>
                        @lang('pages.bodies.verification.line_2')
                    </p>
                @endif
            </div>
        </article>
    </div>



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
                <div class="content-body">
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-4">
                            <div id="reset_password_form_in_page">
                                {!! csrf_field() !!}
                                <input type="hidden"
                                       name="token"
                                       value="{{ $token }}">
                                <div class="form-fields">
                                    <div class="form-group">
                                        <input type="text"
                                               name="email"
                                               id="reset-password-form-in-page-email"
                                               placeholder="{{ trans('forms.fields.reset_password.email') }}"
                                               class="form-control nrml{{ $errors->has('password') ? ' error' : NULL }}" />
                                    </div>
                                    <div class="form-group">
                                        <input type="password"
                                               name="password"
                                               id="reset-password-form-in-page-password"
                                               placeholder="{{ trans('forms.fields.reset_password.password') }}"
                                               class="form-control nrml{{ $errors->has('password') ? ' error' : NULL }}" />
                                    </div>
                                </div>
                                <div class="form-action text-center">
                                    <a href="javascript.void(0);"
                                       id="reset_password_form_in_page_button"
                                       rel="nofollow"
                                       class="btn_orange large">
                                        @lang('forms.buttons.reset_password.submit')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
