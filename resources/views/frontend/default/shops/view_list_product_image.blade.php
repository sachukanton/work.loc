@php
    global $wrap;
    $_img = 0;
    $_device_type = $wrap['device']['type'] ?? 'pc';
@endphp
<div class="shop-product-change-images">
    {{--{{$_img}}--}}
    @if($_img == 0)
@if($_item->preview_fid)
    <div class="preview-fid">
        {!! $_item->_preview_asset('productTeaser_344_319', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
    </div>
@else
    {!! image_render(NULL, 'productTeaser_344_319', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
@endif
    @else
@if($_item->full_fid)
    <div class="full-fid">
        {!! image_render($_item->_preview_full, 'productTeaser_344_319', ['attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
    </div>
@endif
    @endif
</div>