<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//------------ For troubleshooting purposes and  testing purposes --------------//
\App\Http\Controllers\Debugger::routes();

Route::group(['namespace' => 'Pages'], function () {
    //------------ Generic App-Page Routes -----------------------------------------
    Route::group(['as' => 'app.'], function () {
        Route::get('/', ['as' => 'home', 'uses' => 'AppController@index']);
    });

    Route::group(['middleware' => ['auth' => 'auth']], function () {
        //------------ Admin Panel Page Routes ----------------------------------------------
        Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['admin' => 'auth.admin']], function () {
            Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'AdminController@dashboard']);
            Route::get('persons', ['as' => 'persons', 'uses' => 'AdminController@persons']);
            Route::get('camps', ['as' => 'camps', 'uses' => 'AdminController@camps']);
            Route::get('organizations', ['as' => 'organizations', 'uses' => 'AdminController@organizations']);
            Route::get('users', ['as' => 'users', 'uses' => 'AdminController@users']);
            Route::get('locations-states', ['as' => 'locations_states', 'uses' => 'AdminController@locations_states']);
            Route::get('locations-lgas/{state_code}', ['as' => 'locations_lgas', 'uses' => 'AdminController@locations_lgas']);
            Route::get('settings', ['as' => 'settings', 'uses' => 'AdminController@settings']);
            Route::get('sys_log', ['as' => 'sys_log', 'uses' => 'AdminController@sysLog']);
        });

        //------------ Data-Entry-Officer's Panel Page Routes ----------------------------------------------
        Route::group(['as' => 'deo.', 'prefix' => 'deo', 'middleware' => ['admin' => 'auth.deo']], function () {
            Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DeoController@dashboard']);
            Route::get('persons', ['as' => 'persons', 'uses' => 'DeoController@persons']);
            Route::get('camps', ['as' => 'camps', 'uses' => 'DeoController@camps']);
            Route::get('organizations', ['as' => 'organizations', 'uses' => 'DeoController@organizations']);
            Route::get('persons/enroll', ['as' => 'enroll_idp', 'uses' => 'DeoController@enrollPerson']);
            Route::get('persons/verify/{id}', ['as' => 'verify_idp', 'uses' => 'DeoController@verifyPerson']);
        });

        Route::group(['as' => 'account.', 'prefix' => 'account'], function () {
            Route::get('profile', ['as' => 'profile', 'uses' => 'AccountController@profile']);
            Route::get('password', ['as' => 'password', 'uses' => 'AccountController@password']);
            Route::post('change-image', ['as' => 'profile.image', 'uses' => 'AccountController@changeImage']);
        });
    });
});

//-------------Base Routes-----------------------------------------------------
Route::group(['middleware' => ['auth' => 'auth'], 'namespace' => 'Base'], function () {
    Route::group(['as' => 'user.', 'prefix' => 'user'], function () {
        Route::post('update', ['as' => 'update', 'uses' => 'UserController@update']);
        Route::post('set-photo', ['as' => 'photo', 'uses' => 'UserController@setPhoto']);
        Route::post('change-password', ['as' => 'change_password', 'uses' => 'UserController@changePassword']);
        Route::group(['middleware' => ['admin' => 'auth.admin']], function () {
            Route::post('add', ['as' => 'add', 'uses' => 'UserController@add']);
            Route::post('manage-list', ['as' => 'manage_list', 'uses' => 'UserController@manageList']);
        });
    });

    Route::group(['as' => 'location.', 'prefix' => 'location', 'middleware' => ['admin' => 'auth.admin']], function () {
        Route::post('add-state', ['as' => 'add_state', 'uses' => 'LocationController@addState']);
        Route::post('update-state', ['as' => 'update_state', 'uses' => 'LocationController@updateState']);
        Route::post('manage-states', ['as' => 'manage_state_list', 'uses' => 'LocationController@manageStateList']);
        Route::post('add-lga', ['as' => 'add_lga', 'uses' => 'LocationController@addLga']);
        Route::post('update-lga', ['as' => 'update_lga', 'uses' => 'LocationController@updateLga']);
        Route::post('manage-lgas', ['as' => 'manage_lga_list', 'uses' => 'LocationController@manageLgaList']);
    });

    Route::group(['as' => 'camp.', 'prefix' => 'camp', 'middleware' => ['admin' => 'auth.admin']], function () {
        Route::post('add', ['as' => 'add', 'uses' => 'CampController@add']);
        Route::post('update', ['as' => 'update', 'uses' => 'CampController@update']);
        Route::post('manage-list', ['as' => 'manage_list', 'uses' => 'CampController@manageList']);
    });

    Route::group(['as' => 'organization.', 'prefix' => 'organization', 'middleware' => ['admin' => 'auth.admin']], function () {
        Route::post('add', ['as' => 'add', 'uses' => 'OrganizationController@add']);
        Route::post('update', ['as' => 'update', 'uses' => 'OrganizationController@update']);
        Route::post('manage-list', ['as' => 'manage_list', 'uses' => 'OrganizationController@manageList']);
    });

    Route::group(['as' => 'idp.', 'prefix' => 'idp', 'middleware' => ['admin' => 'auth.deo']], function () {
        Route::post('update', ['as' => 'update', 'uses' => 'PersonController@update']);
        Route::post('set-photo', ['as' => 'set_photo', 'uses' => 'PersonController@setPhoto']);
        Route::post('discard', ['as' => 'discard', 'uses' => 'PersonController@discard']);
        Route::post('manage-list', ['as' => 'manage_list', 'uses' => 'PersonController@manageList']);
    });
});

//-------------Authentication, Registration & Password Reset roues-----------------//
Route::group(['as' => 'auth.', 'namespace' => 'Auth'], function () {
    // Authentication Routes...
    Route::get('login', ['as' => 'login', 'uses' => 'LoginController@showLoginForm']);
    Route::post('login', ['as' => 'login', 'uses' => 'LoginController@login']);
    Route::post('logout', ['as' => 'logout', 'uses' => 'LoginController@logout'])->middleware('auth');

    // Registration Routes...
    /*
    Route::get('signup', ['as' => 'signup', 'uses' => 'RegisterController@showRegistrationForm']);
    Route::post('signup', ['as' => 'signup', 'uses' => 'RegisterController@register']);
    */

    // Password Reset Routes...
    Route::get('password/reset', ['as' => 'password.form', 'uses' => 'ForgotPasswordController@showLinkRequestForm']);
    Route::post('password/email', ['as' => 'password.email', 'uses' => 'ForgotPasswordController@sendResetLinkEmail']);
    Route::get('password/reset/{token}', ['as' => 'password.link', 'uses' => 'ResetPasswordController@showResetForm']);
    Route::post('password/reset', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);

    // Socialite
    Route::get('auth/{service}/{action}', ['as' => 'social.redirect', 'uses' => 'SocialAuthController@redirectToProvider']);
    Route::get('auth/{service}/{action}/callback', ['as' => 'social.callback', 'uses' => 'SocialAuthController@handleProviderCallback']);
});
