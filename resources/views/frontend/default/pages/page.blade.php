@php
    $_device_type = $_wrap['device']['type'];
@endphp

@extends('frontend.default.index')

@section('content')
    <div class="container">
        <article class="other">
            <h1>    
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <h4>
                    {!! $_item->sub_title !!}
                </h4>
            @endif
            @if($_item->body)
                <div class="seo__text">
                    {!! $_item->body !!}
                </div>
            @endif
        </article>
    </div>
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

@push('schema')
<script type="application/ld+json">
    {!! $_item->schema !!}
</script>
@endpush

@push('scripts')
    <script type="text/javascript">
        if (typeof fbq == 'function') {
            var a = {};
            if (typeof FbData == 'object') attr = Object.assign(a, FbData);
            fbq('track', 'ViewContent', a);
        }
    </script>
@endpush
