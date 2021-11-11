<div id="uk-items-list-top-bar"
     class="uk-clearfix uk-filter-sort">
    {{--<div class="uk-float-left">--}}
    {{--<div class="uk-button-group uk-box-shadow-small uk-border-rounded">--}}
    {{--<button type="button"--}}
    {{--uk-icon="icon: view_module"--}}
    {{--data-path="{{ _r('ajax.shop_change_view', ['view' => 'module']) }}"--}}
    {{--rel="nofollow"--}}
    {{--class="uk-button uk-button-icon uk-button-default uk-button-small use-ajax{{ $_view == 'module' ? ' uk-active' : NULL }}">--}}
    {{--</button>--}}
    {{--<button type="button"--}}
    {{--data-path="{{ _r('ajax.shop_change_view', ['view' => 'list']) }}"--}}
    {{--rel="nofollow"--}}
    {{--uk-icon="icon: view_list"--}}
    {{--class="uk-button uk-button-icon uk-button-default uk-button-small use-ajax{{ $_view == 'list' ? ' uk-active' : NULL }}">--}}
    {{--</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    <div class="uk-position-relative sort-catalog box-dropdown">
        <button class="uk-button uk-button-default uk-flex uk-flex-between uk-flex-middle btn-dropdown"
                type="button">
            {{ $_sort['use']['title'] }}
            <img uk-img="data-src:{{ formalize_path('template/images/icon-arrow-down.svg') }}"
                 alt="">
        </button>
        <div uk-dropdown="mode: click; pos: bottom-left; boundary: .lang-box; animation: false; duration: 0;flip:false">
            <ul class="uk-nav uk-nav-default uk-position-relative">
                @foreach($_sort['list'] as $_link)
                    <li>
                        <a href="{{ $_link['alias'] }}"
                           class="use-ajax">
                            {{ $_link['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>