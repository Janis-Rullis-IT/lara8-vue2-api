<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->get('products', 'App\Http\Controllers\ProductController@index');

    $api->get('products/{productHash}/ingredients', 'App\Http\Controllers\Product\IngredientController@index');
    $api->get('products/{productType}/{productSlug}', 'App\Http\Controllers\ProductController@showBySlug');
    $api->get('products/{productHash}', 'App\Http\Controllers\ProductController@show');

    $api->post('products/{productHash}/ingredients', 'App\Http\Controllers\Product\IngredientController@store');
    $api->put('products/{productHash}/ingredients/sequence', 'App\Http\Controllers\Product\IngredientController@sequence');
    $api->delete('products/{productHash}/ingredients/{ingredientHash}', 'App\Http\Controllers\Product\IngredientController@remove');
});
