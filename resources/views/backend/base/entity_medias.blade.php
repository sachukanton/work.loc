@if($_item->relatedMedias && $_item->relatedMedias->isNotEmpty())
    <div class="swiper-container blog_open__gallery">
        <div uk-lightbox="animation: fade" class="swiper-wrapper">
            @foreach($_item->relatedMedias as $_file)
                @php
                    $_caption = $_file->description ? "data-caption=\"{$_file->description}\"" : ($_file->title ? "data-caption=\"{$_file->title}\"" : '');
                @endphp
                <a href="{!! image_render($_file, NULL, ['only_way' => TRUE]) !!}" class="blog_open__gallery_item swiper-slide">
                       {!! image_render($_file, 'thumb_200_150') !!}
                </a>
            @endforeach
        </div>
    </div>
@endif
