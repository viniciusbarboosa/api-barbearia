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

    <!-- Modal  -->
    <div v-if="showServicesModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div class="bg-[#3E3B47] rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-[#666360]">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-[#F4EDE8]">Escolha um serviço</h3>
            <button @click="closeServicesModal" class="text-[#999591] hover:text-[#FF9000] transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <p class="text-[#999591] mt-2">
            {{ barber?.name }} - {{ formattedDate }} às {{ selectedTime }}
          </p>
        </div>

        <div class="p-6 overflow-y-auto max-h-[60vh]">
          <div v-if="servicesLoading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#FF9000]"></div>
          </div>

          <div v-else-if="services.length === 0" class="text-center py-8">
            <p class="text-[#999591]">Nenhum serviço disponível</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="service in paginatedServices"
              :key="service.id"
              @click="selectService(service)"
              :class="[
                'p-4 rounded-lg border-2 cursor-pointer transition-all',
                selectedService?.id === service.id
                  ? 'border-[#FF9000] bg-[#FF9000]/10'
                  : 'border-[#666360] hover:border-[#FF9000]/50'
              ]"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-semibold text-[#F4EDE8]">{{ service.name }}</h4>
                  <p class="text-[#999591] text-sm mt-1">{{ service.description }}</p>
                  <div class="flex items-center gap-2 mt-2">
                    <span class="text-[#FF9000] font-bold">R$ {{ service.price }}</span>
                    <span class="text-xs text-[#999591]">{{ service.duration_minutes }} min</span>
                  </div>
                </div>
                <div v-if="selectedService?.id === service.id" class="text-[#FF9000]">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
              </div>
            </div>

            <div v-if="totalPages > 1" class="flex justify-center items-center gap-4 mt-6 pt-6 border-t border-[#666360]">
              <button
                @click="prevPage"
                :disabled="currentPage === 1"
                :class="[
                  'px-4 py-2 rounded-lg transition-colors',
                  currentPage === 1
                    ? 'bg-[#212026] text-[#666360] cursor-not-allowed'
                    : 'bg-[#FF9000] text-[#312E38] hover:bg-[#FFA733]'
                ]"
              >
                Anterior
              </button>

              <span class="text-[#999591]">
                Página {{ currentPage }} de {{ totalPages }}
              </span>

              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                :class="[
                  'px-4 py-2 rounded-lg transition-colors',
                  currentPage === totalPages
                    ? 'bg-[#212026] text-[#666360] cursor-not-allowed'
                    : 'bg-[#FF9000] text-[#312E38] hover:bg-[#FFA733]'
                ]"
              >
                Próxima
              </button>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-[#666360] bg-[#312E38]">
          <div class="flex justify-between items-center">
            <div>
              <p v-if="selectedService" class="text-[#F4EDE8]">
                {{ selectedService.name }} - R$ {{ selectedService.price }}
              </p>
              <p v-else class="text-[#999591]">Nenhum serviço selecionado</p>
            </div>
            <button
              @click="confirmAppointment"
              :disabled="!selectedService"
              :class="[
                'px-6 py-3 rounded-lg font-semibold transition-colors',
                selectedService
                  ? 'bg-[#FF9000] text-[#312E38] hover:bg-[#FFA733]'
                  : 'bg-[#212026] text-[#666360] cursor-not-allowed'
              ]"
            >
              Confirmar Agendamento
            </button>
          </div>
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

//MODAL SERVICES
const showServicesModal = ref(false);
const services = ref([]);
const servicesLoading = ref(false);
const selectedService = ref(null);
const selectedSchedule = ref(null);

const currentPage = ref(1);
const servicesPerPage = 5;

const formatDateForAPI = (date) => date.toISOString().split('T')[0];

const formattedDate = computed(() => {
  return selectedDate.value.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long' });
});

const selectedTime = computed(() => {
  return selectedSchedule.value?.start_time.substring(0, 5) || '';
});

const paginatedServices = computed(() => {
  const start = (currentPage.value - 1) * servicesPerPage;
  const end = start + servicesPerPage;
  return services.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(services.value.length / servicesPerPage);
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

async function fetchServices() {
  servicesLoading.value = true;
  services.value = [];
  try {
    const response = await api.get('/services/by-barber', {
      params: {
        user_id: barberId
      }
    });

    if (response.data && Array.isArray(response.data)) {
      services.value = response.data;
    } else if (response.data?.data && Array.isArray(response.data.data)) {
      services.value = response.data.data;
    } else if (response.data?.success && Array.isArray(response.data.data)) {
      services.value = response.data.data;
    } else {
      services.value = [];
    }
  } catch (error) {
    alert("Erro ao FAzer Cadatras Serviço")
  } finally {
    servicesLoading.value = false;
  }
}

function selectTime(schedule) {
  if (!schedule.available || !barber.value) return;

  selectedSchedule.value = schedule;
  selectedService.value = null;
  currentPage.value = 1;
  showServicesModal.value = true;
  fetchServices();
}

function selectService(service) {
  selectedService.value = service;
}

function closeServicesModal() {
  showServicesModal.value = false;
  selectedService.value = null;
  selectedSchedule.value = null;
  currentPage.value = 1;
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
}

function prevPage() {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
}

async function confirmAppointment() {
  if (!selectedService.value || !selectedSchedule.value) return;

  try {
    const appointmentData = {
      barber_id: barberId,
      service_id: selectedService.value.id,
      schedule_id: selectedSchedule.value.id,
      date: formatDateForAPI(selectedDate.value),
      appointment_time: selectedSchedule.value.start_time
    };

    const response = await api.post('/appointments', appointmentData);

    if (response.data.success || response.status === 201) {
      alert('Agendamento realizado com sucesso!');
      closeServicesModal();
      fetchSchedules(selectedDate.value);
    } else {
      alert('Erro ao realizar agendamento: ' + (response.data.message || 'Tente novamente'));
    }
  } catch (error) {
    alert('Erro ao criar agendamento!');
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
