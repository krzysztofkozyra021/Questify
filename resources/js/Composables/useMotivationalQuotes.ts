import { useTranslation } from '@/Composables/useTranslation';

interface MotivationalQuotes {
    getRandomFallbackQuote: () => string;
}

export function useMotivationalQuotes(): MotivationalQuotes {
    const { trans } = useTranslation();

    const fallbackQuotes: string[] = [
        "Every day is a new beginning.",
        "Stay focused, stay motivated.",
        "Small steps lead to big achievements.",
        "Your potential is limitless.",
        "Make today amazing!",
        "Believe in yourself.",
        "Success is a journey, not a destination.",
        "Keep pushing forward!",
        "You are stronger than you think.",
        "Make it happen!"
    ];

    const getRandomFallbackQuote = (): string => {
        const randomIndex: number = Math.floor(Math.random() * fallbackQuotes.length);
        return trans(fallbackQuotes[randomIndex]);
    }

    return {
        getRandomFallbackQuote
    };
} 