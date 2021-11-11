@extends('backend.index')

@section('content')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin uk-text-color-teal">
                {!! $_wrap['seo']['title'] !!}
            </h1>
        </div>
        @if($_items->before)
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
                @if($_items->before['header'])
                    <div class="uk-card-header">
                        <h2 class="uk-text-uppercase">
                            {!! $_items->before['header'] !!}
                        </h2>
                    </div>
                @endif
                <div class="uk-card-body">
                    {!! $_items->before['body'] !!}
                </div>
                @if($_items->before['footer'])
                    <div class="uk-card-footer uk-text-right">
                        {!! $_items->before['footer'] !!}
                    </div>
                @endif
            </div>
        @endif
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
            @if($_items->buttons || $_items->filters)
                <div class="uk-card-header">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-expand">
                            @if($_items->filters)
                                <button uk-toggle="target: #items-filter"
                                        class="uk-button uk-button-primary uk-border-rounded uk-margin-small-right uk-text-uppercase"
                                        type="button">
                                    <span uk-icon="icon: filter_list"></span>&nbsp;
                                    Фильтровать
                                </button>
                            @endif
                        </div>
                        <div>
                            @foreach($_items->buttons as $_button)
                                {!! $_button !!}
                            @endforeach
                        </div>
                    </div>
                    @if($_items->filters)
                        <div id="items-filter"
                             class="uk-margin-small-top"
                            {{ $_items->use_filters ?: 'hidden' }}>
                            <form action=""
                                  method="get">
                                <div class="uk-grid uk-grid-small uk-flex uk-flex-bottom">
                                    <div class="uk-width-expand"
                                         style="border-right: 1px #e4e9f0 solid;">
                                        <div class="uk-grid uk-grid-small">
                                            @foreach($_items->filters as $_field)
                                                <div class="uk-margin-small-top {{ $_field['class'] ?? 'uk-width-medium' }}">
                                                    {!! $_field['data'] !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="uk-width-auto uk-padding-small-bottom">
                                        <button type="submit"
                                                name="filter"
                                                value="1"
                                                class="uk-button uk-button-primary uk-button-icon uk-border-rounded uk-margin-small-right"
                                                uk-icon="filter_list"></button>
                                        <button type="submit"
                                                name="clear"
                                                value="1"
                                                class="uk-button uk-button-danger uk-button-icon uk-border-rounded"
                                                uk-icon="cancel"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
            <div class="uk-card-body">
                @if($_items->items->isNotEmpty())
                    <table
                        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small uk-margin-remove">
                        <thead>
                            <tr>
                                @foreach($_items->headers as $_td)
                                    <th class="{{ $_td['class'] ?? NULL }}"
                                        style="{{ $_td['style'] ?? NULL }}">
                                        {!! $_td['data'] ?? NULL !!}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($_items->items as $_item)
                                <tr class="{{ $_item['class'] ?? NULL }}"
                                    {{ $_item['attributes'] ?? NULL }}
                                    id="{{ $_item['id'] ?? NULL }}">
                                    @foreach(($_item['data'] ?? $_item) as $_key => $_td)
                                        @if(is_string($_td))
                                            <td class="{{ $_items->headers[$_key]['class'] ?? NULL }}"
                                                style="{{ $_items->headers[$_key]['style'] ?? NULL }}">
                                                {!! $_td !!}
                                            </td>
                                        @else
                                            <td class="{{ $_items->headers[$_key]['class'] ?? NULL }} {{ $_td['class'] ?? NULL }}"
                                                id="{{ $_td['id'] ?? NULL }}"
                                                {{ $_td['attributes'] ?? NULL }}
                                                style="{{ $_items->headers[$_key]['style'] ?? NULL }} {{ $_td['style'] ?? NULL }}">
                                                {!! $_td['data'] !!}
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($_items->pagination)
                        <div class="uk-clearfix">
                            {!! $_items->pagination !!}
                        </div>
                    @endisset
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded">
                        Список пуст
                    </div>
                @endif
            </div>
        </div>
        @if($_items->after)
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
                @if($_items->after['header'])
                    <div class="uk-card-header">
                        <h2 class="uk-text-uppercase">
                            {!! $_items->after['header'] !!}
                        </h2>
                    </div>
                @endif
                <div class="uk-card-body">
                    {!! $_items->after['body'] !!}
                </div>
                @if($_items->after['footer'])
                    <div class="uk-card-footer uk-text-right">
                        {!! $_items->after['footer'] !!}
                    </div>
                @endif
            </div>
        @endif
    </article>
@endsection