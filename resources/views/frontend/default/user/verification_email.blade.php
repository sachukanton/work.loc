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
@endsection
