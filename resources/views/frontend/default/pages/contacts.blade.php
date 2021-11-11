@extends('frontend.default.index')

@section('content')

    <article class="uk-article uk-position-relative contacts-page">
        {{--@include('frontend.default.partials.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])--}}
        <div class="uk-container">
            <h1 class="title-01 uk-position-relative uk-position-z-index">
                {!! $_wrap['page']['title'] !!}
            </h1>
            {{--@if($_item->sub_title)--}}
            {{--<div class="uk-h4 uk-margin-remove-top uk-text-muted">--}}
            {{--{!! $_item->sub_title !!}--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--@if($_item->body)--}}
            {{--<div class="contact-page-content uk-margin-medium-bottom">--}}
            {{--{!! $_item->body !!}--}}
            {{--</div>--}}
            {{--@endif--}}
            <div class="uk-margin-medium-bottom uk-margin-medium-top">
                    @if($_wrap['loads']['contacts']['address'])
                        <div class="item-contact uk-margin-small-bottom">
                             {{ $_wrap['loads']['contacts']['address'] }}
                        </div>
                    @endif
                    @if($_wrap['loads']['contacts']['working_hours'])
                            <div class="item-contact uk-margin-small-bottom">
                                    {!! $_wrap['loads']['contacts']['working_hours'] !!}
                                </div>
                    @endif
                    @if($_wrap['loads']['contacts']['phones'])
                            <div class="item-contact uk-margin-small-bottom">
                                @foreach($_wrap['loads']['contacts']['phones'] as $_phone)
                                        {!! $_phone['format_render_3'] !!}
                                @endforeach
                        </div>
                    @endif
                    @if($_wrap['loads']['contacts']['email'])
                            <div class="item-contact">
                                    <a href="mailto:{{ $_wrap['loads']['contacts']['email'] }}">
                                        {{ $_wrap['loads']['contacts']['email'] }}
                                    </a>
                                </div>
                    @endif
                </div>
            {{--@formRender('1')--}}
        </div>
        @if($_wrap['services']['googleMap'] && isset($_wrap['loads']['contacts']) && $_wrap['loads']['contacts']['locations']['lat'] && $_wrap['loads']['contacts']['locations']['lng'])
            <div class="maps uk-position-relative">
                <div id="maps-google"
                     class="uk-position-relative maps-google"></div>
            </div>
        @endif
    </article>
@endsection

@push('edit_page')
@if(isset($_accessEdit['page']) && $_accessEdit['page'])
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

@push('scripts')
@if($_wrap['services']['googleMap'] && isset($_wrap['loads']['contacts']) && $_wrap['loads']['contacts']['locations']['lat'] && $_wrap['loads']['contacts']['locations']['lng'])
    <script>
        var domEntityMaps = document.getElementById('maps-google'), map,
            marker = {!! json_encode($_wrap['loads']['contacts']['locations']) !!},
            mark_icon = '{{ config('app.url') . formalize_path('template/images/map-marks.png') }}';
        class googleMap {
            constructor() {
                this.options = {
                    zoom: 15,
                    mapTypeControl: false,
                    center: {lat: parseFloat(marker.lat), lng: parseFloat(marker.lng)},
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    disableDefaultUI: true
                }, this.icon = null, this.init()
            }

            init() {
                domEntityMaps.style.height = "650px", map = new google.maps.Map(domEntityMaps, this.options), this.mapMarkerIcon(), new google.maps.Marker({
                    icon: this.icon,
                    position: {lat: parseFloat(marker.lat), lng: parseFloat(marker.lng)},
                    map: map
                })
            }

            mapMarkerIcon() {
                this.icon = {
                    url: mark_icon,
                    scaledSize: new google.maps.Size(200, 156),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(20, 40)
                }
            }
        }
        function initGoogleMap() {
            new googleMap()
        }
    </script>
    <script defer
            async
            src="//maps.google.com/maps/api/js?key={{ $_wrap['services']['googleMap'] }}&callback=initGoogleMap"></script>
@endif
@endpush

@push('schema')
<script type="application/ld+json">
    {!! $_item->schema !!}
</script>
@endpush