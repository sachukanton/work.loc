@php
  global $wrap;
@endphp

@isset($_items)
    <div class="last-nodes" data-src="{{ formalize_path('template/images/bg-top.jpg') }}" uk-img>
        <div class="uk-container">
            <h2 class="title-02 uk-position-relative uk-position-z-index">
                Наш блог
            </h2>
            <div class="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-3 uk-grid-small uk-grid"
                 uk-height-match="target: .title">
                @foreach($_items as $_node)
                    <div>
                        @include('frontend.default.nodes.node_teaser', ['_item' => $_node, '_class' => 'uk-height-1-1'])
                    </div>
                @endforeach
            </div>
            @if(count($_items) >= 3)
                <div class="uk-text-center uk-margin-top">
                    @if($wrap['routes']['blog'])
                        <a href="{{$wrap['routes']['blog']}}"
                           class="uk-link-more uk-position-relative uk-position-z-index">
                            @lang('frontend.link_more')
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endisset