@php
$_device_type = $_wrap['device']['type'];
@endphp
@extends('frontend.default.index')

@section('content')

    <div class="container">
        <h2 class="page_title">{!! $_wrap['page']['title'] !!}</h2>
    </div>
    @menuRender('2')
    <section class="blog">
        <div class="container">
            <div class="blog-list">
            @if($_device_type == 'pc')
                @if($_item->_items->currentPage() == 1)
                    <div class="blog__top">
                        <div class="blog__top--img">
                            {!! $_item->_items[0]->_preview_asset('nodeTeaser_400_300', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title)]]) !!}
                        </div>
                        <div class="blog__top--info">
                            <h2>{!! $_item->_items[0]->title !!}</h2>
                            <span class="blog__top_date">{{ $_item->_items[0]->published_at->format('d.m.Y') }}</span>
                            <p>{!! str_limit(strip_tags($_item->_items[0]->body), 200) !!}</p>
                            <a href="{!! $_item->_items[0]->generate_url !!}" class="btn">{!! variable('show_more') !!}</a>
                        </div>
                    </div>
                @endif
                @if($_item->_items->isNotEmpty())
                <div class="blogs">
                    @if($_item->_items->currentPage() == 1)
                        @foreach($_item->_items as $_node)
                            @if($_item->_items[0] === $_node)

                            @else
                                @include('frontend.default.nodes.node_teaser', ['_item' => $_node])
                            @endif
                        @endforeach
                    @else
                        @foreach($_item->_items as $_node)
                            @include('frontend.default.nodes.node_teaser', ['_item' => $_node])
                        @endforeach
                @endif
                </div>
                <div class="pagination">
                    @if(method_exists($_item->_items, 'links'))
                        {!! $_item->_items->links('frontend.default.partials.pagination') !!}
                    @endif
                </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        @lang('frontend.no_items')
                    </div>
                @endif
            @else
                @if($_item->_items->isNotEmpty())
                <div class="blogs">
                    @foreach($_item->_items as $_node)
                        @include('frontend.default.nodes.node_teaser', ['_item' => $_node])
                    @endforeach
                </div>
                <div class="pagination">
                    @if(method_exists($_item->_items, 'links'))
                        {!! $_item->_items->links('frontend.default.partials.pagination') !!}
                    @endif
                </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        @lang('frontend.no_items')
                    </div>
                @endif
            @endif
                <div id="uk-items-list-body">
                    @if((is_null($_wrap['seo']['page_number']) || ($_wrap['seo']['page_number'] < 2)) && $_item->body)
                        <div>
                            @if($_item->body)
                                <div class="seo__text">
                                    {!! $_item->body !!}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('edit_page')
@if($_authUser && $_authUser->can('pages_update'))
    <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
        <button class="uk-button uk-button-color-amber"
                type="button">
            <span uk-icon="icon: settings"></span>
        </button>
        <div uk-dropdown="pos: bottom-right; mode: click"
             class="uk-box-shadow-small uk-padding-small">
            <ul class="uk-nav uk-dropdown-nav">
                <li>
                    @if($_locale == DEFAULT_LOCALE)
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.pages.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    @else
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.pages.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    @endif
                </li>
            </ul>
        </div>
    </div>
@endif
@endpush