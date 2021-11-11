<script>
    window.catalogFilterParam = {!! json_encode($_filter) !!};
    window.catalogCatalogUrl = "{!! $_category->generate_url !!}";
</script>

@php
    $_device_type = wrap()->get('device.type');
@endphp

@if($_filter)
    <catalog-filter-component :refresh="refreshFilter"></catalog-filter-component>
@endif
