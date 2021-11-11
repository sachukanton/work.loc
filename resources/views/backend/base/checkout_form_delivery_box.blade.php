@php
    $_locale = wrap()->get('locale', env('DEFAULT_LOCALE'));
@endphp
@switch($_delivery_method)
    @case('pickup')
    @php
        $_pickup_data = config('os_shop.delivery_method.pickup.values');
        $_pickup_values = [];
        foreach ($_pickup_data as $_key => $_value){
            $_pickup_values[$_key] = $_value[$_locale];
        }
    @endphp
    <div class="uk-card uk-card-body uk-card-small uk-background-color-grey lighten-4 uk-border-rounded">
        @formField('delivery.pickup', ['type' => 'radio', 'required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.delivery_code')], 'values' => $_pickup_values,  'prefix'   => '<div class="form-group">', 'suffix'   => '</div>', 'form_id' => 'form-checkout-order'])
    </div>
    @break
    @case('np_courier')
    <div class="uk-card uk-card-body uk-card-small uk-background-color-grey lighten-4 uk-border-rounded">
        @php
            $_np = new NovaPoshta($_locale);
            echo $_np->show_fields_type_courier(['city' => ($_query['area'] ?? NULL)]);
        @endphp
    </div>
    @break
    @case('np_branch')
    <div class="uk-card uk-card-body uk-card-small uk-background-color-grey lighten-4 uk-border-rounded">
        @php
            $_np = new NovaPoshta($_locale);
            echo $_np->show_fields_type_branch(['area' => ($_query['area'] ?? NULL), 'city' => ($_query['city'] ?? NULL)]);
        @endphp
    </div>
    @break
    @case('ukr_branch')
    <div class="uk-card uk-card-body uk-card-small uk-background-color-grey lighten-4 uk-border-rounded">
        @formField('delivery.ukr_code', ['required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.delivery_code')]])
        @formField('delivery.ukr_city', ['type' => 'autocomplete', 'class' => 'uk-autocomplete', 'required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.delivery_city'), 'data-url' => _r('ajax.checkout_delivery_city'), 'data-value' => 'name',]])
        @formField('delivery.ukr_street', ['required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.delivery_street')]])
    </div>
    <div class="uk-child-width-1-2 uk-grid-small"
         uk-grid>
        <div>
            @formField('delivery.ukr_house', ['required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.delivery_house')]])
        </div>
        <div>
            @formField('delivery.ukr_flat', ['attributes' => ['placeholder' => trans('forms.fields.checkout.delivery_flat')]])
        </div>
    </div>
    @break
@endswitch