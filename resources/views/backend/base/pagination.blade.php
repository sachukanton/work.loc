@if($paginator->hasPages())
    <div class="uk-pagination-box uk-margin-medium-bottom uk-margin-medium-top">
        @php
            $_wrap = wrap()->get();
            $_current_page = $paginator->currentPage();
            $_total = $paginator->total();
            $_current_url = $_wrap['seo']['url_alias'];
            $_current_url_query = $_wrap['seo']['url_query'];
            $_next_page_link = $_wrap['seo']['link_next'] ?? NULL;
            $_prev_page_link = $_wrap['seo']['link_prev'] ?? NULL;
            $_per_page = $paginator->perPage();
            $_count_showing = $_per_page * $_current_page;
            $_load_more_number = $_total - $_count_showing > $_per_page ? $_per_page : $_total - $_count_showing;
        debug($_wrap['seo']);
        @endphp
        <ul class="uk-pagination uk-flex uk-flex-right uk-margin-remove">
            @if($_next_page_link)
                <li class="load-more">
                    <a href="{{ $_next_page_link }}"
                       data-load_more="1"
                       class="uk-button uk-button-small uk-button-default uk-box-shadow-small use-ajax">
                        @lang('frontend.pagination_load_more', ['number' => $_load_more_number])
                    </a>
                </li>
            @endif
            @if ($paginator->onFirstPage() || is_null($_prev_page_link))
                <li class="uk-disabled">
                    <a href="#"
                       class="uk-button uk-button-small uk-button-default uk-box-shadow-small">
                        <span uk-pagination-previous></span>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ $_prev_page_link }}"
                       class="uk-button uk-button-small uk-button-default uk-box-shadow-small"
                       rel="prev">
                        <span uk-pagination-previous></span>
                    </a>
                </li>
            @endif
            @foreach ($elements as $element)
                @if (is_string($element))
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $url = $page > 1 ? trim($_current_url, '/') . "/page-{$page}" : $_current_url;
                            $url = _u($url) . $_current_url_query;
                        @endphp
                        @if ($page == $_current_page)
                            <li class="uk-active uk-text-color-white">
                                <a href="#"
                                   disabled
                                   class="uk-button uk-button-small uk-button-color-blue-grey uk-box-shadow-small uk-disabled">
                                    {{ $page }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="uk-button uk-button-small uk-button-default uk-box-shadow-small"
                                   href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if($paginator->hasMorePages())
                <li>
                    <a href="{{ $_next_page_link }}"
                       class="uk-button uk-button-small uk-button-default uk-box-shadow-small"
                       rel="next">
                        <span uk-pagination-next></span>
                    </a>
                </li>
            @else
                <li class="uk-disabled">
                    <a href="#"
                       class="uk-button uk-button-small uk-button-default uk-box-shadow-small">
                        <span uk-pagination-next></span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif