@if($_params)
    <script>
        window.catalogFilterParam = {!! json_encode($_params) !!};
        window.catalogSelected = {!! json_encode($_selected) !!};
        window.catalogCatalogUrl = "{!! $_category->generate_url !!}";
    </script>
    <div class="uk-margin-bottom uk-text-uppercase uk-text-bold">
        @lang('shop.labels.title_filter')
    </div>
    <div class="filter-selected">
        <catalog-filter-selected-component
            :refresh="refreshFilter"></catalog-filter-selected-component>
    </div>
    <div class="uk-margin-bottom">
        @foreach($_params as $_param_id => $_param)
            <catalog-filter-item-component
                :param_id="{{ $_param_id }}"
                :refresh="refreshFilter"></catalog-filter-item-component>
        @endforeach
    </div>
@endif