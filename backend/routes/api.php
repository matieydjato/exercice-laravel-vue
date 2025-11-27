<?php

use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;

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
