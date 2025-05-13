import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface QuoteResponse {
  q: string;
  a: string;
}

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

function getMotivationalQuote(): Promise<string> {
  // This function fetches a motivational quote from the backend and returns the quote text.
  return fetch(`/dashboard/motivational-quote/${currentLocale.value}`)
    .then((response: Response) => response.json())
    .then((data: QuoteResponse[]) => {
      if (Array.isArray(data) && data.length > 0 && data[0].q) {
        return `${data[0].q} — ${data[0].a}`;
      }
      return "Stay motivated!";
    })
    .catch(() => "Stay motivated!");
}

export { getMotivationalQuote }; 