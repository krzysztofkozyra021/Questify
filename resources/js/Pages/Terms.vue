<template>
  <div class="flex flex-col min-h-screen">
    <Header :showPlayerPanel="false" />
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-8">Regulamin</h1>
      
      <div class="bg-white rounded-lg shadow-md p-6 space-y-8">
        <section>
          <h2 class="text-2xl font-semibold mb-4">1. Postanowienia ogólne</h2>
          <div class="prose prose-stone max-w-none">
            <p>Niniejszy regulamin określa zasady korzystania z serwisu Questify oraz prawa i obowiązki użytkowników.</p>
            <p>Serwis Questify jest platformą do zarządzania zadaniami i śledzenia postępów w grach.</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">2. Definicje</h2>
          <div class="prose prose-stone max-w-none">
            <ul>
              <li>Serwis - platforma Questify dostępna pod adresem questify.com</li>
              <li>Użytkownik - osoba korzystająca z Serwisu</li>
              <li>Konto - indywidualne konto użytkownika w Serwisie</li>
              <li>Zadanie - pojedyncze zadanie lub quest dodane przez użytkownika</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">3. Rejestracja i konto</h2>
          <div class="prose prose-stone max-w-none">
            <p>Warunki rejestracji konta:</p>
            <ul>
              <li>Ukończone 13 lat</li>
              <li>Podanie prawdziwych danych</li>
              <li>Akceptacja regulaminu</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">4. Zasady korzystania z Serwisu</h2>
          <div class="prose prose-stone max-w-none">
            <p>Zabrania się:</p>
            <ul>
              <li>Publikowania treści niezgodnych z prawem</li>
              <li>Naruszań praw autorskich</li>
              <li>Spamu i niechcianych wiadomości</li>
              <li>Działań szkodliwych dla innych użytkowników</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">5. Ochrona danych osobowych</h2>
          <div class="prose prose-stone max-w-none">
            <p>Administratorem danych osobowych jest Questify.</p>
            <p>Szczegółowe informacje o przetwarzaniu danych znajdują się w Polityce Prywatności.</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">6. Odpowiedzialność</h2>
          <div class="prose prose-stone max-w-none">
            <p>Serwis nie ponosi odpowiedzialności za:</p>
            <ul>
              <li>Treści dodawane przez użytkowników</li>
              <li>Działania innych użytkowników</li>
              <li>Utratę danych w wyniku awarii</li>
            </ul>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">7. Zmiany regulaminu</h2>
          <div class="prose prose-stone max-w-none">
            <p>Serwis zastrzega sobie prawo do zmiany regulaminu w każdym czasie.</p>
            <p>O zmianach użytkownicy będą informowani z wyprzedzeniem.</p>
          </div>
        </section>

        <section>
          <h2 class="text-2xl font-semibold mb-4">8. Postanowienia końcowe</h2>
          <div class="prose prose-stone max-w-none">
            <p>Regulamin wchodzi w życie z dniem publikacji.</p>
            <p>W sprawach nieuregulowanych zastosowanie mają przepisy prawa polskiego.</p>
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
