

import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

function getMotivationalQuote() {
  // This function fetches a motivational quote from the backend and returns the quote text.
  return fetch(`/dashboard/motivational-quote/${currentLocale.value}`)
    .then(response => response.json())
    .then(data => {
      if (Array.isArray(data) && data.length > 0 && data[0].q) {
        return `${data[0].q} — ${data[0].a}`;
      }
      return "Stay motivated!";
    })
    .catch(() => "Stay motivated!");
}

export { getMotivationalQuote };