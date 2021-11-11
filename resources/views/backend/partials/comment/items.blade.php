<div class="uk-margin">
    <div id="list-comment-items">
        @isset($items)
            @if($items->isNotEmpty())
                @include('backend.partials.comment.items_list', compact('items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        @l('Добавить ответ', 'oleus.comments.item', ['p' => ['comment' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
    </div>
</div>
