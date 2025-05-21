<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">Polityka Prywatności</h1>
      
      <div class="bg-white rounded-lg shadow-md p-6 space-y-8">
        <section>
          <h2 class="text-2xl font-semibold mb-4">1. Informacje ogólne</h2>
          <div class="prose prose-stone max-w-none">
            <p>Niniejsza polityka prywatności określa zasady przetwarzania i ochrony danych osobowych w serwisie Questify.</p>
            <p>Administratorem danych osobowych jest Questify.</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">2. Zakres przetwarzanych danych</h2>
          <div class="prose prose-stone max-w-none">
            <p>Przetwarzamy następujące dane osobowe:</p>
            <ul>
              <li>Adres email</li>
              <li>Imię i nazwisko</li>
              <li>Dane logowania</li>
              <li>Dane dotyczące aktywności w serwisie</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">3. Cel przetwarzania danych</h2>
          <div class="prose prose-stone max-w-none">
            <p>Dane osobowe są przetwarzane w celu:</p>
            <ul>
              <li>Świadczenia usług przez serwis</li>
              <li>Komunikacji z użytkownikami</li>
              <li>Analizy i poprawy działania serwisu</li>
              <li>Zapewnienia bezpieczeństwa</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">4. Podstawa prawna przetwarzania</h2>
          <div class="prose prose-stone max-w-none">
            <p>Przetwarzanie danych odbywa się na podstawie:</p>
            <ul>
              <li>Zgody użytkownika</li>
              <li>Wykonania umowy</li>
              <li>Prawnie uzasadnionych interesów administratora</li>
              <li>Obowiązku prawnego</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">5. Okres przechowywania danych</h2>
          <div class="prose prose-stone max-w-none">
            <p>Dane osobowe są przechowywane przez okres:</p>
            <ul>
              <li>Niezbędny do realizacji celów</li>
              <li>Wymagany przez przepisy prawa</li>
              <li>Do momentu wycofania zgody</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">6. Prawa użytkownika</h2>
          <div class="prose prose-stone max-w-none">
            <p>Użytkownik ma prawo do:</p>
            <ul>
              <li>Dostępu do swoich danych</li>
              <li>Sprostowania danych</li>
              <li>Usunięcia danych</li>
              <li>Ograniczenia przetwarzania</li>
              <li>Przenoszenia danych</li>
              <li>Sprzeciwu wobec przetwarzania</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">7. Bezpieczeństwo danych</h2>
          <div class="prose prose-stone max-w-none">
            <p>Zapewniamy bezpieczeństwo danych poprzez:</p>
            <ul>
              <li>Szyfrowanie transmisji</li>
              <li>Regularne kopie zapasowe</li>
              <li>Kontrolę dostępu</li>
              <li>Monitorowanie systemu</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">8. Pliki cookies</h2>
          <div class="prose prose-stone max-w-none">
            <p>Serwis wykorzystuje pliki cookies do:</p>
            <ul>
              <li>Utrzymania sesji użytkownika</li>
              <li>Zapamiętywania preferencji</li>
              <li>Analizy ruchu</li>
              <li>Personalizacji treści</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">9. Kontakt</h2>
          <div class="prose prose-stone max-w-none">
            <p>W sprawach związanych z ochroną danych można kontaktować się:</p>
            <ul>
              <li>Email: privacy@questify.com</li>
              <li>Telefon: +48 123 456 789</li>
              <li>Adres: ul. Przykładowa 123, 00-000 Warszawa</li>
            </ul>
          </div>
        </section>
      </div>
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { useTranslation } from '@/Composables/useTranslation'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'

const { trans } = useTranslation()
</script>
