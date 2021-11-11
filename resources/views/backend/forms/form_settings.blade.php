@extends('backend.index')

@section('content')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin">
                {!! $_wrap['seo']['title'] !!}
            </h1>
        </div>
        <form class="uk-form uk-form-stacked uk-width-1-1 uk-margin-medium-bottom"
              method="POST"
              enctype="multipart/form-data"
              action="{{ _r('oleus.settings', ['page' => $_form->route_tag]) }}">
            {{ csrf_field() }}
            {{ method_field('POST') }}
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
                <div class="uk-card-header uk-text-right"
                     uk-sticky="animation: uk-animation-slide-top; top: 80">
                    @if($_form->buttons)
                        @foreach($_form->buttons as $_button)
                            {!! $_button !!}
                        @endforeach
                    @endif
                    <button type="submit"
                            name="save"
                            value="1"
                            class="uk-button uk-button-success uk-text-uppercase">
                        Сохранить настройки
                    </button>
                </div>
                <div class="uk-card-body">
                    @if($errors->any())
                        <div class="uk-alert uk-alert-danger">
                            <ul class="uk-list">
                                @foreach ($errors->all() as $_error)
                                    <li class="uk-margin-remove">{{ $_error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($_form->tabs)
                        <div class="uk-grid-match"
                             uk-grid>
                            <div class="uk-width-1-4">
                                <ul class="uk-tab uk-tab-left"
                                    uk-tab="connect: #uk-tab-body; animation: uk-animation-fade; swiping: false;">
                                    @foreach($_form->tabs as $tab)
                                        @if($tab)
                                            <li class="{{ $loop->index == 0 ? 'uk-active' : '' }}">
                                                <a href="#">{{ $tab['title'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                    @if(isset($_form->seo) && $_form->seo)
                                        <li>
                                            <a href="#">@lang('others.tab_meta_tags')</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="uk-width-3-4">
                                <ul id="uk-tab-body"
                                    class="uk-switcher uk-margin">
                                    @foreach($_form->tabs as $tab)
                                        @if($tab)
                                            <li class="{{ $loop->index == 0 ? 'uk-active' : '' }}">
                                                @foreach($tab['content'] as $content)
                                                    {!! $content !!}
                                                @endforeach
                                            </li>
                                        @endif
                                    @endforeach
                                    @if(isset($_form->seo) && $_form->seo)
                                        <li>
                                            @include('backend.fields.fields_group_meta_tags', compact('_item'))
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @elseif($_form->contents)
                        @foreach($_form->contents as $content)
                            @if($content)
                                {!! $content !!}
                            @endif
                        @endforeach
                        @if($_form->seo)
                            @include('backend.fields.fields_group_meta_tags', compact('_item'))
                        @endif
                    @endif
                </div>
            </div>
        </form>
    </article>
@endsection
