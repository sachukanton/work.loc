@php
    switch($_item->status){
        case 1:
        case 2:
            $_class = 'primary';
        break;
        case 3:
            $_class = 'success';
        break;
        case 4:
            $_class = 'danger';
        break;
        default:
            $_class = 'secondary';
        break;
    }
@endphp
<button class="uk-modal-close-default uk-text-inverse"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked uk-form-horizontal use-ajax"
      id="modal-menu-item-form"
      method="POST"
      action="{{ _r('oleus.shop_orders.save_order') }}">
    <input type="hidden"
           value="{{ $_item->id }}"
           name="item">
    <div class="uk-modal-header uk-background-{{ $_class }}">
        <h2 class="uk-modal-title uk-text-uppercase uk-text-thin uk-text-inverse">
            @lang('shop.labels.order', ['order' => $_item->id])
            (<span class="uk-text-bold">@lang(ShopOrder::ORDER_STATUS[$_item->status])</span>)
        </h2>
    </div>
    <div class="uk-modal-body">
        <div uk-grid
             style="margin-left: -15px;"
             class="uk-grid uk-grid-small">
            <div class="uk-width-1-4">
                <div class="uk-form-row">
                    <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Данные заказа
                        </span>
                    </h3>
                    <dl class='uk-description-list-horizontal uk-margin-remove'>
                        <dt class='uk-text-right'>
                            Тип:
                        </dt>
                        <dd>
                            {{ $_item->type == 'full' ? 'Полный заказ' : 'Быстрый заказ'   }}
                        </dd>
                        <dt class='uk-text-right'>
                            Дата и время оформления:
                        </dt>
                        <dd>
                            {{ $_item->created_at->format('d.m.Y - H:i') }}
                        </dd>
                        <dt class='uk-text-right'>
                            Предзаказ:
                        </dt>
                        <dd>
                            {{ $_item->pre_order_at ? $_item->pre_order_at->format('d.m.Y - H:i') : '-//-' }}
                        </dd>
                        <dt class='uk-text-right'>
                            Количество персон:
                        </dt>
                        <dd>
                            {{ $_item->person ?: '-//-' }}
                        </dd>
                        <dt class='uk-text-right'>
                            Общая сумма заказ:
                        </dt>
                        <dd class='uk-text-black uk-text-success'
                            id="form-field-order-amount-data">
                            @if($_item->discount)
                                {{ $_item->format_amount['amount_less_discount']['format']['view_price'] }}
                                &nbsp;{{ $_item->format_amount['amount_less_discount']['currency']['suffix'] }}
                            @else
                                {{ $_item->format_amount['amount']['format']['view_price'] }}
                                &nbsp;{{ $_item->format_amount['amount']['currency']['suffix'] }}
                            @endif
                        </dd>
                        <dt class='uk-text-right'>
                            Комментарий к заказу:
                        </dt>
                        <dd>
                            {!! $_item->comment ?: '-' !!}
                        </dd>
                    </dl>
                    <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Оплата
                        </span>
                    </h3>
                    <dl class='uk-description-list-horizontal uk-margin-remove uk-visible'>
                        <dt class='uk-margin-remove'>
                            Форма оплаты:
                        </dt>
                        <dd>
                            {{ $_item->payment_method ? trans('shop.payment_method.payment_method_' . $_item->payment_method) : '-//-' }}
                        </dd>
                        @if($_item->payment_method == 'card')
                            <dt class='uk-margin-remove'>
                                Статус оплаты:
                            </dt>
                            <dd>
                                {!! $_item->payment_status ? 'Оплачено' : 'Не оплачено' !!}
                            </dd>
                            <dt class='uk-margin-remove'>
                                Номер транзакции:
                            </dt>
                            <dd>
                                {!! $_item->payment_transaction_number ?: '-//-' !!}
                            </dd>
                            <dt class='uk-margin-remove'>
                                Статус транзакции:
                            </dt>
                            <dd>
                                {!! $_item->payment_transaction_number ? ($_item::LIQPAY_STATUS[$_item->payment_transaction_status] ?: '-//-') : '-//-' !!}
                            </dd>
                        @endif
                        @if($_item->payment_method == 'cash')
                            <dt class='uk-margin-remove'>
                                Сдача с:
                            </dt>
                            <dd>
                                {!! $_item->surrender ?: '-//-' !!}
                            </dd>
                        @endif
                    </dl>
                    <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Доставка
                        </span>
                    </h3>
                    <dl class='uk-description-list-horizontal uk-margin-remove uk-visible'>
                        <dt class='uk-margin-remove'>
                            Метод доставки:
                        </dt>
                        <dd>
                            {{ $_item->delivery_method ? trans('shop.delivery_method.delivery_method_' . $_item->delivery_method) : '-//-' }}
                        </dd>
                        @if($_item->delivery_method == 'delivery')
                            <dt class='uk-margin-remove'>
                                Адрес доставки:
                            </dt>
                            <dd>
                                {!! $_item->formation_address ?: '-//-' !!}
                            </dd>
                        @endif
                        <dt class='uk-margin-remove'>
                            Бесплатная доставка:
                        </dt>
                        <dd>
                            {!! $_item->delivery_free || $_item->delivery_method == 'pickup' ? 'Да' : 'Нет' !!}
                        </dd>
                    </dl>
                    <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Данные клиента
                        </span>
                    </h3>
                    <dl class='uk-description-list-horizontal uk-margin-remove uk-visible'>
                        <dt class='uk-margin-remove'>
                            Фамилия:
                        </dt>
                        <dd>
                            {{ $_item->surname ?: '-//-' }}
                        </dd>
                        <dt class='uk-margin-remove'>
                            Имя:
                        </dt>
                        <dd>
                            {{ $_item->name }}
                        </dd>
                        <dt class='uk-margin-remove'>
                            Номер телефона:
                        </dt>
                        <dd>
                            {!! $_item->format_phone !!}
                        </dd>
                    </dl>
                    <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Подарок
                        </span>
                    </h3>
                    @if($_item->_gift->exists)
                        <div class="uk-text-bold">{{ $_item->_gift->title }}</div>
                    @else
                        <div>-//-</div>
                    @endif
                </div>
            </div>
            <div class="uk-width-3-4">
                <div class="uk-form-row"
                     id="form-field-order_products-object">
                    <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Состав заказа
                        </span>
                    </h3>
                    @include('backend.shop.partials.items_view_order', compact('_item'))
                    @if($_item->status <= 2)
                        @formField('status', ['type' => 'radio', 'label' => 'Статус заказа', 'selected' =>
                        $_item->status ?: 2, 'values' => [0 => 'shop.status.0', 1 => 'shop.status.1', 2 =>
                        'shop.status.2', 3 => 'shop.status.3', 4 => 'shop.status.4'], 'attributes' =>
                        ['class' => 'uk-select2'], 'class' => 'uk-select2'])
                        @formField('comment', ['type' => 'textarea', 'label' => 'Примечание оператора', 'attributes' =>
                        ['rows' => 3], 'value' => $_item->manager_comment])
                    @else
                        <h3 class="uk-heading-line uk-text-uppercase">
                        <span>
                            Примечание оператора
                        </span>
                        </h3>
                        <div>
                            {!! $_item->manager_comment ?: '<span class="uk-text-muted">нет комметария</span>' !!}
                        </div>
                    @endif
                    @if($_item->status <= 2)
                        <div class="uk-text-danger uk-margin-top">
                            <hr>
                            <p>
                                <strong>Внимание!!!</strong><br>
                                При изменение количестава товара в заказе, либо удаления товара из заказа, обязательно
                                прожать кнопку "Пересчитать". Иначе изменения не будут применены.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="uk-modal-footer uk-text-right uk-padding-remove-top">
        @if($_item->status <= 2)
            <button type="submit"
                    name="recalculate"
                    value="1"
                    class="uk-button uk-button-primary uk-border-rounded uk-margin-small-right uk-text-uppercase">
            <span uk-icon="icon:replay; ratio:.8"
                  style="margin-right: 5px;"></span>
                Пересчитать
            </button>
            <button type="submit"
                    name="save"
                    value="1"
                    class="uk-button uk-button-success uk-border-rounded uk-margin-small-right uk-text-uppercase">
            <span uk-icon="icon:save; ratio:.8"
                  style="margin-right: 5px;"></span>
                Соханить
            </button>
        @endif
        <button class="uk-button uk-button-secondary uk-button-icon uk-border-rounded uk-modal-close"
                uk-icon="icon: clearclose"
                type="button"></button>
    </div>
</form>
