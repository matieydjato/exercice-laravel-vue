<?php

namespace App\Observers;

use App\Models\InvoiceItem;

class InvoiceItemObserver
{
  // Recalculer la facture qprès qu'un article soit créé ou modifié
  public function saved(InvoiceItem $item): void
  {
    $this->recalculateInvoice($item);
  }

  // Recalculer la facture après qu'un article soit supprimé
  public function deleted(InvoiceItem $item): void
  {
    $this->recalculateInvoice($item);
  }

  // Recalculer la facture si elle est en statut brouillon
  private function recalculateInvoice(InvoiceItem $item): void
  {
    $invoice = $item->invoice;

    // Ne recalculer que si la facture est éditable
    if ($invoice && $invoice->isEditable()) {
      $invoice->recalculateTotals();
      $invoice->saveQuietly();
    }
  }
}
