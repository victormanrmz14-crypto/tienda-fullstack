import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import InputField from '@/components/InputField.vue'

describe('InputField', () => {
  it('muestra el label correctamente', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Nombre', name: 'nombre', modelValue: '' }
    })
    expect(wrapper.text()).toContain('Nombre')
  })

  it('muestra el mensaje de error cuando se pasa', () => {
    const wrapper = mount(InputField, {
      props: {
        label: 'Precio',
        name: 'precio',
        modelValue: '',
        error: 'El precio es obligatorio'
      }
    })
    expect(wrapper.text()).toContain('El precio es obligatorio')
  })

  it('no muestra error cuando no se pasa', () => {
    const wrapper = mount(InputField, {
      props: { label: 'Stock', name: 'stock', modelValue: '', error: '' }
    })
    expect(wrapper.find('.error-msg').exists()).toBe(false)
  })

  it('emite update:modelValue al escribir', async () => {
    const wrapper = mount(InputField, {
      props: { label: 'Nombre', name: 'nombre', modelValue: '' }
    })
    await wrapper.find('input').setValue('Laptop')
    expect(wrapper.emitted('update:modelValue')).toBeTruthy()
  })
})
