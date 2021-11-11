@extends('frontend.default.index')

@section('content')
    <div class="uk-container uk-margin-auto-left uk-margin-auto-right">
        <article class="uk-article">
            <h1 class="uk-article-title uk-heading-medium uk-heading-divider">
                {!! $_wrap['page']['title'] !!}
            </h1>
            @if($_item->sub_title)
                <div class="uk-h4 uk-margin-remove-top uk-text-muted uk-margin-medium-bottom">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            @include('backend.base.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])
            <div class="uk-article-content">
                @if($_item->_items->isNotEmpty())
                    <div id="uk-items-list"
                         class="uk-margin-medium-bottom">
                        <ul uk-accordion="active: 0;"
                            class="uk-list-divider">
                            @foreach($_item->_items as $_faq)
                                <li>
                                    <a class="uk-accordion-title uk-text-uppercase uk-text-color-blue-grey uk-margin-remove-last-child"
                                       href="#">
                                        {!! $_faq->question !!}
                                    </a>
                                    <div class="uk-accordion-content uk-position-relative">
                                        @if(isset($_accessEdit['faq']) && $_accessEdit['faq'])
                                            <div class="uk-position-absolute uk-position-top-right">
                                                @if($_locale == DEFAULT_LOCALE)
                                                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.faqs.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                                                @else
                                                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.faqs.translate', ['p' => ['id' => $_item->id, 'locale' => $_wrap['locale']], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                                                @endif
                                            </div>
                                        @endif
                                        {!! $_faq->answer !!}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        @lang('frontend.no_items')
                    </div>
                @endif
                @if($_item->body)
                    <div class="uk-content-body uk-margin-medium-bottom">
                        {!! $_item->body !!}
                    </div>
                @endif
            </div>
        </article>
    </div>
@endsection

@push('edit_page')
    @if($_authUser && $_authUser->can('pages_update'))
        <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <button class="uk-button uk-button-color-amber"
                    type="button">
                <span uk-icon="icon: settings"></span>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click"
                 class="uk-box-shadow-small uk-padding-small">
                <ul class="uk-nav uk-dropdown-nav">
                    <li>
                        @l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus/faqs', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                    </li>
                </ul>
            </div>
        </div>
    @endif
@endpush