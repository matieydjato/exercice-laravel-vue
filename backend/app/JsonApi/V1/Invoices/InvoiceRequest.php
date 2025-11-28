<?php

namespace App\JsonApi\V1\Invoices;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class InvoiceRequest extends ResourceRequest
{

  /**
   * Get the validation rules for the resource.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'customerName' => ['required', 'string', 'max:255'],
      'currency' => ['sometimes', 'string', 'size:3'],
      'status' => ['sometimes', Rule::in(['draft', 'sent', 'paid'])],
      'issuedAt' => ['required', 'date'],
      'dueAt' => ['required', 'date'],
    ];
  }
}
