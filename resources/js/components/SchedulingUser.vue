<template>
  <div class="min-h-screen bg-[#28262E] text-white p-4 sm:p-8">

    <div v-if="isLoading" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-[#FF9000]"></div>
    </div>

    <div class="flex items-center gap-4 sm:gap-6 mb-8">
      <router-link to="/" class="text-[#999591] hover:text-[#FF9000] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </router-link>

      <img
        v-if="barber"
        class="object-cover w-16 h-16 rounded-full bg-gray-600"
        :src="barber.profile_photo || defaultAvatarUrl"
        :alt="barber.name"
      />
      <h1 class="text-2xl sm:text-3xl font-bold text-[#F4EDE8]">
        Agendar com {{ barber?.name || 'Carregando...' }}
      </h1>
    </div>

    <div class="flex flex-col md:flex-row gap-8">

      <div class="md:w-full lg:w-1/3">
        <VDatePicker
          v-model="selectedDate"
          is-dark
          color="orange"
          :min-date="new Date()"
          :attributes="[{ key: 'today', dot: true, dates: new Date() }]"
          title-position="left"
          class="custom-calendar"
        />
      </div>

      <div class="md:w-full lg:w-2/3">
        <h2 class="text-xl font-semibold text-[#FF9000] mb-4">
          Horários para {{ formattedDate }}
        </h2>

        <div v-if="!isLoading && schedules.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
          <button
            v-for="schedule in schedules"
            :key="schedule.id"
            :disabled="!schedule.available"
            @click="selectTime(schedule)"
            :class="[
              'p-3 rounded-lg text-center font-bold transition-colors',
              schedule.available
                ? 'bg-[#3E3B47] hover:bg-[#FF9000] hover:text-[#312E38]'
                : 'bg-[#212026] text-[#666360] cursor-not-allowed line-through'
            ]"
          >
            {{ schedule.start_time.substring(0, 5) }}
          </button>
        </div>

        <div v-else-if="!isLoading && schedules.length === 0" class="text-center bg-[#3E3B47] p-8 rounded-lg">
          <p class="text-[#999591]">Nenhum horário disponível para esta data.</p>
        </div>
      </div>
    </div>

  </div>
</template>

<style>
.custom-calendar .vc-pane-layout { background-color: #3E3B47; border-radius: 10px; }
.custom-calendar .vc-header { padding: 20px 20px 15px 20px; margin-bottom: 10px; }
.custom-calendar .vc-title { color: #FF9000; font-weight: 700; font-size: 1.1rem; }
.custom-calendar .vc-arrow { color: #FF9000; transform: scale(1.2); }
.custom-calendar .vc-weekday { color: #999591; padding-bottom: 10px; }
.custom-calendar .vc-day-content { color: #F4EDE8; border-radius: 9999px; width: 40px; height: 40px; }
.custom-calendar .vc-day-content:hover { background-color: rgba(255, 144, 0, 0.2); }
.custom-calendar .vc-day-content.is-disabled { color: #666360; }
</style>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { DatePicker as VDatePicker } from 'v-calendar';
import 'v-calendar/dist/style.css';
import api from '../services/api';
import defaultAvatarUrl from '/resources/images/defaultavatar.jpg';

const route = useRoute();
const router = useRouter();
const barberId = route.params.barberId;

const barber = ref(null);
const schedules = ref([]);
const selectedDate = ref(new Date());
const isLoading = ref(true);

const formatDateForAPI = (date) => date.toISOString().split('T')[0];

const formattedDate = computed(() => {
  return selectedDate.value.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long' });
});

async function fetchBarberDetails() {
  try {
    const response = await api.get(`/barbers/${barberId}`);
    barber.value = response.data;
  } catch (error) {
    router.push({ name: 'Home' });
  }
}

async function fetchSchedules(date) {
  isLoading.value = true;
  schedules.value = [];
  try {
    const response = await api.get(`/barbers/${barberId}/schedules`, {
      params: { date: formatDateForAPI(date) }
    });
    schedules.value = response.data || [];
  } catch (error) {
    console.error('Erro ao buscar horários:', error);
    schedules.value = [];
  } finally {
    isLoading.value = false;
  }
}

function selectTime(schedule) {
  if (!barber.value) return;

  const confirmation = confirm(
    `Deseja confirmar o agendamento com ${barber.value.name} no dia ${formattedDate.value} às ${schedule.start_time.substring(0, 5)}?`
  );

  if (confirmation) {
    alert('Agendamento realizado com sucesso! (implementação pendente)');
  }
}

onMounted(async () => {
  await fetchBarberDetails();

  if (barber.value) {
    await fetchSchedules(selectedDate.value);
  } else {
    isLoading.value = false;
  }
});

watch(selectedDate, (newDate) => {
  if (barber.value) {
    fetchSchedules(newDate);
  }
});
</script>
