<script setup>
import { useTranslation } from '@/Composables/useTranslation'
import { usePage } from '@inertiajs/vue3'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'

const { trans } = useTranslation()
const page = usePage()

const userStats = page.props.userStatistics
const userClassExpMultiplier = page.props.userClassExpMultiplier
</script>

<template>
  <div class="flex flex-col h-screen">
    <Header :showPlayerPanel="false" />
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h1 class="text-3xl font-bold mb-8 text-center">O Questify</h1>
          
          <div class="space-y-8">
            <!-- Misja -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">Nasza Misja</h2>
              <p class="text-gray-600">
                Questify to innowacyjne narzędzie do zarządzania zadaniami, które łączy w sobie elementy grywalizacji z efektywnym zarządzaniem czasem. 
                Naszym celem jest pomaganie użytkownikom w osiąganiu ich celów poprzez przekształcenie codziennych zadań w ekscytującą przygodę.
              </p>
            </section>

            <!-- Historia -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">Historia</h2>
              <p class="text-gray-600">
                Questify narodził się z pasji do efektywnego zarządzania czasem i miłości do gier. 
                Zauważyliśmy, że tradycyjne metody zarządzania zadaniami często są nudne i nie motywują do działania. 
                Postanowiliśmy to zmienić, tworząc platformę, która sprawia, że realizacja celów staje się przyjemnością.
              </p>
            </section>

            <!-- Wartości -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">Nasze Wartości</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-xl font-medium mb-2">Motywacja</h3>
                  <p class="text-gray-600">
                    Wierzymy w siłę pozytywnej motywacji i gamifikacji w osiąganiu celów.
                  </p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-xl font-medium mb-2">Rozwój</h3>
                  <p class="text-gray-600">
                    Wspieramy ciągły rozwój osobisty poprzez systematyczne osiąganie małych celów.
                  </p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                  <h3 class="text-xl font-medium mb-2">Współpraca</h3>
                  <p class="text-gray-600">
                    Promujemy współpracę i wsparcie społeczności w dążeniu do wspólnych celów.
                  </p>
                </div>
              </div>
            </section>

            <!-- Zespół -->
            <section>
              <h2 class="text-2xl font-semibold mb-4">Nasz Zespół</h2>
              <p class="text-gray-600 mb-4">
                Tworzymy zespół pasjonatów, którzy wierzą w siłę gamifikacji i efektywnego zarządzania czasem. 
                Każdy z nas wnosi unikalne doświadczenie i perspektywę, tworząc razem platformę, która pomaga ludziom osiągać ich cele.
              </p>
            </section>

            <!-- Dołącz do nas -->
            <section class="text-center">
              <h2 class="text-2xl font-semibold mb-4">Dołącz do Nas</h2>
              <p class="text-gray-600 mb-6">
                Rozpocznij swoją przygodę z Questify już dziś i odkryj, jak przyjemne może być osiąganie celów!
              </p>
              <a 
                href="/register" 
                class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
              >
                Rozpocznij Teraz
              </a>
            </section>
          </div>
        </div>
      </div>
    </div>
    <Footer />
  </div>
</template>
