{{--<div class="category-side-bar">--}}
{{--@foreach($_others->where('parent_id', 0) as $category)--}}
{{--<div class="category-title uk-text-uppercase">{{ $category->title }}</div>--}}
{{--<div class="category-child">--}}
{{--@foreach($_others->where('parent_id', $category->id) as $child)--}}
{{--<div class="category-sub-title">--}}
{{--<a class="@if($_wrap['seo']['url'] == $child->generate_url) active uk-disabled @endif" href="{{$child->generate_url}}">--}}
{{--{{ $child->title }}--}}
{{--</a>--}}

{{--</div>--}}
{{--@endforeach--}}
{{--</div>--}}
{{--@endforeach--}}
{{--</div>--}}





@foreach($_others->where('parent_id', 0) as $category)
    <div>
        {{$category->title}}
    </div>
    @if($category->_children->isNotEmpty())
        @foreach($_others->where('parent_id', $category->id) as $category_children)
            <div>
                {{$category_children->title}}
            </div>
            @foreach($category_children->_children as $category_child)
                {{$category_child->title}}
            @endforeach
        @endforeach
    @endif
@endforeach