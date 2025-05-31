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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\API\DeliveryController;

use App\Http\Controllers\ParcelController;      
use App\Http\Controllers\HomeController;      

Route::get('/products/list', [ProductsController::class, 'getProducts'])->name('products.list');


Route::get('/get-orders', [OrderController::class, 'getOrders'])->name('orders.getOrders');
// Nouvelles routes pour les commandes en cours et archives
Route::get('/orders-current', [OrderController::class, 'current'])->name('orders.current');
Route::get('/orders-archives', [OrderController::class, 'archives'])->name('orders.archives');
Route::get('/get-current-orders', [OrderController::class, 'getCurrentOrders'])->name('orders.getCurrentOrders');
Route::get('/get-archived-orders', [OrderController::class, 'getArchivedOrders'])->name('orders.getArchivedOrders');


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

Route::get('/getparcels', [ParcelController::class, 'getParcels'])->name('parcels.getParcels');
#Route::get('/parcels', [ParcelController::class, 'index'])->name('parcels.index');
Route::delete('/parcels/{parcel}', [ParcelController::class, 'destroy'])->name('parcels.destroy');
Route::post('/parcels/{order}', [ParcelController::class, 'store'])->name('parcels.store');
#Route::get('/parcels/{parcel}/details', [ParcelController::class, 'show'])->name('parcels.details');
Route::get('/parcel/{id}/bl', [ParcelController::class, 'generateBL'])->name('parcel.bl');
Route::post('/parcels/generate-pdf', [ParcelController::class, 'generateParcelsPdf'])->name('parcels.generatePdf');


Route::get('/invoices-by-product/{productId}', [HomeController::class, 'getInvoicesByProduct'])->name('invoices.by.product');

/*
 
 use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\QuotesController;
 use App\Http\Controllers\ColorController;
 use App\Http\Controllers\LivraisonsController;
  
//reglements
Route::post('/invoices/add_payment',[InvoicesController::class, 'add_payment'])->name('add_payment');
Route::get('/invoices/get_payment',[InvoicesController::class, 'get_payment'])->name('invoices.get_payment');
Route::post('/invoices/edit_payment',[InvoicesController::class, 'edit_payment'])->name('edit_payment');
Route::post('/invoices/delete_payment',[InvoicesController::class, 'delete_payment'])->name('delete_payment');
Route::post('/invoices/total_reglements',[InvoicesController::class, 'total_reglements'])->name('total_reglements');

 
Route::get('/quotes/all', [QuotesController::class, 'all'])->name('quotes.all');
Route::get('/quotes/liste', [QuotesController::class, 'liste']);
Route::get('/quotes/list', [QuotesController::class, 'getQuotes'])->name('quotes.list');

Route::get('/invoices-data', [InvoicesController::class, 'getInvoices'])->name('invoices.data');

Route::resource('categories', CategoriesController::class);
Route::resource('invoices', InvoicesController::class);
Route::resource('quotes', QuotesController::class);
Route::resource('livraisons', LivraisonsController::class);

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
/*
//Signature
Route::get('signatures', [App\Http\Controllers\SignaturePadController::class, 'index']);
Route::get('signature/{quote_id}', [App\Http\Controllers\SignaturePadController::class, 'signature'])->name('signature');
Route::post('signature-pad', [App\Http\Controllers\SignaturePadController::class, 'save'])->name('signpad.save');

//invoices
Route::get('/invoices/download_pdf/{id}', [InvoicesController::class, 'download_pdf'])->name('invoices.download_pdf');
Route::get('/invoices/show_pdf/{id}', [InvoicesController::class, 'show_pdf'])->name('invoices.show_pdf');
Route::get('/invoices/send_pdf/{id}', [InvoicesController::class, 'send_pdf'])->name('invoices.send_pdf');
Route::get('/invoices/add/{customer_id}', [InvoicesController::class, 'add'])->name('invoices.add');
Route::post('/invoices/update_totals',[InvoicesController::class, 'update_totals'])->name('invoices.update_totals');



//quotes
Route::get('/quotes/download_pdf/{id}', [QuotesController::class, 'download_pdf'])->name('quotes.download_pdf');
Route::get('/quotes/download_pdf_signature/{id}', [QuotesController::class, 'download_pdf_signature'])->name('quotes.download_pdf_signature');
Route::get('/quotes/show_pdf/{id}', [QuotesController::class, 'show_pdf'])->name('quotes.show_pdf');
Route::get('/quotes/show_pdf_tva/{id}', [QuotesController::class, 'show_pdf_tva'])->name('quotes.show_pdf_tva');
Route::get('/quotes/save_invoice/{id}', [QuotesController::class, 'save_invoice'])->name('quotes.save_invoice');
Route::post('/updatetotals',[QuotesController::class, 'updatetotals'])->name('updatetotals');
Route::get('/quotes/add/{customer_id}', [QuotesController::class, 'add'])->name('quotes.add');
Route::post('/quotes/ajout_signature', [QuotesController::class, 'ajout_signature'])->name('quotes.ajout_signature');
Route::get('/quotes/edit_men/{id}', [QuotesController::class, 'edit_men'])->name('quotes.edit_men');



//livraisons
Route::get('/livraisons/download_pdf/{id}', [LivraisonsController::class, 'download_pdf'])->name('livraisons.download_pdf');
Route::get('/livraisons/download_pdf_signature/{id}', [LivraisonsController::class, 'download_pdf_signature'])->name('livraisons.download_pdf_signature');
Route::get('/livraisons/show_pdf/{id}', [LivraisonsController::class, 'show_pdf'])->name('livraisons.show_pdf');
Route::get('/livraisons/show_pdf_tva/{id}', [LivraisonsController::class, 'show_pdf_tva'])->name('livraisons.show_pdf_tva');
Route::get('/livraisons/save_bl/{id}', [LivraisonsController::class, 'save_bl'])->name('livraisons.save_bl');
Route::post('/update_totals',[LivraisonsController::class, 'update_totals'])->name('update_totals');
Route::get('/livraisons/add/{customer_id}', [LivraisonsController::class, 'add'])->name('livraisons.add');
Route::get('/livraisons/edit_men/{id}', [LivraisonsController::class, 'edit_men'])->name('livraisons.edit_men');
Route::get('/calendar', [LivraisonsController::class, 'calendar'])->name('calendar');
 
Route::get('/colors', [ColorController::class, 'index'])->name('colors.index');
*/

 