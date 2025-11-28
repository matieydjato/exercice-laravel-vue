/**
 * Retourne la classe CSS Tailwind pour le badge de statut
 * @param {string} status - Statut de la facture (draft, sent, paid)
 * @returns {string} Classes CSS
 */
export function getStatusBadgeClass(status) {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    sent: 'bg-blue-100 text-blue-800',
    paid: 'bg-green-100 text-green-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

/**
 * Retourne le label français du statut
 * @param {string} status - Statut de la facture
 * @returns {string} Label traduit
 */
export function getStatusLabel(status) {
  const labels = {
    draft: 'Brouillon',
    sent: 'Envoyée',
    paid: 'Payée',
  }
  return labels[status] || status
}

/**
 * Vérifie si une facture peut être modifiée
 * @param {Object} invoice - Objet facture
 * @returns {boolean}
 */
export function canEditInvoice(invoice) {
  return invoice?.attributes?.status === 'draft'
}

/**
 * Vérifie si une facture peut être marquée comme envoyée
 * @param {Object} invoice - Objet facture
 * @returns {boolean}
 */
export function canMarkAsSent(invoice) {
  return invoice?.attributes?.status === 'draft'
}

/**
 * Vérifie si une facture peut être marquée comme payée
 * @param {Object} invoice - Objet facture
 * @returns {boolean}
 */
export function canMarkAsPaid(invoice) {
  return invoice?.attributes?.status === 'sent'
}

/**
 * Calcule le sous-total d'une liste d'items
 * @param {Array} items - Liste des items
 * @returns {number} Sous-total
 */
export function calculateSubtotal(items) {
  return items.reduce((sum, item) => {
    return sum + item.quantity * item.unitPrice
  }, 0)
}

/**
 * Calcule le montant de la remise
 * @param {number} subtotal - Sous-total
 * @param {number} discountRate - Taux de remise en %
 * @returns {number} Montant de la remise
 */
export function calculateDiscount(subtotal, discountRate) {
  return subtotal * (discountRate / 100)
}

/**
 * Calcule le total à payer
 * @param {number} subtotal - Sous-total
 * @param {number} discountAmount - Montant de la remise
 * @returns {number} Total à payer
 */
export function calculateTotal(subtotal, discountAmount) {
  return subtotal - discountAmount
}

/**
 * Calcule le total d'une ligne d'item
 * @param {Object} item - Item avec quantity et unitPrice
 * @returns {number} Total de la ligne
 */
export function calculateLineTotal(item) {
  return item.quantity * item.unitPrice
}
