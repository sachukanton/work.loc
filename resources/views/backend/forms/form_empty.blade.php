@extends('backend.index')

@section('content')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin">
                {!! $_wrap['seo']['title'] !!}
            </h1>
        </div>
        <form class="uk-form uk-form-stacked uk-width-1-1{{ $_form->class ? " {$_form->class}" : NULL }}"
              enctype="multipart/form-data"
              method="{{ $_form->method }}"
              action="{{ $_form->route_tag }}">
            {{ csrf_field() }}
            {{ method_field($_form->method) }}
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
                <div class="uk-card-header uk-text-right"
                     uk-sticky="animation: uk-animation-slide-top; top: 80">
                    @can($_form->permission['apply'])
                        <button type="submit"
                                name="apply"
                                value="1"
                                class="uk-button uk-button-success uk-margin-small-right">
                            Применить
                        </button>
                    @endcan
                    @if($_form->buttons)
                        @foreach($_form->buttons as $_button)
                            {!! $_button !!}
                        @endforeach
                    @endif
                    @if($_form->rollback)
                        @l('', $_form->rollback, ['attributes' => ['class' => 'uk-button uk-button-secondary uk-button-icon', 'uk-icon' => 'icon: reply']])
                    @endif
                </div>
                <div class="uk-card-body">
                    @if($errors->any())
                        <div class="uk-alert uk-alert-danger">
                            <ul class="uk-list">
                                @foreach ($errors->all() as $_error)
                                    <li class="uk-margin-remove">{!! $_error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if($_alert)
                        <div class="uk-alert uk-alert-primary">
                            {!! $_alert !!}
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
                                </ul>
                            </div>
                        </div>
                    @elseif($_form->contents)
                        @foreach($_form->contents as $content)
                            @if($content)
                                {!! $content !!}
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </form>
    </article>
@endsection
