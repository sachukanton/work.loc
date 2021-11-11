<div class="uk-form-row">
    <div class="uk-form-controls">
        <div id="list-sliders-items"
             class="uk-list">
            @isset($items)
                @if($items->isNotEmpty())
                    @include('backend.partials.sliders.items_table', compact('items'))
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded"
                         uk-alert>
                        Список элементов пуст
                    </div>
                @endif
            @endisset
        </div>
        <div class="uk-clearfix uk-text-right">
            <div class="uk-button-group">
                @if($items && $items->count() > 1)
                    <a href="{{ _r('oleus.sliders.sort', ['menu' => $entity->id]) }}"
                       class="uk-button uk-button-medium uk-button-primary uk-button-save-sorting">
                        Сохранить сортировку
                    </a>
                @endif
                @l('Добавить слайд', 'oleus.sliders.item', ['p' => ['slider' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
            </div>
        </div>
    </div>
</div>