<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
  public function saving(Invoice $invoice): void
  {
    // Recalculer seulement si la facture est editable et items chargés
    if (
      $invoice->isEditable()
      && $invoice->relationLoaded('items')
      && !$invoice->skipObserverRecalculation
    ) {
      $invoice->recalculateTotals();
    }
  }
}
