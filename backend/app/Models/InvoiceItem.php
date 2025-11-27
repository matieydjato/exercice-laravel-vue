<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
  protected $fillable = [
    'invoice_id',
    'description',
    'quantity',
    'unit_price',
  ];

  protected $casts = [
    'quantity' => 'integer',
    'unit_price' => 'decimal:4',
  ];

  public function invoice(): BelongsTo
  {
    return $this->belongsTo(Invoice::class);
  }

  // Calculer le total de la ligne
  public function getLineTotalPrice(): float
  {
    return $this->quantity * $this->unit_price;
  }
}
