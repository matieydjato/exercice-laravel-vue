<script setup>
import { ref, onMounted } from 'vue'
import { InvoiceList, InvoiceForm } from './components'
import { invoiceService } from './services'

const apiResponse = ref(null)
const loading = ref(false)
const showForm = ref(false)
const editingInvoice = ref(null)

// Charger les factures depuis l'API
const loadInvoices = async () => {
  loading.value = true
  try {
    const response = await invoiceService.getAllInvoices()
    apiResponse.value = response
  } catch (error) {
    console.error('Error loading invoices:', error)
    alert('Erreur lors du chargement des factures')
  } finally {
    loading.value = false
  }
}

// Gérer la création d'une nouvelle facture
const handleCreate = () => {
  editingInvoice.value = null
  showForm.value = true
}

// Gérer l'édition d'une facture
const handleEdit = (invoice) => {
  const invoiceItems =
    apiResponse.value.included?.filter(
      (item) =>
        item.type === 'invoice-items' &&
        invoice.relationships?.items?.data?.some((ref) => ref.id === item.id),
    ) || []

  editingInvoice.value = {
    ...invoice,
    items: invoiceItems,
  }

  showForm.value = true
}

// Gérer la sauvegarde d'une facture
const handleSave = async () => {
  showForm.value = false
  editingInvoice.value = null
  await loadInvoices()
}

// Gérer l'annulation de l'édition du formulaire
const handleCancel = () => {
  showForm.value = false
  editingInvoice.value = null
}

// Gérer la suppression d'une facture
const handleDelete = async (id) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) return

  try {
    await invoiceService.deleteInvoice(id)
    await loadInvoices()
  } catch (error) {
    console.error('Error deleting invoice:', error)
    alert('Erreur lors de la suppression')
  }
}

// Gérer l'envoi d'une facture
const handleMarkStatusAsSent = async (id) => {
  try {
    await invoiceService.changeInvoiceStatus(id, 'sent')
    await loadInvoices()
  } catch (error) {
    console.error('Error marking as sent:', error)
    alert("Erreur lors de l'envoi de la facture")
  }
}

// Gérer le paiement d'une facture
const handleMarkStatusAsPaid = async (id) => {
  try {
    await invoiceService.changeInvoiceStatus(id, 'paid')
    await loadInvoices()
  } catch (error) {
    console.error('Error marking as paid:', error)
    alert('Erreur lors du paiement de la facture')
  }
}

onMounted(() => {
  loadInvoices()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-900">📋 Mini-Système de facturation</h1>
          <button
            @click="handleCreate"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition"
          >
            + Nouvelle Facture
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
      <div v-if="loading" class="text-center py-12">
        <div
          class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
        ></div>
        <p class="mt-4 text-gray-600">Chargement...</p>
      </div>

      <InvoiceForm
        v-if="showForm"
        :invoice="editingInvoice"
        @save="handleSave"
        @cancel="handleCancel"
      />

      <InvoiceList
        v-else-if="apiResponse"
        :invoices="apiResponse.data"
        @edit="handleEdit"
        @delete="handleDelete"
        @send="handleMarkStatusAsSent"
        @pay="handleMarkStatusAsPaid"
      />
    </main>
  </div>
</template>
