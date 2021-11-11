@extends('backend.index')

@section('content')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin uk-text-color-teal">
                {!! $_wrap['seo']['title'] !!}
            </h1>
        </div>
        @include('backend.shop.partials.box_new_orders')
        @include('backend.shop.partials.box_last_complete_orders')
        @isset($_others['journal'])
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
                <div class="uk-card-body">
                    <h2 class="uk-heading-line"><span>Журнал событий</span></h2>
                    <ul class="uk-list">
                        @foreach($_others['journal'] as $_message)
                            <li class="uk-alert-{{ $_message->class }} uk-padding-small uk-border-rounded">
                                <div uk-grid>
                                    <div class="uk-width-auto">
                                        {{ $_message->created_at }}
                                    </div>
                                    <div class="uk-width-expand">
                                        {!! $_message->message !!}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endisset
        <div id="load-update-order-lists"></div>
        <script>
            window.update_order_lists = true;
            window.update_order_lists_last_create_at = null;
        </script>
    </article>
@endsection
