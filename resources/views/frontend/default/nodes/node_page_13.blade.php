@php
    $_device_type = $_wrap['device']['type'];
@endphp
@extends('frontend.default.index')

@section('content')
@menuRender('2')
    <div class="container">
        @if ($_device_type == 'pc')
            <h2 class="page_title">{!! $_wrap['page']['title'] !!}</h2>
        @endif
    </div>
    <section class="blog_open">
        <div class="container">
                <h2>{!! $_wrap['page']['title'] !!}</h2>
            @if($_item->sub_title)
                <div class="sub-title">
                    {!! $_item->sub_title !!}
                </div>
            @endif
            @if($_item->published_at)
                <span class="blog_open__date">
                    {{ $_item->published_at->format('d.m.y') }}
                </span>
            @endif
            <div>
                @if($_item->preview_fid)
                    <div class="blog_open__img">
                        {!! $_item->_preview_asset('nodeTeaser_1200_420', ['only_way' => FALSE, 'attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title)]]) !!}
                    </div>
                @endif
            </div>
            @if($_item->body)
                <div class="uk-content-body">
                    {!! $_item->body !!}
                    @include('backend.base.entity_medias')
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
        @include('backend.base.entity_files')
        </div>
 <!--        <div class="uk-container uk-container-small">
            <div class="other-node-link uk-flex uk-flex-center">
                @foreach($_item->_last_nodes(13) as $_key => $_node)
                    @php
                        $_key = $_node->id;
                        if($_item->_show()['previous'] != null) {
                        $_key_previous = $_item->_show()['previous']->id;
                        }
                         if($_item->_show()['next'] != null) {
                        $_key_next = $_item->_show()['next']->id;
                        }
                        $_page_id = $_node->page_id;
                    @endphp
                    @if(isset($_key_next))
                        @if($_key == $_key_next)
                            <a href="{{$_node->generate_url}}">
                                <img src="{{ formalize_path('template/images/arrow-prev.svg') }}"
                                     alt="" uk-svg>
                                Предыдущая статья
                            </a>
                        @endif
                    @endif
                    @if(isset($_key_previous))
                        @if($_key == $_key_previous)
                            <a href="{{$_node->generate_url}}">
                                Следующая статья
                                <img src="{{ formalize_path('template/images/arrow-next.svg') }}"
                                     alt="" uk-svg>
                            </a>
                        @endif
                    @endif
                @endforeach
            </div>
        </div> -->
    </section>

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

{{--@push('scripts')--}}
{{--<script src="/template/js/vue.js"--}}
        {{--type="text/javascript"></script>--}}
{{--@endpush--}}