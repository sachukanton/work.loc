<ul class="uk-comment-list">
    @foreach($items as $_item)
        <li>
            @include('backend.partials.comment.item', compact('_item'))
        </li>
    @endforeach
</ul>