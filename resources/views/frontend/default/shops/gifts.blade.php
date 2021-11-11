@php
    $_gifts = isset($gifts) ? $gifts : \App\Models\Shop\Gift::getInfo();
@endphp
@if($_gifts)
    <div id="gifts-box"
         class="cart__top">
        <div class="wrapper">
            @foreach($_gifts['steps'] as $_step)
                <div class="wrapper_inner{{ $_step['checked'] ? ' active' : NULL }}">
                    <div class="wrapperes">
                        <div class="cart__top--item"
                             style="background-image: url('{{ $_step['image_url'] }}');">
                            <svg>
                                <use xlink:href="#check"></use>
                            </svg>
                        </div>
                        <h6 class="{{ $_step['checked'] ? 'active' : NULL }}">
                            {{ $_step['title'] }}
                        </h6>
                    </div>
                </div>
                <span class="arrows{{ $_step['checked'] ? ' active' : NULL }}">
                    <svg>
                        <use xlink:href="#arrows_dwn"></use>
                    </svg>
                </span>
            @endforeach
        </div>
        
        
        @if($_gifts['view_text'])
            <div class="go_more">
                <h6>{!! $_gifts['view_text'] !!}</h6>
            </div>
        @endif
        <div class="minmax_time">
            <div class="minmax">
                <span class="minmax_img">
                    <img src="{{ formalize_path('template/images/icons/clock.svg') }}"
                         alt="@variable('max_time_1')">
                </span>
                <div>@variable('max_time_1')</div>
            </div>
            <div class="minmax">
                <span class="minmax_img">
                    <img src="{{ formalize_path('template/images/icons/clock.svg') }}"
                         alt="@variable('max_time_2')">
                </span>
                <div>@variable('max_time_2')</div>
            </div>
        </div>
    </div>
@endif
