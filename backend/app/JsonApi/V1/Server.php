<?php

namespace App\JsonApi\V1;

use App\JsonApi\V1\Invoices\InvoiceSchema;
use App\JsonApi\V1\InvoiceItems\InvoiceItemSchema;
use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{

  /**
   * The base URI namespace for this server.
   *
   * @var string
   */
  protected string $baseUri = '/api/v1';

  /**
   * Bootstrap the server when it is handling an HTTP request.
   *
   * @return void
   */
  public function serving(): void
  {
    // no-op
  }

  /**
   * Get the server's list of schemas.
   *
   * @return array
   */
  protected function allSchemas(): array
  {
    return [
      InvoiceSchema::class,
      InvoiceItemSchema::class,
    ];
  }

  // Désactiver l'autorisation pour simplifier
  public function authorizable(): bool
  {
    return false;
  }
}
