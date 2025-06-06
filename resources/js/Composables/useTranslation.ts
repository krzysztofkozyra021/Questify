import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

type Translations = Record<string, string>;

type AvailableLocales = Record<string, string>;

interface PageProps {
  translations?: Translations
  locale?: string
  availableLocales?: AvailableLocales
}

export function useTranslation() {
  const page = usePage<{ props: PageProps }>()

  const translations = computed(() => page.props.translations || {})
  const currentLocale = computed(() => page.props.locale || 'en')
  const availableLocales = computed(() => page.props.availableLocales || { 'English': 'en', 'Polish': 'pl' })

  function trans(key: string): string {
    if (!translations.value || Object.keys(translations.value).length === 0) {
      console.warn(`Translations not found for key: ${key}`)
    }
    return (translations.value as Translations)[key] || key
  }

  function switchLanguage(locale: string): void {
    router.get(route('language.switch', { locale }), {
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
