<?php

namespace App\JsonApi\V1\InvoiceItems;

use App\Models\InvoiceItem;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Schema;

class InvoiceItemSchema extends Schema
{

  /**
   * The model the schema corresponds to.
   *
   * @var string
   */
  public static string $model = InvoiceItem::class;

  /**
   * Get the resource fields.
   *
   * @return array
   */
  public function fields(): array
  {
    return [
      ID::make(),
      Str::make('description'),
      Number::make('quantity'),
      Number::make('unitPrice'),
      DateTime::make('createdAt')->readOnly(),
      DateTime::make('updatedAt')->readOnly(),
      BelongsTo::make('invoice'),
    ];
  }

  /**
   * Get the resource filters.
   *
   * @return array
   */
  public function filters(): array
  {
    return [];
  }
}
