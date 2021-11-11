@if(isset($_wrap) && is_array($_wrap))
@php
    $_analytics_google = $_wrap['services']['googleTag'] ?? NULL;
    $_analytics_facebook = $_wrap['services']['facebookPixel'] ?? NULL;
    $_reCaptcha = $_wrap['services']['reCaptcha'] ?? NULL;
    $_top_logotype = $_wrap['page']['logotype']['top'];
    $_footer_logotype = $_wrap['page']['logotype']['footer'] ? $_wrap['page']['logotype']['footer'] : $_top_logotype;
    $_top_mobile = $_wrap['page']['logotype']['mobile'] ? $_wrap['page']['logotype']['mobile'] : $_top_logotype;
    $_device_type = $_wrap['device']['type'];
@endphp
        <!DOCTYPE html>
<html lang="{{ $_wrap['locale'] }}"
      class="uk-background-color-white uk-height-min-vh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type"
          content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <base href="{{ $_wrap['seo']['base_url'] }}">
    <title>{{ strip_tags("{$_wrap['seo']['title']} {$_wrap['seo']['title_suffix']}{$_wrap['seo']['page_number_suffix']}") }}</title>
    <meta name="description"
          content="{{ $_wrap['seo']['description'] . $_wrap['seo']['page_number_suffix'] }}">
    <meta name="keywords"
          content="{{ $_wrap['seo']['keywords'] }}">
    <meta name="robots"
          content="noindex, nofollow"/>
    @if(isset($_wrap['seo']['last_modified']) && $_wrap['seo']['last_modified'])
        <meta http-equiv="Last-Modified"
              content="{{ $_wrap['seo']['last_modified'] }}">
    @endif
    @if(isset($_wrap['seo']['url']) && $_wrap['seo']['url'])
        <meta name="url"
              content="{{ $_wrap['seo']['base_url'] . $_wrap['seo']['url'] }}">
    @endif
    @if(isset($_wrap['seo']['canonical']) && $_wrap['seo']['canonical'])
        <link rel="canonical"
              href="{{ $_wrap['seo']['base_url'] . $_wrap['seo']['canonical'] }}"/>
    @endif
    @if(isset($_wrap['seo']['color']) && $_wrap['seo']['color'])
        <meta name="theme-color"
              content="{{ $_wrap['seo']['color'] }}">
    @endif
    @if(isset($_wrap['seo']['copyright']) && $_wrap['seo']['copyright'])
        <meta name="copyright"
              content="{{ $_wrap['seo']['copyright'] }}">
    @endif
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    @if(isset($_wrap['page']['favicon']) && $_wrap['page']['favicon'])
        <link href="/favicon.ico"
              rel="shortcut icon"
              type="image/x-icon"/>
        {{--<link rel="icon"--}}
        {{--type="image/png"--}}
        {{--href="/favicon.png"--}}
        {{--sizes="16x16">--}}
        {{--<link rel="icon"--}}
        {{--sizes="192x192"--}}
        {{--href="/favicon-192-192.png">--}}
        {{--<link rel="icon"--}}
        {{--type="image/png"--}}
        {{--href="/favicon-32-32.png"--}}
        {{--sizes="32x32">--}}
    @endif
    @if(isset($_wrap['seo']['link_prev']) && $_wrap['seo']['link_prev'])
        <link rel="prev"
              href="{{ $_wrap['seo']['base_url'] . $_wrap['seo']['link_prev'] }}"/>
    @endif
    @if(isset($_wrap['seo']['link_next']) && $_wrap['seo']['link_next'])
        <link rel="next"
              href="{{ $_wrap['seo']['base_url'] . $_wrap['seo']['link_next'] }}"/>
    @endif
    <link rel="preconnect"
          href="{{ $_wrap['seo']['base_url'] }}">
    <link rel="dns-prefetch"
          href="{{ $_wrap['seo']['base_url'] }}">
    <link rel="preconnect"
          href="//fonts.googleapis.com">
    <link rel="dns-prefetch"
          href="//fonts.googleapis.com">
    <link rel="preconnect"
          href="//fonts.gstatic.com/"
          crossorigin>
    <link rel="dns-prefetch"
          href="//fonts.gstatic.com/">
    @if(isset($_wrap['page']['styles']['header']) && ($_link_styles_in_head = $_wrap['page']['styles']['header']))
        {!! $_link_styles_in_head !!}
    @endif
    <script type="text/javascript">
        window.Laravel = {!! isset($_wrap['page']['js_settings']) ? (is_array($_wrap['page']['js_settings']) ? json_encode($_wrap['page']['js_settings']) : $_wrap['page']['js_settings']) : json_encode([]) !!};
        var FbData = {
            locale: '{{ $_wrap['locale'] }}',
            device: '{{ $_wrap['device']['type'] }}',
            path: '{{ $_wrap['seo']['url'] }}',
            content_name: '{{ $_wrap['page']['title'] }}',
        };
        window.reCaptchaKey = '{{ $_reCaptcha }}';
    </script>
    @if(isset($_wrap['page']['scripts']['header']) && ($_link_scripts_in_head = $_wrap['page']['scripts']['header']))
        {!! $_link_scripts_in_head !!}
    @endif
    @if($_analytics_google)
        <script async
                src="https://www.googletagmanager.com/gtag/js?id={{ $_analytics_google }}"></script>
        <script type="text/javascript">
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', '{{ $_analytics_google }}');
            gtag('config', 'AW-467931757');
        </script>
    @endif
    @if($_analytics_facebook)
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $_analytics_facebook }}');
            fbq('track', 'PageView', FbData);
        </script>
        <noscript>
            <img height="1"
                 width="1"
                 style="display:none"
                 src="https://www.facebook.com/tr?id={{ $_analytics_facebook }}&ev=PageView&noscript=1"
            />
        </noscript>
    @endif
    @if(isset($_wrap['seo']['open_graph']) && $_wrap['seo']['open_graph'] && is_string($_wrap['seo']['open_graph']))
        {!! $_wrap['seo']['open_graph'] !!}
    @endif
