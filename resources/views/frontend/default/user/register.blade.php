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
                <div class="uk-width-1-3 uk-margin-auto-left uk-margin-auto-right">
                    {!! $_item->registerFormOutput !!}
                </div>
            </div>
        </article>
    </div>
@endsection
