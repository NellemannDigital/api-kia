import React from 'react'
import { createRoot } from 'react-dom/client'
import CarList, { Car } from './CarList'

class CarListElement extends HTMLElement {
  async connectedCallback() {
    const carIdAttr = this.getAttribute('car-id')
    let url = '/api/tools/cars'

    if (carIdAttr) {
      const ids = carIdAttr.split(',').map(id => id.trim())
      url += '?ids=' + ids.join(',')
    }

    try {
      const res = await fetch(url)
      const cars: Car[] = await res.json()

      const rootEl = document.createElement('div')
      this.attachShadow({ mode: 'open' }).appendChild(rootEl)

      createRoot(rootEl).render(<CarList cars={cars} />)
    } catch (error) {
      console.error('Failed to load cars:', error)
      throw error // Vite overlay fanger fejl i dev
    }
  }
}

customElements.define('car-list', CarListElement)
