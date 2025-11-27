<?php

namespace App\JsonApi\V1\InvoiceItems;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class InvoiceItemRequest extends ResourceRequest
{

  /**
   * Get the validation rules for the resource.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'description' => ['required', 'string', 'max:255'],
      'quantity' => ['required', 'integer', 'min:1'],
      'unitPrice' => ['required', 'numeric', 'min:0'],
      'invoice' => ['required', JsonApiRule::toOne()],
    ];
  }
}
