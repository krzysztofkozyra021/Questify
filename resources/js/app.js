import '../css/app.css'
import './bootstrap.js'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from 'ziggy-js'
import LanguageSwitcher from './Components/LanguageSwitcher.vue'
import { createHead } from '@vueuse/head'
import AppLayout from './Layouts/AppLayout.vue'

const appName = import.meta.env.VITE_APP_NAME || 'Questify'
const head = createHead()

createInertiaApp({
  title: (title) => `${title} | ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(head)
      .component('LanguageSwitcher', LanguageSwitcher)
      .component('AppLayout', AppLayout)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
