<script setup>
import { formatDate, formatCurrency } from '../utils/formatters'
import {
  getStatusBadgeClass,
  getStatusLabel,
  canEditInvoice,
  canMarkAsSent,
  canMarkAsPaid,
} from '../utils/helpers'

defineProps({
  invoices: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['edit', 'delete', 'send', 'pay'])
</script>

<template>
  <div>
    <!-- Afficher si aucune facture -->
    <div v-if="invoices.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
      <div class="text-6xl mb-4">📄</div>
      <p class="text-gray-500 text-lg">Aucune facture</p>
      <p class="text-gray-400 mt-2">Créez votre première facture !</p>
    </div>

    <!-- Liste des factures -->
    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Client
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Montant
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Remise (%)
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Crée le
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Statut
            </th>
            <th
              class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="invoice in invoices" :key="invoice.id" class="hover:bg-gray-50 transition">
            <!-- Client -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
                {{ invoice.attributes.customerName }}
              </div>
            </td>

            <!-- Montant -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">
                {{ formatCurrency(invoice.attributes.totalDue) }}
              </div>
            </td>

            <!-- Montant -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">
                {{ invoice.attributes.discountRate }}
              </div>
            </td>

            <!-- Date de création -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(invoice.attributes.issuedAt) }}
            </td>

            <!-- Statut -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                :class="getStatusBadgeClass(invoice.attributes.status)"
                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ getStatusLabel(invoice.attributes.status) }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <!-- Modifier -->
              <button
                v-if="canEditInvoice(invoice)"
                @click="emit('edit', invoice)"
                class="text-blue-600 hover:text-blue-900 transition"
                title="Modifier"
              >
                ✏️ Modifier
              </button>

              <!-- Marquer comme envoyée -->
              <button
                v-if="canMarkAsSent(invoice)"
                @click="emit('send', invoice.id)"
                class="text-green-600 hover:text-green-900 transition"
                title="Marquer comme envoyée"
              >
                📤 Envoyer
              </button>

              <!-- Marquer comme payée -->
              <button
                v-if="canMarkAsPaid(invoice)"
                @click="emit('pay', invoice.id)"
                class="text-purple-600 hover:text-purple-900 transition"
                title="Marquer comme payée"
              >
                💰 Payer
              </button>

              <!-- Supprimer -->
              <button
                @click="emit('delete', invoice.id)"
                class="text-red-600 hover:text-red-900 transition"
                title="Supprimer"
              >
                🗑️ Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
