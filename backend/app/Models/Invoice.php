<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
  protected $fillable = [
    'customer_name',
    'currency',
    'status',
    'discount_rate',
    'subtotal',
    'discount_amount',
    'total_due',
    'issued_at',
    'due_at',
  ];

  protected $casts = [
    'discount_rate' => 'decimal:2',
    'subtotal' => 'decimal:2',
    'discount_amount' => 'decimal:2',
    'total_due' => 'decimal:2',
    'issued_at' => 'date',
    'due_at' => 'date',
  ];

  protected $attributes = [
    'currency' => 'EUR',
    'status' => 'draft',
    'discount_rate' => 0,
    'subtotal' => 0,
    'discount_amount' => 0,
    'total_due' => 0,
  ];

  public function items(): HasMany
  {
    return $this->hasMany(InvoiceItem::class);
  }

  // Vérifier si la facture peut être modifiée
  public function isEditable(): bool
  {
    return $this->status === 'draft';
  }

  // Recalculer les totaux de la facture
  public function recalculateTotals(): void
  {
    if (!$this->relationLoaded('items')) {
      $this->load('items');
    }

    // Somme totale avant remise
    $this->subtotal = $this->items->sum(function ($item) {
      return $item->quantity * $item->unit_price;
    });

    // Montant de la remise
    $this->discount_amount = $this->subtotal * ($this->discount_rate / 100);

    // Total final
    $this->total_due = $this->subtotal - $this->discount_amount;
  }

  /**
   * Marquer la facture comme envoyée
   */
  public function markAsSent(): bool
  {
    if ($this->status !== 'draft') {
      return false;
    }

    $this->status = 'sent';

    return $this->save();
  }

  /**
   * Marquer la facture comme payée
   */
  public function markAsPaid(): bool
  {
    if ($this->status === 'paid') {
      return false;
    }

    $this->status = 'paid';

    return $this->save();
  }
}
