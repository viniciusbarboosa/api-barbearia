<template>
  <div class="min-h-screen bg-[#28262E] text-white p-4 sm:p-8">

    <div v-if="isLoading" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-[#FF9000]"></div>
    </div>

    <div class="flex items-center gap-4 mb-8">
        <router-link to="/" class="text-[#999591] hover:text-[#FF9000] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        </router-link>
        <h1 class="text-3xl font-bold text-[#F4EDE8]">Gerenciar Horários</h1>
    </div>

    <div class="flex flex-col md:flex-row gap-8">

      <div class="md:w-1/2 lg:w-1/3">
        <VDatePicker
          v-model="selectedDate"
          is-dark
          color="orange"
          :attributes="[{ key: 'today', dot: true, dates: new Date() }]"
          title-position="left"
          class="custom-calendar"
        />
      </div>


      <div class="md:w-1/2 lg:w-2/3">
        <h2 class="text-xl font-semibold text-[#FF9000] mb-4">
          Horários para {{ formattedDate }}
        </h2>


        <div v-if="schedules.length > 0" class="space-y-3 max-h-[60vh] overflow-y-auto pr-2">
          <div v-for="schedule in schedules" :key="schedule.id" class="flex justify-between items-center bg-[#3E3B47] p-4 rounded-lg">
            <span class="text-lg font-medium text-[#F4EDE8]">{{ schedule.start_time.substring(0, 5) }}</span>
            <button
              @click="toggleAvailability(schedule)"
              :class="[
                'px-4 py-2 rounded-md font-bold text-sm transition-colors',
                schedule.available ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'
              ]"
            >
              {{ schedule.available ? 'Disponível' : 'Indisponível' }}
            </button>
          </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center bg-[#3E3B47] p-8 rounded-lg text-center">
          <p class="text-[#999591] mb-4">Nenhum horário cadastrado para esta data.</p>
          <button @click="isModalOpen = true" class="w-full max-w-xs py-3 font-medium text-[#312E38] bg-[#FF9000] rounded-lg hover:bg-opacity-90">
            Gerar Grade de Horários
          </button>
        </div>
      </div>
    </div>

    <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-[#3E3B47] p-8 rounded-xl w-full max-w-md border-2 border-[#FF9000]">
        <h3 class="text-2xl font-bold mb-6 text-center text-[#F4EDE8]">Gerar Horários para {{ formattedDate }}</h3>
        <form @submit.prevent="createBatchSchedules" class="space-y-4">
          <div>
            <label for="start_time" class="block mb-2 text-sm font-medium text-[#999591]">Horário de Início do Expediente</label>
            <input v-model="batchForm.start_time" type="time" id="start_time" class="w-full p-3 text-[#F4EDE8] bg-[#28262E] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required>
          </div>
          <div>
            <label for="end_time" class="block mb-2 text-sm font-medium text-[#999591]">Horário de Fim do Expediente</label>
            <input v-model="batchForm.end_time" type="time" id="end_time" class="w-full p-3 text-[#F4EDE8] bg-[#28262E] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required>
          </div>
          <div class="flex justify-between gap-4 pt-4">
            <button @click="isModalOpen = false" type="button" class="w-full py-3 font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">
              Cancelar
            </button>
            <button type="submit" class="w-full py-3 font-medium text-[#312E38] bg-[#FF9000] rounded-lg hover:bg-opacity-90">
              Gerar
            </button>
          </div>
        </form>
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
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { DatePicker as VDatePicker } from 'v-calendar';
import 'v-calendar/dist/style.css';
import api from '../services/api';

const schedules = ref([]);
const selectedDate = ref(new Date());
const isLoading = ref(false);
const isModalOpen = ref(false);

const batchForm = reactive({
  start_time: '09:00',
  end_time: '18:00',
});

const formatDateForAPI = (date) => date.toISOString().split('T')[0];

const formattedDate = computed(() => {
  return selectedDate.value.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long' });
});

async function fetchSchedules(date) {
  isLoading.value = true;
  schedules.value = [];
  try {
    const response = await api.get('/schedules', {
      params: { date: formatDateForAPI(date) }
    });

    schedules.value = response.data || [];
  } catch (error) {
    if (error.response && error.response.status === 404) {
      schedules.value = [];
    } else {
      console.error('Erro ao buscar horários:', error);
      alert('Não foi possível carregar os horários.');
    }
  } finally {
    isLoading.value = false;
  }
}

async function createBatchSchedules() {
  isLoading.value = true;
  try {

    await api.post('/schedules/batch-create', {
      date: formatDateForAPI(selectedDate.value),
      start_time: batchForm.start_time,
      end_time: batchForm.end_time
    });
    isModalOpen.value = false;
    await fetchSchedules(selectedDate.value);
    alert('Horários gerados com sucesso!');
  } catch (error) {
    console.error('Erro ao criar horários:', error);
    alert(error.response?.data?.message || 'Erro ao gerar horários.');
  } finally {
    isLoading.value = false;
  }
}

async function toggleAvailability(schedule) {
  const originalAvailability = schedule.available;
  schedule.available = !schedule.available;

  try {
    const payload = { available: schedule.available };

    payload.disponivel = schedule.available;

    await api.patch(`/schedules/${schedule.id}`, payload);
  } catch (error) {
    schedule.available = originalAvailability; //REVERTER SE DER ERRO
    console.error('Erro ao atualizar horário:', error);
    alert(error.response?.data?.message || 'Não foi possível atualizar o horário.');
  }
}

onMounted(() => fetchSchedules(selectedDate.value));

watch(selectedDate, (newDate) => fetchSchedules(newDate));
</script>
