import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'
import { useNotification } from '@/Composables/useNotification'
import { useMotivationalQuotes } from './useMotivationalQuotes'


const { getRandomFallbackQuote } = useMotivationalQuotes()
const page = usePage()
const { addNotification } = useNotification()

export const showMotivationalQuote = async () => {
  try {
    const response = await fetch(route('dashboard.getMotivationalQuote', { locale: page.props.locale }))
    const data = await response.json()
    
    if (data[0]?.q) {
      addNotification(data[0].q, 'success')
    } else {
      addNotification(getRandomFallbackQuote() + ' - Questify', 'success')
    }
  } catch (error) {
    addNotification(getRandomFallbackQuote() + ' - Questify', 'success')
  }
}
