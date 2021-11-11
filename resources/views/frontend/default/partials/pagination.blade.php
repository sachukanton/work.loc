@if($paginator->hasPages())
        @php
            $_wrap = wrap()->get();
            $_locale = $_wrap['locale'];
            $_url_locale = $_locale != DEFAULT_LOCALE ? "/{$_locale}/" : '/';
            $_current_page = $paginator->currentPage();
            $_total = $paginator->total();
            $_current_url = $_wrap['seo']['url_alias'];
            $_current_url_query = $_wrap['seo']['url_query'];
            $_next_page_link = $_wrap['seo']['link_next'] ?? NULL;
            $_prev_page_link = $_wrap['seo']['link_prev'] ?? NULL;
            $_per_page = $paginator->perPage();
            $_count_showing = $_per_page * $_current_page;
            $_load_more_number = $_total - $_count_showing > $_per_page ? $_per_page : $_total - $_count_showing;
            $_load_more_items = $_total - $_per_page;
        @endphp
        <!-- <ul class="uk-pagination uk-flex uk-flex-center uk-margin-remove uk-position-relative"> -->
            @if ($paginator->onFirstPage() || is_null($_prev_page_link))
                <div class="pagination__left hiden">
                    <a href="#"
                       class="left disabled">
                        <svg>
                            <use xlink:href="#left"></use>
                        </svg>
                    </a>
                </div>
            @else
                <div class="pagination__left">
                    <a href="{{ $_prev_page_link }}"
                       class="left"
                       rel="prev">
                        <svg>
                            <use xlink:href="#left"></use>
                        </svg>
                    </a>
                </div>
            @endif
            <div class="pagination__center">
            @foreach ($elements as $element)
                @if (is_string($element))
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $url = $page > 1 ? trim($_current_url, '/') . "/page-{$page}" : $_current_url;
                            $url = $_url_locale . $url . $_current_url_query;
                        @endphp
                        @if ($page == $_current_page)
                                <a href="#" class="active disabled">
                                    {{ $page }}
                                </a>
                            </li>
                        @else
                                <a class="" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
            </div>
            @if($paginator->hasMorePages())
                <div class="pagination__right">
                    <a href="{{ $_next_page_link }}"
                       class="right"
                       rel="next">
                        <svg>
                            <use xlink:href="#right"></use>
                        </svg>
                    </a>
                </div>
            @else
                <div class="pagination__right hiden">
                    <a href="#"
                       class="right disabled">
                        <svg>
                            <use xlink:href="#right"></use>
                        </svg>
                    </a>
                </div>
            @endif
           <!--  @if($_next_page_link)
                <li class="load-more">
                    <a href="{{ $_next_page_link }}"
                       data-load_more="1"
                       class="uk-button uk-flex uk-flex-middle use-ajax">
                        @lang('frontend.pagination_load_more', ['number' => $_load_more_number, 'number_items' => $_load_more_items])
                    </a>
                </li>
            @endif -->
        <!-- </ul> -->
@endif
