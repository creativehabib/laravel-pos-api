<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesManagerController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/**
 *
 */
//Route::get('test', [ScriptManager::class, 'getLocationData']);
//Route::get('country', [ScriptManager::class, 'getCountry']);

Route::post('login', [AuthController::class, 'login']);

Route::post('divisions', [DivisionController::class, 'index']);
Route::post('districts/{id}', [DistrictController::class, 'index']);
Route::post('areas/{id}', [AreaController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum','auth:admin']], static function (){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('get-brand-list',[BrandController::class, 'getBrandsList']);
    Route::get('get-country-list',[CountryController::class, 'getCountriesList']);
    Route::get('get-supplier-list',[SupplierController::class, 'getSupplierList']);
    Route::post('get-shop-list',[ShopController::class, 'getShopList']);
    Route::get('get-attribute-list',[AttributeController::class, 'getAttributeList']);
    Route::post('product-photo-upload/{id}',[ProductPhotoController::class, 'store']);
    Route::apiResource('category',CategoryController::class);
    Route::apiResource('sub-category',SubCategoryController::class);
    Route::apiResource('brand', BrandController::class);
    Route::apiResource('supplier', SupplierController::class);
    Route::apiResource('attribute', AttributeController::class);
    Route::apiResource('value', AttributeValueController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('photo', ProductPhotoController::class);
    Route::apiResource('shop', ShopController::class);
    Route::apiResource('sales-manager', SalesManagerController::class);

});

Route::group(['middleware' => ['auth:admin,sales_manager']], function (){
    Route::apiResource('product', ProductController::class)->only('index', 'show');
    Route::apiResource('customer', CustomerController::class);
    Route::apiResource('order', OrderController::class);
    Route::get('get-payment-methods', [PaymentMethodController::class, 'index']);

    Route::get('get-product-columns', [ProductController::class, 'get_product_columns']);

    Route::get('get-category-list',[CategoryController::class, 'getCategoryList']);
    Route::get('get-sub-category-list/{category_id}',[SubCategoryController::class, 'getSubCategoryList']);
    Route::get('get-product-list-for-bar-code',[ProductController::class, 'getProductListForBarCode']);
    Route::get('get-reports',[ReportController::class, 'index']);
    Route::get('get-add-product-data', [ProductController::class, 'get_add_product_data']);

});


Route::group(['middleware' => ['auth:sales_manager']], static function (){
    //Route::apiResource('product', ProductController::class)->only('index', 'show');
});


