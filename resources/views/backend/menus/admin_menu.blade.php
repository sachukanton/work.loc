@php
    $_menu = config('os_dashboard.menu');
@endphp
@if($_menu)
    <ul class="uk-navbar-nav"
        uk-nav>
        @foreach($_menu as $_item)
            @if(isset($_item['children']) && count($_item['children']))
                @php
                    $_access_item = FALSE;
                    if(isset($_item['permission']) && $_item['permission']){
                        foreach ($_item['permission'] as $_permission_item) {
                            if($_wrap['user']->can($_permission_item)) {
                                $_access_item = TRUE;
                                break;
                            }
                        }
                    }else{
                        $_access_item = TRUE;
                    }
                @endphp
                @if($_access_item)
                    @php
                        $_children = collect($_item['children']);
                        $_children_routes = $_children->pluck('route');
                    @endphp
                    <li class="uk-parent{{ _ar($_children_routes->all()) }}">
                        <a href="javascript:void(0);"
                           rel="nofollow">
                            {!! $_item['link'] !!}
                            <span uk-icon="keyboard_arrow_down"></span>
                        </a>
                        <div class="uk-navbar-dropdown"
                             uk-dropdown="mode: click">
                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                @foreach($_children as $_item_children)
                                    @if((!isset($_item_children['route']) || is_null($_item_children['route'])) && isset($_item_children['children']) && count($_item_children['children']))
                                        @php
                                            $_access_item_children = FALSE;
                                            if(isset($_item_children['permission']) && $_item_children['permission']){
                                                foreach ($_item_children['permission'] as $_permission_item) {
                                                    if($_wrap['user']->can($_permission_item)) {
                                                        $_access_item_children = TRUE;
                                                        break;
                                                    }
                                                }
                                            }else{
                                                $_access_item_children = TRUE;
                                            }
                                        @endphp
                                        @if($_access_item)
                                            @php
                                                $_children_2 = collect($_item_children['children']);
                                                $_children_routes_2 = $_children_2->pluck('route');
                                            @endphp
                                            <li class="uk-parent{{ _ar($_children_routes_2->all()) }}">
                                                <a href="javascript:void(0);"
                                                   rel="nofollow">
                                                    {!! $_item_children['link'] !!}
                                                </a>
                                                <ul class="uk-nav-sub">
                                                    @foreach($_children_2 as $_item_children_2)
                                                        @if($_wrap['user']->can($_item_children_2['permission']))
                                                            <li class="{{ _ar($_item_children_2['route'], ($_item_children_2['params'] ?? [])) }}">
                                                                <a href="{{ _r($_item_children_2['route'], ($_item_children_2['params'] ?? [])) }}">
                                                                    {!! $_item_children_2['link'] !!}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif
                                    @elseif($_item_children['route'])
                                        @if((isset($_item_children['permission']) && $_item_children['permission'] && $_wrap['user']->can($_item_children['permission'])) || (!isset($_item_children['permission']) || is_null($_item_children['permission'])))
                                            <li class="{{ _ar($_item_children['route'], ($_item_children['params'] ?? [])) }}">
                                                <a href="{{ _r($_item_children['route'], ($_item_children['params'] ?? [])) }}">
                                                    {!! $_item_children['link'] !!}
                                                </a>
                                            </li>
                                        @endif
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif
            @elseif($_item['route'])
                @if($_wrap['user']->can($_item['permission']))
                    <li class="{{ _ar($_item['route']) }}">
                        <a href="{{ _r($_item['route'], ($_item['params'] ?? [])) }}"
                           class="{{ _ar($_item['route']) }}">
                            {!! $_item['link'] !!}
                        </a>
                    </li>
                @endif
            @endif
        @endforeach
    </ul>
@endif