import '../css/app.css'
import { createApp, h, type DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";

const appName = import.meta.env.VITE_APP_NAME || 'Questify'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: async (name) => await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .component('LanguageSwitcher', LanguageSwitcher)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
