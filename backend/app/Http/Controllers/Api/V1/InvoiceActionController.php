<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceActionController extends Controller
{
  /**
   * Créer une facture
   */
  public function storeWithItems(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'data.attributes.customerName' => 'required|string|max:255',
      'data.attributes.currency' => 'sometimes|string|size:3',
      'data.attributes.discountRate' => 'sometimes|numeric|min:0|max:100',
      'data.attributes.issuedAt' => 'required|date',
      'data.attributes.dueAt' => 'required|date|after_or_equal:data.attributes.issuedAt',
      'data.attributes.items' => 'required|array|min:1',
      'data.attributes.items.*.description' => 'required|string|max:255',
      'data.attributes.items.*.quantity' => 'required|integer|min:1',
      'data.attributes.items.*.unitPrice' => 'required|numeric|min:0',
    ]);

    $attrs = $validated['data']['attributes'];

    $invoice = Invoice::create([
      'customer_name' => $attrs['customerName'],
      'currency' => $attrs['currency'] ?? 'EUR',
      'discount_rate' => $attrs['discountRate'] ?? 0,
      'issued_at' => $attrs['issuedAt'],
      'due_at' => $attrs['dueAt'],
    ]);

    foreach ($attrs['items'] as $itemData) {
      $invoice->items()->create([
        'description' => $itemData['description'],
        'quantity' => $itemData['quantity'],
        'unit_price' => $itemData['unitPrice'],
      ]);
    }

    $invoice->refresh();
    $invoice->load('items');

    return $this->formatInvoiceResponse($invoice, 201);
  }

  /**
   * Modifier une facture
   */
  public function updateWithItems(Request $request, Invoice $invoice): JsonResponse
  {
    $invoice->skipObserverRecalculation = true;

    if (!$invoice->isEditable()) {
      return response()->json([
        'errors' => [
          [
            'status' => '422',
            'title' => 'Unprocessable Content',
            'detail' => 'Cannot modify a sent or paid invoice',
          ]
        ]
      ], 422);
    }

    $validated = $request->validate([
      'data.attributes.customerName' => 'sometimes|string|max:255',
      'data.attributes.currency' => 'sometimes|string|size:3',
      'data.attributes.discountRate' => 'sometimes|numeric|min:0|max:100',
      'data.attributes.issuedAt' => 'sometimes|date',
      'data.attributes.dueAt' => 'sometimes|date',
      'data.attributes.items' => 'sometimes|array',
      'data.attributes.items.*.id' => 'sometimes|exists:invoice_items,id',
      'data.attributes.items.*.description' => 'required_without:data.attributes.items.*.id|string|max:255',
      'data.attributes.items.*.quantity' => 'required_without:data.attributes.items.*.id|integer|min:1',
      'data.attributes.items.*.unitPrice' => 'required_without:data.attributes.items.*.id|numeric|min:0',
    ]);

    $attrs = $validated['data']['attributes'];

    $invoice->update([
      'customer_name' => $attrs['customerName'] ?? $invoice->customer_name,
      'currency' => $attrs['currency'] ?? $invoice->currency,
      'discount_rate' => $attrs['discountRate'] ?? $invoice->discount_rate,
      'issued_at' => $attrs['issuedAt'] ?? $invoice->issued_at,
      'due_at' => $attrs['dueAt'] ?? $invoice->due_at,
    ]);

    if (isset($attrs['items'])) {
      $itemIdsToKeep = [];

      foreach ($attrs['items'] as $itemData) {
        if (isset($itemData['id'])) {
          // Modifier un item existant
          $item = $invoice->items()->find($itemData['id']);
          if ($item) {
            $item->update([
              'description' => $itemData['description'] ?? $item->description,
              'quantity' => $itemData['quantity'] ?? $item->quantity,
              'unit_price' => $itemData['unitPrice'] ?? $item->unit_price,
            ]);
            $itemIdsToKeep[] = $item->id;
          }
        } else {
          // Ajouter un nouvel item
          $newItem = $invoice->items()->create([
            'description' => $itemData['description'],
            'quantity' => $itemData['quantity'],
            'unit_price' => $itemData['unitPrice'],
          ]);
          $itemIdsToKeep[] = $newItem->id;
        }
      }

      // Supprimer les items non présents dans la liste
      $invoice->items()->whereNotIn('id', $itemIdsToKeep)->delete();
    }

    $invoice->skipObserverRecalculation = false;
    $invoice->refresh();
    $invoice->load('items');
    $invoice->recalculateTotals();
    $invoice->save();

    return $this->formatInvoiceResponse($invoice);
  }

  /**
   * Formater la réponse au format JSON:API
   */
  private function formatInvoiceResponse(Invoice $invoice, int $status = 200): JsonResponse
  {
    return response()->json([
      'jsonapi' => ['version' => '1.0'],
      'data' => [
        'type' => 'invoices',
        'id' => (string) $invoice->id,
        'attributes' => [
          'customerName' => $invoice->customer_name,
          'currency' => $invoice->currency,
          'status' => $invoice->status,
          'discountRate' => (float) $invoice->discount_rate,
          'subtotal' => (float) $invoice->subtotal,
          'discountAmount' => (float) $invoice->discount_amount,
          'totalDue' => (float) $invoice->total_due,
          'issuedAt' => $invoice->issued_at,
          'dueAt' => $invoice->due_at,
          'createdAt' => $invoice->created_at->toIso8601String(),
          'updatedAt' => $invoice->updated_at->toIso8601String(),
        ],
        'relationships' => [
          'items' => [
            'data' => $invoice->items->map(function ($item) {
              return [
                'type' => 'invoice-items',
                'id' => (string) $item->id,
              ];
            })->toArray(),
          ],
        ],
      ],
      'included' => $invoice->items->map(function ($item) {
        return [
          'type' => 'invoice-items',
          'id' => (string) $item->id,
          'attributes' => [
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unitPrice' => (float) $item->unit_price,
            'lineTotalPrice' => (float) $item->line_total_price,
          ],
        ];
      })->toArray(),
    ], $status);
  }
}
