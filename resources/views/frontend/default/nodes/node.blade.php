@extends('frontend.default.index')

@section('content')

    <article class="uk-article uk-margin-top uk-margin-bottom uk-position-relative ">
        <div class="uk-container uk-container-small">
            <h1 class="title-01 uk-position-relative uk-position-z-index uk-margin-small-bottom uk-margin-top">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="sub-title">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            @if($_item->published_at)
                <div class="uk-article-meta uk-margin-small-bottom">
                    {{ $_item->published_at->format('d.m.y') }}
                </div>
            @endif
        </div>
        <div class="uk-container">
            <div>
                @if($_item->preview_fid)
                    <div class="blog-preview-fid uk-position-relative uk-overflow-hidden">
                        {!! $_item->_preview_asset('nodeTeaser_1200_420', ['only_way' => FALSE, 'attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-cover' => TRUE]]) !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="uk-container uk-container-small">
            @if($_item->body)
                <div class="uk-content-body uk-margin-medium-bottom">
                    {!! $_item->body !!}
                </div>
            @endif
            @if($_item->_tags->isNotEmpty())
                <div class="uk-margin-medium-bottom">
                    <hr class="uk-margin-small-top">
                    @foreach($_item->_tags as $_tag)
                        @l($_tag->title, $_tag->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'uk-text-color-pink uk-text-small uk-margin-small-right']])
                    @endforeach
                </div>
            @endif
        </div>
            @include('backend.base.entity_medias')
            @include('backend.base.entity_files')
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
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.nodes.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    @else
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.nodes.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    @endif
                </li>
            </ul>
        </div>
    </div>
@endif
@endpush