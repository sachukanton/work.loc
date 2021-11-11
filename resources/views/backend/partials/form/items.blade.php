<div class="uk-form-row">
    <div id="list-form-items">
        @isset($items)
            @if($items->isNotEmpty())
                @include('backend.partials.form.items_table', compact('items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        {{--@l('Добавить поле', 'oleus.forms.item', ['p' => ['form' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])--}}
        <div>
            <button class="uk-button uk-button-medium uk-button-success"
                    type="button">Добавить элемент
                <span uk-icon="keyboard_arrow_down"
                      class="uk-margin-small-left"></span>
            </button>
            <div id="forms-field-menu"
                 uk-dropdown="mode: click; pos: bottom-right;">
                <ul class="uk-nav uk-dropdown-nav uk-text-left">
                    <li>
                        @l('Текстовое поле', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'text'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Числовое поле', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'number'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Текстовая область', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'textarea'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Скрытое поле', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'hidden'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Элементы списка', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'select'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Флажки', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'checkbox'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Переключатели', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'radio'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Выбор файла', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'file'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Разметка', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'markup'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    <li>
                        @l('Шаг формы', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'break'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])
                    </li>
                    {{--<li>--}}
                    {{--@l('Календарь', 'oleus.forms.field', ['p' => ['form' => $entity->id, 'action' => 'add', 'key' => 'datepicker'], 'attributes' => ['class' => 'use-ajax', 'data-hide-load' => '1']])--}}
                    {{--</li>--}}
                </ul>
            </div>
        </div>
    </div>
</div>