</head>
<body {!! $_wrap['page']['attributes'] ?? NULL !!}>
<div id="app" @if($_wrap['page']['is_front']) @else class="not-front" @endif>
    <header>
        <div class="container">
            <div class="header__wrapper">
                <div class="burger_wrapper">
                    <div class="burger" onclick="this.classList.toggle('active')">
                        <span class="burger_item1"></span>
                        <span class="burger_item2"></span>
                    </div>
                </div>
                <div class="mobile__nav">
                    @menuRender('1')
                    <div class="mobile__nav-bottom">
                        <div class="work_time">
                            <p>{{variable('work_today_title')}}</p>
                            <span>с <b>10:30</b> до <b>22:00</b></span>
                        </div>
                        <a href="tel+38 (063) 629 69 04">+38 (063) 629 69 04</a>
                    </div>
                </div>
                <div class="header__left">
                    <div class="header__left--info">
                        <div class="header__left--icon">
                            <img src="template/images/icons/call.svg" alt="">
                        </div>
                        <div class="header__left--city">
                            {{variable('city')}}
                        </div>
                        @if($_wrap['loads']['contacts']['phones'][0])
                        {!! $_wrap['loads']['contacts']['phones'][0]['format_render_3'] !!}
                        @endif
                        @if($_wrap['loads']['contacts']['working_hours'])
                            @if(variable('work_today_title'))
                                <div class="header__left--day">
                                    {{variable('work_today_title')}}
                                </div>
                            @endif
                            <div class="header__left--time">
                                {!! $_wrap['loads']['contacts']['working_hours'] !!}
                            </div>
                        @endif
                    </div>
                    <div class="btn--white">
                        <button type="button"
                                data-path="/ajax/open-form/2"
                                data-index=""
                                data-view=""
                                data-path-title=""
                                id="call-back"
                                class="use-ajax">
                                <svg>
                                    <use xlink:href="#call"></use>
                                </svg>
                                {{variable('callback')}}
                                
                        </button>
                    </div>
                </div>
                <div class="header__center">
                    @if($_wrap['page']['is_front'])
                        <div class="header__logo">
                            @imageRender($_top_logotype, NULL)
                        </div>
                    @else
                        <a href="{{ LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/') }}"
                           class="header__logo">
                            @imageRender($_top_logotype, NULL)
                        </a>
                    @endif
                </div>
                <div class="header__right">
                    <div class="wrapper">
                        <div class="header__right--social">
                            @foreach($_wrap['loads']['contacts']['socials'] as $_key => $_social)
                                @if($_social)
                                    <a href="{{ $_social }}"
                                       class=" social__item {{ $_key }}"
                                       target="_blank">
                                        <svg>
                                            <use xlink:href="#{{ $_key }}"></use>
                                        </svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        @if($_wrap['use']['multi_language'])
                        <ul class="lang-switcher">
                        @foreach(LaravelLocalization::getSupportedLocales() as $_locale_code => $_properties)
                        <li class="">
                        @if(LaravelLocalization::getCurrentLocale() == $_locale_code)
                        <span class="current">{{ $_properties['name'] }}</span>
                        @else
                        <a rel="alternate"
                        class=""
                        hreflang="{{ $_locale_code }}"
                        href="{{ LaravelLocalization::getLocalizedURL($_locale_code, $_wrap['seo']['url'], []) }}">
                        {{ $_properties['name'] }}
                        </a>
                        @endif
                        </li>
                        @endforeach
                        </ul>
                        @endif
                    </div>
                    @include('frontend.default.load_entities.store_management_block')
                </div>
            </div>
        </div>
    </header>

    <main>
        @include('frontend.default.partials.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
        {{--@dd($_wrap)--}}
        @yield('content')
    </main>


        <footer>
        <div class="container">
            <div class="footer__left--info">
                <div class="footer__left--icon">
                     <img src="template/images/icons/call.svg" alt="call">
                </div>
                <div class="wrapper">
                    <div class="footer__left--city">
                        {!! variable('city') !!}
                    </div>
                    <div class="footer__left--time">
                        {!! variable('work_today_title') !!} @if($_wrap['loads']['contacts']['working_hours'])
                           {!! $_wrap['loads']['contacts']['working_hours'] !!}
                        @endif
                    </div>
                </div>
            </div>
            <div class="footer__left--tel">
                <p>{!! variable('checkout_title') !!}</p>
                @if($_wrap['loads']['contacts']['phones'])
                <ul>
                    @foreach($_wrap['loads']['contacts']['phones'] as $phone)
                       <li>
                            {!! $phone['format_render_3'] !!}
                        </li>
                    @endforeach
                </ul>
                @endif
            </div>
            <div class="footer_btn">
                <button type="button"
                    data-path="/ajax/open-form/2"
                    data-index=""
                    data-view=""
                    data-path-title=""
                    id="call-back"
                    class="use-ajax btn--white">
                    {!! variable('callback') !!}
                </button>
            </div>
            @if($_wrap['page']['site_copyright'])
            <div class="footer__left--copyright">
                {{ $_wrap['page']['site_copyright'] }}
            </div>
            @endif
            <div class="footer__center_logo">
                <div class="footer__center">
                    <a href="{{ LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/') }}" class="footer__logo">
                         @imageRender($_top_logotype, NULL)
                    </a>
                </div>
            </div>
            <div class="footer__right--nav">
                @menuRender('1')        
            </div>
            <div class="footer__right--social">
                <p>{!! variable('social_network') !!}</p>
                @foreach($_wrap['loads']['contacts']['socials'] as $_key => $_social)
                    @if($_social)
                        <a href="{{ $_social }}"
                           class=" social__item {{ $_key }}"
                           target="_blank">
                            <svg>
                                <use xlink:href="#{{ $_key }}"></use>
                            </svg>
                        </a>
                    @endif
                @endforeach
            </div>
            <div class="footer__right--privacy">
                <ul>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Договор публичной оферты</a></li>
                </ul>
            </div>
            <a href="https://oleus.com.ua/"  target="_blank" class="footer__right--oleus">
                <img src="template/images/oleus.svg">
            </a>
        </div>
    </footer>
</div>

@if($_device_type != 'pc')
    <div class="uk-fix-menu uk-position-bottom-left uk-position-fixed">
        @menuRender('2')
    </div>
    <div id="offcanvas-menu"
         uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar">
            <div class="uk-flex uk-flex-between uk-flex-top">
                <button class="uk-offcanvas-close uk-position-relative"
                        type="button">
                    <img uk-img="data-src:{{ formalize_path('template/images/icon-close.svg') }}"
                         alt="" width="15" height="15">
                </button>
                <div>
                    <img uk-img="data-src:{{ formalize_path('template/images/sushi-man.png') }}"
                         alt="" width="63" height="58">
                </div>
            </div>

            @menuRender('1')

            <div class="uk-grid-collapse uk-grid uk-margin-small-bottom">
                @if($_wrap['loads']['contacts']['working_hours'])
                    <div class="working-hours uk-width-2-3">
                        @if(variable('work_today_title'))
                            <div class="title-04">
                                {{variable('work_today_title')}}:
                            </div>
                        @endif
                        <div>
                            {!! $_wrap['loads']['contacts']['working_hours'] !!}
                        </div>
                    </div>
                @endif

                @if($_wrap['loads']['contacts']['socials'])
                    <div class="cosial-link uk-width-1-3">
                        @foreach($_wrap['loads']['contacts']['socials'] as $_key => $_social)
                            @if($_social)
                                <a href="{{ $_social }}"
                                   class=" icon-{{ $_key }}"
                                   target="_blank">
                                    <img class="uk-margin-remove" uk-img="data-src:{{ formalize_path('template/images/icon-' . $_key . '.svg') }}"
                                         alt="" width="22" height="22">
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="uk-grid-collapse uk-grid">
                <div class="uk-width-2-3">
                    @if($_wrap['loads']['contacts']['phones'] || $_wrap['loads']['contacts']['address'])
                        @if(variable('checkout_title'))
                            <div class="title-04">
                                {{variable('checkout_title')}}:
                            </div>
                        @endif
                        @if($_wrap['loads']['contacts']['phones'])
                            <div class="phone">
                                @foreach($_wrap['loads']['contacts']['phones'] as $_phone)
                                    {!! $_phone['format_render_3'] !!}
                                @endforeach
                            </div>
                        @endif
                        @if($_wrap['loads']['contacts']['address'])
                            <div class="uk-address">
                                {!! $_wrap['loads']['contacts']['address'] !!}
                            </div>
                        @endif
                    @endif
                </div>
                <div class="uk-width-1-3">
                    <img uk-img="data-src:{{ formalize_path('template/images/cards.png') }}"
                         alt="" width="172" height="53">
                </div>

            </div>
        </div>
        @endif

        @if(isset($_wrap['page']['styles']['footer']) && ($_link_styles_in_footer = $_wrap['page']['styles']['footer']))
            {!! $_link_styles_in_footer !!}
        @endif
        @if(isset($_wrap['page']['scripts']['footer']) && ($_link_scripts_in_footer = $_wrap['page']['scripts']['footer']))
            {!! $_link_scripts_in_footer !!}
        @endif
        @stack('styles')
        @stack('scripts')
        {{--@if($_authUser)--}}
        {{--<div id="control-edit-box" class="uk-position-relative uk-position-z-index">--}}
        {{--@stack('edit_page')--}}
        {{--</div>--}}
        {{--@endif--}}
        @if (session('commands'))
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    setTimeout(function () {
                        var $commands = <?= session('commands') ?>;
                        for (var $i = 0; $i < $commands.length; ++$i) {
                            var command = $commands[$i];
                            if (window['cmd_' + command.command] != undefined) window['cmd_' + command.command](command.options);
                        }
                    }, 500);
                });
            </script>
        @endif
        @stack('schema')
        @if($_wrap['loads']['contacts']['schema'])
            <script type='application/ld+json'>
                {!! $_wrap['loads']['contacts']['schema'] !!}
            </script>
