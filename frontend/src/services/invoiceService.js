import api from './api'

export const invoiceService = {
  async getAllInvoices() {
    const response = await api.get('/invoices?include=items')
    return response.data
  },

  async createInvoice(invoiceData) {
    const response = await api.post('/invoices/with-items', {
      data: {
        attributes: invoiceData,
      },
    })
    return response.data
  },

  async updateInvoice(id, invoiceData) {
    const response = await api.put(`/invoices/${id}/with-items`, {
      data: {
        attributes: invoiceData,
      },
    })
    return response.data
  },

  async deleteInvoice(id) {
    await api.delete(`/invoices/${id}`)
  },

  async changeInvoiceStatus(id, newStatus) {
    const response = await api.patch(`/invoices/${id}`, {
      data: {
        type: 'invoices',
        id: String(id),
        attributes: {
          status: newStatus,
        },
      },
    })

    return response.data
  },
}
