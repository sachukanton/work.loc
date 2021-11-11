<article class="uk-comment">
    <header class="uk-comment-header uk-grid-medium uk-flex-middle"
            uk-grid>
        <div class="uk-width-auto">
            <h4 class="uk-comment-title uk-margin-remove">
                @if($_item->user_id)
                    @l($_item->_user->full_name, 'oleus.users.edit', ['p' => ['user' => $_item->_user], 'attributes' => ['class' => 'uk-link-reset']])
                @else
                    {{ $_item->name }}
                @endif
            </h4>
        </div>
        <div class="uk-width-auto">
            {{ $_item->created_at->format('d m Y H:i') }}
        </div>
        <div class="uk-width-expand">
            @if($_item->status)
                <div class="uk-badge uk-badge-success">Опубликовано</div>
            @else
                <div class="uk-badge uk-badge-danger">Не опубликовано</div>
            @endif
            <span class="uk-text-primary">
                @l('', 'oleus.comments.item', ['p' => ['comment' => $_item->reply, 'action' => 'edit', 'id' => $_item->id], 'attributes' => ['class' => 'uk-button-icon use-ajax uk-margin-small-left', 'uk-icon' => 'icon: createmode_editedit']])
            </span>
            <span class="uk-text-danger">
                @l('', 'oleus.comments.item', ['p' => ['comment' => $_item->reply, 'action' => 'destroy', 'id' => $_item->id], 'attributes' => ['class' => 'uk-button-icon use-ajax uk-margin-small-left', 'uk-icon' => 'icon: delete']])
            </span>
        </div>
    </header>
    <div class="uk-comment-body">
        <p>
            {!! $_item->comment !!}
        </p>
    </div>
</article>
@if($_item->_reply->isNotEmpty())
    <ul>
        @foreach($_item->_reply as $_reply)
            <li>
                @include('backend.partials.comment.item', ['_item' => $_reply])
            </li>
        @endforeach
    </ul>
@endif