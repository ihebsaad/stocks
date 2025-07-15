<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProvidersController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\ClientApiController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\PickupSlipController;
use App\Http\Controllers\API\DeliveryController;

use App\Http\Controllers\ParcelController;      
use App\Http\Controllers\HomeController;      

Route::get('/products/list', [ProductsController::class, 'getProducts'])->name('products.list');

Route::post( '/parcels/search', [ParcelController::class, 'searchParcel'])->name('parcels.search');

Route::resource('pickup', PickupSlipController::class);
 
Route::get('/stats', [StatisticsController::class, 'index'])->name('stats');

Route::get('/get-orders', [OrderController::class, 'getOrders'])->name('orders.getOrders');
// Nouvelles routes pour les commandes en cours et archives
Route::get('/orders-current', [OrderController::class, 'current'])->name('orders.current');
Route::get('/orders-archives', [OrderController::class, 'archives'])->name('orders.archives');
Route::get('/get-current-orders', [OrderController::class, 'getCurrentOrders'])->name('orders.getCurrentOrders');
Route::get('/get-archived-orders', [OrderController::class, 'getArchivedOrders'])->name('orders.getArchivedOrders');
Route::post('/orders/update-notes', [OrderController::class, 'updateNotes'])->name('orders.update-notes');


Route::resource('categories', CategoriesController::class);
Route::resource('users', UsersController::class);
Route::resource('providers', ProvidersController::class);
Route::resource('orders', OrderController::class);
Route::resource('parcels', ParcelController::class);

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Settings
Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('/update_setting', [App\Http\Controllers\SettingsController::class, 'update_setting'])->name('update_setting');
Route::post('/update_text', [App\Http\Controllers\SettingsController::class, 'update_text'])->name('update_text');


// users
Route::get('profile', [UsersController::class, 'profile'])->name('profile');
Route::post('/updateuser',[UsersController::class, 'updateuser'])->name('updateuser');
Route::get('/loginAs/{id}', [UsersController::class, 'loginAs'])->name('loginAs');
Route::post('/users/ajoutimage',[UsersController::class, 'ajoutimage'])->name('users.ajoutimage');
Route::post('/activer/{id}', [UsersController::class, 'activer'])->name('activer');
Route::post('/desactiver/{id}', [UsersController::class, 'desactiver'])->name('desactiver');

 
// Routes pour les sociétés de livraison
Route::get('delivery-companies/get-data', [App\Http\Controllers\DeliveryCompanyController::class, 'getDeliveryCompanies'])->name('delivery-companies.getData');
Route::resource('delivery-companies', App\Http\Controllers\DeliveryCompanyController::class);
Route::resource('products', ProductsController::class);

// Routes pour la gestion des images
Route::post('products/set-main-image/{id}', [ProductsController::class, 'setMainImage'])->name('products.set-main-image');
Route::delete('products/delete-image/{id}', [ProductsController::class, 'deleteImage'])->name('products.delete-image');


Route::delete('/products/image/{id}', [ProductsController::class, 'deleteImage'])->name('product.image.delete');
Route::post('/products/{id}/duplicate', [ProductsController::class, 'duplicateProduct'])->name('products.duplicate');


Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/entries', [StockEntryController::class,'index'])->name('entries.index');
    Route::get('/entries/create', [StockEntryController::class,'create'])->name('entries.create');
    Route::post('/entries', [StockEntryController::class,'store'])->name('entries.store');
    Route::get('/entries/{entry}', [StockEntryController::class,'show'])->name('entries.show');
 
    // Routes pour la mise à jour de l'entrée de stock
    Route::post('/entries/{entry}/update-description', [StockEntryController::class, 'updateDescription'])->name('stock.entries.update-description');
    Route::get('/entries/calculate-totals/{item}', [StockEntryController::class, 'calculateTotals'])->name('stock.entries.calculate-totals');

    // Route pour la mise à jour des éléments d'entrée de stock
    Route::post('/entry-items/{item}/update', [StockEntryController::class, 'update'])->name('stock.entry-items.update');
});



Route::delete('orders/delete-image/{id}', [OrderController::class, 'deleteImage'])->name('orders.delete-image');

// Routes API pour la gestion des clients
Route::get('clients/search', [ClientApiController::class, 'search'])->name('clients.search');
Route::get('clients/{id}/details', [ClientApiController::class, 'getClientDetails'])->name('clients.details');
Route::get('clients/check-phone', [ClientApiController::class, 'checkPhone'])->name('clients.check-phone');




Route::prefix('delivery/{company}')->group(function () {
    Route::get('/list', [DeliveryController::class, 'list']);
    Route::get('/get/{barcode}', [DeliveryController::class, 'get']);
    Route::post('/create', [DeliveryController::class, 'create']);
});

Route::resource('providers', ProvidersController::class);
Route::resource('users', UsersController::class);

Route::post('/parcels/generate-pdf', [ParcelController::class, 'generateParcelsPdf'])->name('parcels.generatePdf');
Route::get('/getparcels', [ParcelController::class, 'getParcels'])->name('parcels.getParcels');
#Route::get('/parcels', [ParcelController::class, 'index'])->name('parcels.index');
Route::delete('/parcels/{parcel}', [ParcelController::class, 'destroy'])->name('parcels.destroy');
Route::post('/parcels/{order}', [ParcelController::class, 'store'])->name('parcels.store');
#Route::get('/parcels/{parcel}/details', [ParcelController::class, 'show'])->name('parcels.details');
Route::get('/parcel/{id}/bl', [ParcelController::class, 'generateBL'])->name('parcel.bl');


Route::get('/invoices-by-product/{productId}', [HomeController::class, 'getInvoicesByProduct'])->name('invoices.by.product');
 
Route::delete('/variations/{variation}', [ProductsController::class, 'delete_variation'])->name('variations.destroy');


Route::group(['middleware' => 'auth'], function () {
    
    // Routes CRUD pour les clients
    Route::resource('clients', ClientController::class);
    
    // Routes API pour DataTables et AJAX
    Route::get('clients-data', [ClientController::class, 'getClients'])->name('clients.getClients');
    Route::get('clients-stats', [ClientController::class, 'getStats'])->name('clients.getStats');
    Route::get('clients-search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
});


Route::post('/promo-codes', [PromoCodeController::class, 'store'])->name('promo-codes.store');
Route::delete('/promo-codes/{promoCode}', [PromoCodeController::class, 'destroy'])->name('promo-codes.destroy');
Route::post('/promo-codes/{promoCode}/use', [PromoCodeController::class, 'use'])->name('promo-codes.use');


Route::get('pickup-data', [PickupSlipController::class, 'data'])->name('pickup.data');
Route::post('pickup/{pickupSlip}/update-status', [PickupSlipController::class, 'updateStatus'])->name('pickup.update-status');
Route::get('pickup/{pickupSlip}/print', [PickupSlipController::class, 'print'])->name('pickup.print');
Route::get('pickup-export', [PickupSlipController::class, 'export'])->name('pickup.export');
Route::get('pickup-statistics', [PickupSlipController::class, 'statistics'])->name('pickup.statistics');
    