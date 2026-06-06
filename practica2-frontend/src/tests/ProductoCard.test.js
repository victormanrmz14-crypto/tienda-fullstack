import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import ProductoCard from '@/components/ProductoCard.vue'

describe('ProductoCard', () => {
  const producto = { id: 1, nombre: 'Teclado', precio: 59.99, stock: 5 }

  it('muestra el nombre del producto', () => {
    const wrapper = mount(ProductoCard, { props: { producto } })
    expect(wrapper.text()).toContain('Teclado')
  })

  it('muestra el precio del producto', () => {
    const wrapper = mount(ProductoCard, { props: { producto } })
    expect(wrapper.text()).toContain('59.99')
  })

  it('muestra el stock del producto', () => {
    const wrapper = mount(ProductoCard, { props: { producto } })
    expect(wrapper.text()).toContain('5')
  })

  it('emite agregar-carrito al hacer click en el botón', async () => {
    const wrapper = mount(ProductoCard, { props: { producto } })
    await wrapper.find('[data-test="btn-agregar"]').trigger('click')
    expect(wrapper.emitted('agregar-carrito')).toBeTruthy()
    expect(wrapper.emitted('agregar-carrito')[0]).toEqual([producto])
  })
})
