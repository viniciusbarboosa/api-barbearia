<template>
  <main class="flex-1 p-4 sm:p-8 md:p-12 overflow-y-auto">
    <h1 class="text-3xl sm:text-4xl font-bold font-roboto-slab mb-8 sm:mb-10">Barbeiros</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

      <router-link
        v-for="barber in barbers"
        :key="barber.id"
        :to="`/agendamento/${barber.id}`"
        class="bg-[#3E3B47] rounded-lg p-4 sm:p-6 flex items-center gap-4 sm:gap-5 cursor-pointer hover:ring-2 hover:ring-[#FF9000] transition-all focus:outline-none focus:ring-2 focus:ring-[#FF9000]"
      >
        <img
            class="object-cover w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gray-600 pointer-events-none"
            :src="barber.profile_photo || defaultAvatarUrl"
            :alt="barber.name"
        />
        <div class="flex-1 pointer-events-none">
          <h3 class="text-lg sm:text-xl font-bold text-white">{{ barber.name }}</h3>
          <div class="flex items-start gap-2 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#FF9000] mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-sm text-[#999591] text-left">
              <p>Segunda à Sexta</p>
              <p>8h às 18h</p>
            </div>
          </div>
        </div>
      </router-link>

    </div>

    <div ref="observerElement" class="h-10"></div>

    <div v-if="loading" class="text-center py-4">
      <p class="text-[#FF9000]">Carregando mais barbeiros...</p>
    </div>

    <div
      v-if="showFallbackButton && hasMore && !loading"
      class="text-center py-6 animate-pulse"
    >
      <button
        @click="fetchBarbers"
        class="bg-[#FF9000] text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition flex items-center justify-center gap-2 mx-auto"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
        CARREGAR MAIS BARBEIROS
      </button>
    </div>

    <div v-if="!hasMore && barbers.length > 0" class="text-center py-4">
      <p class="text-[#999591]">Todos os barbeiros foram carregados</p>
    </div>

  </main>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import defaultAvatarUrl from '/resources/images/defaultavatar.jpg';
import api from '../services/api';

const barbers = ref([]);
const page = ref(1);
const loading = ref(false);
const hasMore = ref(true);
const showFallbackButton = ref(false);
const observerElement = ref(null);
let observer = null;
let fallbackTimer = null;

const fetchBarbers = async () => {
  if (loading.value || !hasMore.value) return;

  loading.value = true;
  showFallbackButton.value = false;

  try {
    const response = await api.get(`/barbers/approved?page=${page.value}`);
    console.log('API retornou:', response.data);

    let newBarbers, pagination;

    if (response.data.barbers) {
      newBarbers = response.data.barbers;
      pagination = response.data.pagination;
    } else if (response.data.data && response.data.data.data) {
      newBarbers = response.data.data.data;
      pagination = response.data.data;
    } else {
      console.error('Estrutura desconhecida:', response.data);
      return;
    }

    if (newBarbers && newBarbers.length > 0) {
      barbers.value = [...barbers.value, ...newBarbers];
      page.value++;
    }

    if (pagination) {
      hasMore.value = pagination.current_page < pagination.last_page;
    }

  } catch (error) {
    console.error("Erro ao buscar barbeiros:", error);
  } finally {
    loading.value = false;

    if (hasMore.value) {
      startFallbackTimer();
    }
  }
};

const startFallbackTimer = () => {
  clearTimeout(fallbackTimer);
  fallbackTimer = setTimeout(() => {
    if (hasMore.value && !loading.value) {
      console.log('⏰ Fallback: Mostrando botão');
      showFallbackButton.value = true;
    }
  }, 2000);
};

onMounted(() => {
  fetchBarbers();

  observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting && !loading.value && hasMore.value) {
        console.log('🎯 Observer: Carregando automaticamente');
        showFallbackButton.value = false;
        clearTimeout(fallbackTimer);
        fetchBarbers();
      }
    },
    { threshold: 0.1 }
  );

  if (observerElement.value) {
    observer.observe(observerElement.value);
  }
});

onUnmounted(() => {
  if (observer) observer.disconnect();
  clearTimeout(fallbackTimer);
});
</script>
