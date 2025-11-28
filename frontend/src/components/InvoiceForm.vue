<script setup>
import { ref, computed, onMounted } from 'vue'
import { invoiceService } from '../services'
import { formatCurrency, formatDateForInput } from '../utils/formatters'
import {
  calculateSubtotal,
  calculateDiscount,
  calculateTotal,
  calculateLineTotal,
} from '../utils/helpers'

const props = defineProps({
  invoice: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['save', 'cancel'])

const form = ref({
  customerName: '',
  currency: 'EUR',
  discountRate: 0,
  issuedAt: new Date().toISOString().split('T')[0],
  dueAt: '',
  items: [{ description: '', quantity: 1, unitPrice: 0 }],
})

const loading = ref(false)

onMounted(() => {
  // Charger les données de la facture en mode édition
  if (props.invoice) {
    const attrs = props.invoice.attributes

    // Extraire et formater les items depuis les données JSON:API
    let items = []
    if (props.invoice.items && props.invoice.items.length > 0) {
      items = props.invoice.items.map((item) => ({
        id: item.id,
        description: item.attributes.description,
        quantity: item.attributes.quantity,
        unitPrice: item.attributes.unitPrice,
      }))
    }

    if (items.length === 0) {
      items = [{ description: '', quantity: 1, unitPrice: 0 }]
    }

    form.value = {
      customerName: attrs.customerName,
      currency: attrs.currency,
      discountRate: attrs.discountRate,
      issuedAt: formatDateForInput(attrs.issuedAt),
      dueAt: formatDateForInput(attrs.dueAt),
      items: items,
    }
  }
})

// Ajouter un article
const addItem = () => {
  form.value.items.push({
    description: '',
    quantity: 1,
    unitPrice: 0,
  })
}

// Supprimer un article
const removeItem = (index) => {
  if (form.value.items.length > 1) {
    form.value.items.splice(index, 1)
  }
}

// Calculs du total avant remise
const subtotal = computed(() => {
  return calculateSubtotal(form.value.items)
})

// Calcul de la remise
const discountAmount = computed(() => {
  return calculateDiscount(subtotal.value, form.value.discountRate)
})

// Calcul du total final après remise
const total = computed(() => {
  return calculateTotal(subtotal.value, discountAmount.value)
})

// Formater un montant avec la devise sélectionnée
const formatAmount = (amount) => {
  return formatCurrency(amount, form.value.currency)
}

// Soumettre le formulaire
const handleSubmit = async () => {
  loading.value = true
  try {
    if (props.invoice) {
      await invoiceService.updateInvoice(props.invoice.id, form.value)
    } else {
      await invoiceService.createInvoice(form.value)
    }
    emit('save')
  } catch (error) {
    console.error('Error saving invoice:', error)
    alert('Erreur lors de la sauvegarde')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">
      {{ invoice ? '✏️ Modifier la facture' : '➕ Nouvelle facture' }}
    </h2>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Informations générales -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Client -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Nom du client <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.customerName"
            type="text"
            required
            placeholder="Acme Corp"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Devise -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1"> Devise </label>
          <select
            v-model="form.currency"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="EUR">EUR (€)</option>
          </select>
        </div>

        <!-- Date d'émission -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Date d'émission <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.issuedAt"
            type="date"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Date d'échéance -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Date d'échéance <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.dueAt"
            type="date"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Remise -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1"> Remise (%) </label>
          <input
            v-model.number="form.discountRate"
            type="number"
            min="0"
            max="100"
            step="0.01"
            placeholder="0"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      <!-- Articles -->
      <div>
        <div class="flex justify-between items-center mb-3">
          <h3 class="text-lg font-semibold text-gray-800">📦 Articles</h3>
          <button
            type="button"
            @click="addItem"
            class="text-blue-600 hover:text-blue-800 font-medium transition"
          >
            + Ajouter un article
          </button>
        </div>

        <div class="space-y-3">
          <div v-for="(item, index) in form.items" :key="index" class="flex gap-3 items-start">
            <!-- Description -->
            <div class="flex-1">
              <input
                v-model="item.description"
                type="text"
                placeholder="Description de l'article"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <!-- Quantité -->
            <div class="w-24">
              <input
                v-model.number="item.quantity"
                type="number"
                placeholder="Qté"
                min="1"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <!-- Prix unitaire -->
            <div class="w-32">
              <input
                v-model.number="item.unitPrice"
                type="number"
                placeholder="Prix"
                min="0"
                step="0.01"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <!-- Total de la ligne -->
            <div class="w-32 px-3 py-2 bg-gray-50 rounded-md text-right font-medium text-gray-700">
              {{ formatAmount(calculateLineTotal(item)) }}
            </div>

            <!-- Supprimer un article -->
            <button
              v-if="form.items.length > 1"
              type="button"
              @click="removeItem(index)"
              class="px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition"
              title="Supprimer cet article"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>

      <!-- Totaux -->
      <div class="bg-gray-50 p-4 rounded-lg space-y-2 border border-gray-200">
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Sous-total:</span>
          <span class="font-medium text-gray-900">{{ formatAmount(subtotal) }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Remise ({{ form.discountRate }}%):</span>
          <span class="font-medium text-red-600">-{{ formatAmount(discountAmount) }}</span>
        </div>
        <div class="flex justify-between text-lg font-bold border-t pt-2 border-gray-300">
          <span class="text-gray-900">Total:</span>
          <span class="text-blue-600">{{ formatAmount(total) }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3 pt-4 border-t">
        <button
          type="button"
          @click="emit('cancel')"
          class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition font-medium"
        >
          Annuler
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition font-medium"
        >
          {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </div>
    </form>
  </div>
</template>
