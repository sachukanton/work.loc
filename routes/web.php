<?php

// Site Map
Route::get('/sitemap.xml', 'SiteMapController@generate');
Route::get('/sitemap-{index?}.xml', 'SiteMapController@generate');

// User
Route::namespace('Auth')->group(function () {
    // Login
    Route::get('/login', [
        'as'   => 'login',
        'uses' => 'LoginController@showLoginForm'
    ]);
    Route::post('/login', [
        'as'   => 'login',
        'uses' => 'LoginController@login'
    ]);

    // Register
    Route::get('/register', [
        'as'   => 'register',
        'uses' => 'RegisterController@showRegistrationForm'
    ]);
    Route::post('/register', [
        'as'   => 'register',
        'uses' => 'RegisterController@register'
    ]);

    // Verification email
    Route::get('/email/verify', [
        'as'   => 'verification.notice',
        'uses' => 'VerificationController@show'
    ]);
    Route::get('/email/verify/{id}', [
        'as'   => 'verification.verify',
        'uses' => 'VerificationController@verify'
    ]);
    Route::get('/email/resend', [
        'as'   => 'verification.resend',
        'uses' => 'VerificationController@resend'
    ]);

    // Reset password
    Route::get('password/email', [
        'as'   => 'password.email',
        'uses' => 'ForgotPasswordController@showLinkRequestForm'
    ]);
    Route::post('/password/email', [
        'as'   => 'password.email',
        'uses' => 'ForgotPasswordController@sendResetLinkEmail'
    ]);
    Route::get('/password/reset/{token}', [
        'as'   => 'password.reset',
        'uses' => 'ResetPasswordController@showResetForm'
    ]);
    Route::post('/password/reset', [
        'as'   => 'password.reset',
        'uses' => 'ResetPasswordController@reset'
    ]);

    // Logout
    Route::match([
        'get',
        'post'
    ], '/logout', [
        'as'   => 'logout',
        'uses' => 'LoginController@logout'
    ]);

    // Account
    Route::prefix('account')->group(function () {
        Route::get('/', [
            'as'   => 'personal_area',
            'uses' => 'AccountController@orders'
        ]);
        Route::get('/edit', [
            'as'   => 'personal_area.edit',
            'uses' => 'AccountController@edit'
        ]);
        Route::match([
            'get',
            'post'
        ], '/reviews/{page_number?}', [
            'as'   => 'personal_area.reviews',
            'uses' => 'AccountController@reviews'
        ]);
        Route::get('/wish-list', [
            'as'   => 'personal_area.wish_list',
            'uses' => 'AccountController@wish_list'
        ]);
    });
});

// Frontend
Route::namespace('Frontend')->group(function () {
    Route::get('/certificates/{certificate}', [
        'as'   => 'page.shop_certificate',
        'uses' => 'Shop\CertificateController@index'
    ]);
    Route::get('/checkout', [
        'as'   => 'page.shop_checkout',
        'uses' => 'Shop\CheckoutController@index'
    ]);
    Route::get('/checkout-thanks', [
        'as'   => 'page.shop_checkout_thanks_page',
        'uses' => 'Shop\CheckoutController@thanks_page'
    ]);
    Route::match([
        'get',
        'post'
    ], '/search/{page_number?}', [
        'as'   => 'page.search',
        'uses' => 'SearchController@index'
    ]);
    Route::post('/payment-response', [
        'uses' => 'Shop\LiqPayController@paymentResponse',
        'as'   => 'payment.response',
    ]);
    Route::post('/liqpay-status', [
        'uses' => 'Shop\LiqPayController@liqpayStatus',
        'as'   => 'payment.status'
    ]);
});

// Other
Route::match([
    'get',
    'post'
], '/{path?}', [
    'as'   => '',
    'uses' => 'QueryPathController@index'
])
    ->where([
        'path' => '^(?!oleus|ajax).*?'
    ]);
