/**
 * Formate une date au format local français
 * @param {string} dateString - Date au format ISO (YYYY-MM-DD)
 * @returns {string} Date formatée (ex: "15/01/2024")
 */
export function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('fr-FR')
}

/**
 * Formate un montant en devise
 * @param {number} amount - Montant à formater
 * @param {string} currency - Code devise (EUR, USD, GBP)
 * @returns {string} Montant formaté (ex: "1 540,00 €")
 */
export function formatCurrency(amount, currency = 'EUR') {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency,
  }).format(amount)
}

/**
 * Convertit une date ISO complète en format YYYY-MM-DD
 * @param {string} dateString - Date ISO complète
 * @returns {string} Date au format YYYY-MM-DD
 */
export function formatDateForInput(dateString) {
  if (!dateString) return ''

  // Si format ISO complet : "2024-01-15T00:00:00Z"
  if (dateString.includes('T')) {
    return dateString.split('T')[0]
  }

  // Si déjà format YYYY-MM-DD
  return dateString
}