@endif
{{--@if(!$_wrap['page']['is_front'] && $_reCaptcha)--}}
{{--<script src="https://www.google.com/recaptcha/api.js?render={{ $_reCaptcha }}"></script>--}}
{{--<script>--}}
{{--window.reCaptchaValid = null;--}}
{{--grecaptcha.ready(function () {--}}
{{--grecaptcha.execute('{{ $_wrap["services"]["reCaptcha"] }}', {action: 'validate_reCaptcha'})--}}
{{--.then(function (token) {--}}
{{--var data = new FormData();--}}
{{--data.append("action", 'validate_reCaptcha');--}}
{{--data.append("token", token);--}}
{{--fetch('/ajax/validate-reCaptcha', {--}}
{{--method: 'POST',--}}
{{--body: data,--}}
{{--headers: {--}}
{{--'X-CSRF-TOKEN': window.Laravel.csrfToken--}}
{{--}--}}
{{--}).then(function (response, d) {--}}
{{--response.json().then(function (data) {--}}
{{--reCaptchaValid = data.token;--}}
{{--});--}}
{{--});--}}
{{--});--}}
{{--});--}}
{{--</script>--}}
{{--@endif--}}
@include('frontend.default.partials.modal_banner')
</body>
</html>
@else
    @php
        abort(500)
    @endphp
@endif
