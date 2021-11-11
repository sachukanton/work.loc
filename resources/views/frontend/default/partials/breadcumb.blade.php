@php
    $_breadcrumbs = $_items ?? $_wrap['page']['breadcrumb'];
@endphp
@if($_breadcrumbs)
    <section class="breadcrambs" id="breadcrumbs">
        <div class="container">
            @if($_device_type == 'pc')
                @foreach($_breadcrumbs as $_item)
                        @if(!$loop->last)
                            <a href="{{ $_item['url'] }}">
                                {!! $_item['name'] !!}
                            </a>
                        @else
                        <span>
                            {!! $_item['name'] !!}
                        </span>
                        @endif
                @endforeach
            @else
                <a class="to_main" href="{{ LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/') }}">{!! variable('go_main') !!}</a>
            @endif
        </div>
    </section>
    @php
        $_i = 0;
        $_breadcrumbs_items = [];
        foreach ($_breadcrumbs as $_item){
            $_i++;
            $_breadcrumbs_items[] = [
            "@type"=> "ListItem",
                "position"=> $_i,
                "name"=>  $_item['name'],
                "item"=> config('app.url') . $_item['url']
            ];
        }
        $_breadcrumbs = json_encode([
            "@context" => "https://schema.org/",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                $_breadcrumbs_items
            ]
        ]);
    @endphp
    <script type="application/ld+json">
        {!! $_breadcrumbs !!}
    </script>
@endif