@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-margin-auto-left uk-margin-auto-right">
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
            <div class="uk-article-content">
                @if($_item->items)
                    <div class="uk-overflow-auto uk-margin-bottom">
                        <table class="uk-table uk-table-small uk-table-divider">
                            <thead>
                                <tr>
                                    <th class="uk-width-medium">
                                        <div class="uk-width-medium"></div>
                                    </th>
                                    @foreach($_item->items['headers'] as $_product)
                                        <th>
                                            <div class="uk-width-medium">
                                                @l($_product->title, $_product->generate_url)
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    @foreach($_item->items['headers'] as $_product)
                                        <td class="uk-text-center">
                                            @if($_product->preview_fid)
                                                {!! $_product->_preview_asset('thumb_200', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_product->title)]]) !!}
                                            @else
                                                {!! image_render(NULL, 'thumb_200', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_product->title)]]) !!}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>SKY</td>
                                    @foreach($_item->items['headers'] as $_product)
                                        <td>
                                            {{ $_product->sky }}
                                        </td>
                                    @endforeach
                                </tr>
                                @foreach($_item->items['rows'] as $_option)
                                    <tr>
                                        <td>{{ $_option['title'] }}</td>
                                        @foreach($_item->items['headers'] as $_product)
                                            <td>{!! isset($_option['products'][$_product->id]) ? implode(', ', $_option['products'][$_product->id]) : '-//-' !!}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    @foreach($_item->items['headers'] as $_product)
                                        <td class="uk-text-center">
                                            <button class="uk-button uk-button-success uk-button-large uk-text-uppercase"
                                                    type="button">
                                                заказать
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        @lang('frontend.no_items')
                    </div>
                @endif
            </div>
        </article>
    </div>
@endsection