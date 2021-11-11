<div id="list-comments-items">
    <ul class="uk-comment-list uk-margin-medium-bottom">
        @foreach($_item->comments as $_comment)
            <li>
                @include('backend.base.comments_item', compact('_comment'))
            </li>
        @endforeach
    </ul>
    {!! $_item->comments->links('backend.base.comments_pagination', compact('_item')) !!}
</div>