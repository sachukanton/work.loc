@php
    $_data = [
        "street" => NULL,
        "house" => NULL,
        "entrance" => NULL,
        "floor" => NULL,
        "apartment" => NULL
    ];
    if(isset($data) && $data) $_data = array_merge($_data, (array)$data);
@endphp
<div id="form-checkout-order-delivery-fields-box"
     class="uk-margin-top uk-margin-bottom">
    @if($type == 'delivery')
        <div class="delivery-items uk-grid-small uk-child-width-1-2 uk-grid">
            @formField('delivery_address.street', ['value' => $_data['street'], 'required' => TRUE, 'attributes' =>
       ['placeholder' =>
       trans('forms.fields.checkout.delivery_street')], 'form_id' => $form_id])
            @formField('delivery_address.house', ['value' => $_data['house'], 'required' => TRUE, 'attributes' =>
            ['placeholder' =>
            trans('forms.fields.checkout.delivery_house_full')], 'form_id' => $form_id])
            {{--@formField('delivery_address.entrance', ['value' => $_data['entrance'], 'attributes' => ['placeholder' =>--}}
            {{--trans('forms.fields.checkout.delivery_entrance')], 'form_id' => $form_id])--}}
            @formField('delivery_address.floor', ['value' => $_data['floor'], 'attributes' => ['placeholder' =>
            trans('forms.fields.checkout.delivery_floor')], 'form_id' => $form_id])
            @formField('delivery_address.apartment', ['value' => $_data['apartment'], 'attributes' => ['placeholder' =>
            trans('forms.fields.checkout.delivery_flat')], 'form_id' => $form_id])
        </div>
    @elseif($type == 'pickup')
        {{variable('pickup')}}
    @endif
</div>
