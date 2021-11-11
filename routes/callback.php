<?php

Route::namespace('Dashboard')->group(function () {
    // File
    Route::post('/file-upload', [
        'as'   => 'ajax.file.upload',
        'uses' => 'FileController@upload'
    ]);
    Route::post('/file-update/{file}', [
        'as'   => 'ajax.file.update',
        'uses' => 'FileController@update'
    ]);
    //        Route::post('/file-remove', [
    //            'as'   => 'ajax.file.remove',
    //            'uses' => 'FileController@remove'
    //        ]);

    // Shortcut
    Route::post('/shortcut', [
        'as'   => 'ajax.shortcut',
        'uses' => 'Component\ShortCutController@index'
    ]);
});

// Checkout
Route::post('/delivery-box', [
    'as'   => 'ajax.checkout_delivery_box',
    'uses' => 'Callback\ShopController@delivery_box'
]);
Route::post('/payment-box', [
    'as'   => 'ajax.checkout_payment_box',
    'uses' => 'Callback\ShopController@payment_box'
]);
Route::post('/delivery-np', [
    'as'   => 'ajax.checkout_delivery_np',
    'uses' => 'Callback\ShopController@delivery_np'
]);
Route::post('/delivery-city', [
    'as'   => 'ajax.checkout_delivery_city',
    'uses' => 'Callback\ShopController@np_delivery_city'
]);
Route::post('/recount-products', [
    'as'   => 'ajax.checkout_recount_products',
    'uses' => 'Callback\ShopController@recount_products'
]);
Route::post('/remove-products', [
    'as'   => 'ajax.checkout_remove_products',
    'uses' => 'Callback\ShopController@remove_product_in_basket'
]);
Route::post('/emptying-basket', [
    'as'   => 'ajax.checkout_emptying_basket',
    'uses' => 'Callback\ShopController@emptying_basket'
]);

// Forms
Route::post('/open-form/{form}', [
    'as'   => 'ajax.open_form',
    'uses' => 'Callback\FormController@open_form'
]);
Route::post('/submit-form/{form}', [
    'as'   => 'ajax.submit_form',
    'uses' => 'Callback\FormController@submit_form'
]);

// Fields
Route::match([
    'post'
], '/fields/{type}/{action?}', [
    'as'   => 'ajax.fields_item',
    'uses' => 'Callback\FieldsController@field'
]);

// Buy product
Route::post('/basket/{shop_price}/{action?}', [
    'as'   => 'ajax.shop_action_basket',
    'uses' => 'Callback\ShopController@basket_action'
]);
Route::post('/add-product/{shop_product}', [
    'as'   => 'ajax.add_product_to_basket',
    'uses' => 'Callback\ShopController@add_product_to_basket'
]);

// Notify when appears
Route::post('/notify-when-appears', [
    'as'   => 'ajax.shop_notify_when_appears',
    'uses' => 'Callback\ShopController@notify_when_appears'
]);

// Buy one Click
Route::post('/buy-one-click', [
    'as'   => 'ajax.shop_buy_one_click',
    'uses' => 'Callback\ShopController@buy_one_click_form'
]);

// Checkout
Route::post('/checkout', [
    'as'   => 'ajax.shop_buy',
    'uses' => 'Callback\ShopController@buy'
]);

// Submit Application
Route::post('/submit-application', [
    'as'   => 'ajax.shop_submit_application',
    'uses' => 'Callback\ShopController@submit_application_form'
]);

// Shop search
Route::post('/search', [
    'as'   => 'ajax.shop_search_product',
    'uses' => 'Callback\ShopController@search_product'
]);

// reCaptcha
Route::post('/reCaptchaV3', [
    'as'   => 'ajax.validate_reCaptcha',
    'uses' => 'Callback\OtherController@validate_reCaptcha'
]);

//Route::post('/reCaptchaV3', [
//    'as'   => 'ajax.validate_reCaptcha',
//    'uses' => 'OtherController@reCaptchaV3'
//]);

// Account
Route::post('/profile', [
    'as'   => 'ajax.profile_edit',
    'uses' => 'Callback\AccountController@profile_edit'
]);

// Account
Route::post('/certificate', [
    'as'   => 'ajax.shop_certificate',
    'uses' => 'Callback\ShopController@certificate'
]);

//Route::post('/choiceImg', [
//    'as'   => 'ajax.choiceImg',
//    'uses' => 'Callback\ShopController@choiceImg'
//]);
