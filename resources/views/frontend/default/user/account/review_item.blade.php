<div class="review_item"
     id="comment-{{ $_comment->id }}">
    <div class="header">
        <div class="product">
            @l($_comment->model->title, $_comment->model->generate_url, ['attributes' => ['target' => '_blank']])
        </div>
        <ul class="rating_stars locked list-inline hidden-xs">
            <li data-rating="1"
                @if($_comment->rate >= 1) class="active"@endif>
                <i class="star"></i>
            </li>
            <li data-rating="2"
                @if($_comment->rate >= 2) class="active"@endif>
                <i class="star"></i>
            </li>
            <li data-rating="3"
                @if($_comment->rate >= 3) class="active"@endif>
                <i class="star"></i>
            </li>
            <li data-rating="4"
                @if($_comment->rate >= 4) class="active"@endif>
                <i class="star"></i>
            </li>
            <li data-rating="5"
                @if($_comment->rate == 5) class="active"@endif>
                <i class="star"></i>
            </li>
        </ul>
        <div class="date hidden-sm hidden-xs">
            {{ $_comment->created_at->format('d-m-Y H:i') }}
        </div>
        <div class="link hidden-xs">
            @l(trans('forms.buttons.comment.look_at_page'), "reviews/{$_comment->model->_alias->id}", ['anchor' => "comment-{$_comment->id}", 'attributes' => ['target' => '_blank']])
        </div>
    </div>
    <div class="visible-xs">
        <div class="header">
            <div>
                <ul class="rating_stars locked list-inline">
                    <li data-rating="1"
                        @if($_comment->rate >= 1) class="active"@endif>
                        <i class="star"></i>
                    </li>
                    <li data-rating="2"
                        @if($_comment->rate >= 2) class="active"@endif>
                        <i class="star"></i>
                    </li>
                    <li data-rating="3"
                        @if($_comment->rate >= 3) class="active"@endif>
                        <i class="star"></i>
                    </li>
                    <li data-rating="4"
                        @if($_comment->rate >= 4) class="active"@endif>
                        <i class="star"></i>
                    </li>
                    <li data-rating="5"
                        @if($_comment->rate == 5) class="active"@endif>
                        <i class="star"></i>
                    </li>
                </ul>
                <div class="date">
                    {{ $_comment->created_at->format('d-m-Y H:i') }}
                </div>
            </div>
            <div class="link">
                @l(trans('forms.buttons.comment.look_at_page_2'), "reviews/{$_comment->model->_alias->id}", ['anchor' => "comment-{$_comment->id}", 'attributes' => ['target' => '_blank']])
            </div>
        </div>
    </div>
    <div class="text">
        <p>
            {{ strip_tags($_comment->comment) }}
        </p>
    </div>
    @if($_comment->advantages)
        <div class="plus_text">
            <b>
                @lang('forms.labels.comment.advantages'):
            </b>
            {{ strip_tags($_comment->advantages) }}
        </div>
    @endif
    @if($_comment->disadvantages)
        <div class="minus_text">
            <b>
                @lang('forms.labels.comment.disadvantages'):
            </b>
            {{ strip_tags($_comment->disadvantages) }}
        </div>
    @endif
</div>
@if($_comment->_reply->isNotEmpty())
    @foreach($_comment->_reply as $_reply)
        <div class="review_item reply"
             id="comment-{{ $_reply->id }}">
            <div class="header">
                <div class="name">
                    {{ $_reply->name }}
                </div>
                <div class="date">
                    {{ $_reply->created_at->format('d-m-Y H:i') }}
                </div>
            </div>
            <div class="text">
                <p>
                    {{ strip_tags($_reply->comment) }}
                </p>
            </div>
        </div>
    @endforeach
@endif