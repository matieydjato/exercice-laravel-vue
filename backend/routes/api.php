<?php

use App\Http\Controllers\Api\V1\InvoiceActionController;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;

Route::middleware('token.auth')->group(function () {
  // Routes JSON:API standard
  JsonApiRoute::server('v1')
    ->prefix('v1')
    ->resources(function ($server) {
      $server->resource('invoices', JsonApiController::class)
        ->relationships(function ($relationships) {
          $relationships->hasMany('items');
        });

      $server->resource('invoice-items', JsonApiController::class);
    });

  // Routes des customs actions sur les factures
  Route::prefix('v1')->group(function () {
    Route::post('invoices/with-items', [InvoiceActionController::class, 'storeWithItems']);
    Route::put('invoices/{invoice}/with-items', [InvoiceActionController::class, 'updateWithItems']);
  });
});
