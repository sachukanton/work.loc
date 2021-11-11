@if($_sub_categories)
    {{--<div id="uk-items-list-sub-categories">--}}
    {{--@php--}}
    {{--$_sub_categories = array_chunk($_sub_categories, 2);--}}
    {{--@endphp--}}
    {{--@foreach($_sub_categories as $_sub_category_row)--}}
    {{--<div class="row">--}}
    {{--@foreach($_sub_category_row as $_sub_category)--}}
    {{--<div class="col-sm-12 col-md-6">--}}
    {{--<div class="category_colitem">--}}
    {{--<div class="header"--}}
    {{--style="background-color: #f38723;">--}}
    {{--<div class="image">--}}
    {{--<a href="{{ $_sub_category['alias'] }}">--}}
    {{--<img src="{{ $_sub_category['preview'] }}"--}}
    {{--class="img-responsive">--}}
    {{--</a>--}}
    {{--</div>--}}
    {{--<div class="text">--}}
    {{--<a--}}
    {{--href="{{ $_sub_category['alias'] }}">--}}
    {{--{{ $_sub_category['title'] }}--}}
    {{--</a>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="inner row">--}}
    {{--@if($_sub_category['children'])--}}
    {{--@foreach($_sub_category['children'] as $_sub_2_category)--}}
    {{--<div class="col-sm-6 item">--}}
    {{--<a href="{{ $_sub_2_category['alias'] }}">--}}
    {{--{{ $_sub_2_category['title'] }}--}}
    {{--</a>--}}
    {{--</div>--}}
    {{--@endforeach--}}
    {{--@endif--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--@endforeach--}}
    {{--</div>--}}
    {{--@endforeach--}}
    {{--</div>--}}
@endif