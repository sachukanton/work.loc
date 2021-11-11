@extends('frontend.default.index')

@section('content')
    <div class="uk-container">
        <article class="uk-article uk-position-relative page-user">
            <h1 class="title-01 title-default uk-position-relative uk-position-z-index">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            <div class="uk-content-body uk-margin-medium-bottom">
                <div class="uk-width-1-2@m uk-width-2-3@s uk-margin-auto-left uk-margin-auto-right uk-text-center">
                    {!! $_item->loginFormOutput !!}
                </div>
            </div>
        </article>
    </div>
@endsection
