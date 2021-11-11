@switch($_payment_method)
    @case('cashless_payments')
    <div class="uk-card uk-card-body uk-card-small uk-background-color-grey lighten-4 uk-border-rounded">
        @formField('payment.company_name', ['required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.payment_method_company_name')]])
        @formField('payment.company_erdpo', ['required' => TRUE, 'attributes' => ['placeholder' => trans('forms.fields.checkout.payment_method_erdpo')]])
    </div>
    @break
@endswitch