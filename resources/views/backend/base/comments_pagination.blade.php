@if($paginator->hasPages())
    <div class="uk-pagination-box uk-margin-small-bottom uk-margin-medium-top">
        @php
            $_current_page = $paginator->currentPage();
            $_current_url_query = NULL;
        @endphp
        <ul class="uk-pagination uk-flex uk-flex-right uk-margin-remove">
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @php
                            $url = _r('ajax.view_comments_list', ['view' => ($page > 1 ? "/page-{$page}" : NULL)]);
                            $url = _u($url) . $_current_url_query;
                        @endphp
                        @if ($page == $_current_page)
                            <li class="uk-active uk-text-color-white">
                                <a href="#"
                                   rel="nofollow"
                                   class="uk-button uk-button-small uk-button-color-blue-grey uk-box-shadow-small">
                                    {{ $page }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="uk-button uk-button-small uk-button-default uk-box-shadow-small use-ajax"
                                   data-entity="{{ $_item->_alias->id }}"
                                   data-rate="{{ $_rate ?? 'all' }}"
                                   href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>
@endif