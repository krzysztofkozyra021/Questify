import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useTranslation() {
  const page = usePage();
  const translations = computed(() => page.props.translations || {});
  const currentLocale = computed(() => page.props.locale || 'en');
  const availableLocales = computed(() => page.props.availableLocales || {'English': 'en', 'Polish': 'pl'});

  function trans(key) {
    return translations.value[key] || key;
  }

  function switchLanguage(locale) {
    window.location.href = `/language/${locale}`;
  }

  return {
    trans,
    currentLocale,
    availableLocales,
    switchLanguage,
  };
}
