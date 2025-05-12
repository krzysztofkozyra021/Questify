import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useTranslation() {
  const page = usePage()

  const translations = computed(() => page.props.translations || {})
  const currentLocale = computed(() => page.props.locale || 'en')
  const availableLocales = computed(() => page.props.availableLocales || {'English': 'en', 'Polish': 'pl'})

  function trans(key) {
    if (!translations.value || Object.keys(translations.value).length === 0) {
      console.warn(`Translations not found for key: ${key}`)
    }
    return translations.value[key] || key
  }

  function switchLanguage(locale) {
    router.get(route('language.switch', { locale: locale }), {
      preserveScroll: true,
    })
  }

  return {
    trans,
    currentLocale,
    availableLocales,
    switchLanguage,
  }
}
