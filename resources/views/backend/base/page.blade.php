@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-margin-auto-left uk-margin-auto-right">
        <article class="uk-article uk-position-relative">
            <h1 class="uk-article-title uk-heading-medium uk-heading-divider">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            @include('backend.base.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
            @if($_item->body)
                <div class="uk-content-body uk-margin-medium-bottom">
                    {!! $_item->body !!}
                </div>
            @endif
            @include('backend.base.entity_medias')
            @include('backend.base.entity_files')
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