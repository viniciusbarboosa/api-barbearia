<template>
  <div class="min-h-screen bg-[#312E38] text-white p-4 sm:p-8">

    <div v-if="loading && agendamentos.length === 0" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-[#FF9000]"></div>
    </div>

    <div class="flex items-center gap-4 sm:gap-6 mb-8">
      <router-link to="/" class="text-[#999591] hover:text-[#FF9000] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </router-link>
      <h1 class="text-2xl sm:text-3xl font-bold text-[#F4EDE8]">
        Meus Agendamentos
      </h1>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">

      <div class="lg:w-1/3">
        <div class="bg-[#232129] rounded-lg p-4 sticky top-4">
          <h2 class="text-lg font-semibold text-[#F4EDE8] mb-4">Selecione uma data</h2>
          <VDatePicker
            v-model="selectedDate"
            is-dark
            color="orange"
            :attributes="calendarAttributes"
            title-position="left"
            class="custom-calendar"
            @dayclick="handleDateSelect"
          />
        </div>
      </div>

      <div class="lg:w-2/3">
        <div class="bg-[#232129] rounded-lg p-6">
          <h2 class="text-xl font-semibold text-[#FF9000] mb-6">
            Agendamentos para {{ formattedDate }}
          </h2>

          <div v-if="!loading && agendamentos.length > 0" class="space-y-4">

            <div
              v-for="agendamento in agendamentos"
              :key="agendamento.id"
              class="bg-[#3E3B47] rounded-lg p-5 border-l-4 hover:border-l-[#FF9000] transition-all duration-300"
              :class="agendamento.status === 'C' ? 'border-red-500' : 'border-[#FF9000]'"
            >

              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-[#F4EDE8] mb-1">{{ agendamento.service_name }}</h3>
                  <p class="text-[#999591] text-sm">{{ agendamento.service_description }}</p>
                </div>
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    agendamento.status === 'C'
                      ? 'bg-red-600 text-white'
                      : 'bg-green-600 text-white'
                  ]"
                >
                  {{ agendamento.status === 'C' ? 'Cancelado' : 'Agendado' }}
                </span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div class="flex items-start gap-3">
                  <div class="bg-[#FF9000] p-2 rounded-lg">
                    <svg class="w-4 h-4 text-[#312E38]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-[#F4EDE8] font-medium">Data e Horário</p>
                    <p class="text-[#999591]">{{ formatDate(agendamento.date) }}</p>
                    <p class="text-[#999591] text-xs">{{ agendamento.start_time }} - {{ agendamento.end_time }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="bg-[#FF9000] p-2 rounded-lg">
                    <svg class="w-4 h-4 text-[#312E38]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-[#F4EDE8] font-medium">Barbeiro</p>
                    <p class="text-[#999591]">{{ agendamento.barber_name }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="bg-[#FF9000] p-2 rounded-lg">
                    <svg class="w-4 h-4 text-[#312E38]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-[#F4EDE8] font-medium">Valor</p>
                    <p class="text-[#999591]">R$ {{ parseFloat(agendamento.service_price).toFixed(2) }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <div class="bg-[#FF9000] p-2 rounded-lg">
                    <svg class="w-4 h-4 text-[#312E38]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-[#F4EDE8] font-medium">Duração</p>
                    <p class="text-[#999591]">{{ agendamento.service_duration }} minutos</p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="currentPage < lastPage" class="flex justify-center pt-6">
              <button
                @click="loadMore"
                :disabled="loadingMore"
                class="px-6 py-3 bg-[#FF9000] text-[#312E38] font-semibold rounded-lg hover:bg-[#FFA733] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <span v-if="loadingMore" class="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-[#312E38]"></span>
                {{ loadingMore ? 'Carregando...' : 'Carregar Mais' }}
              </button>
            </div>
          </div>

          <div v-else-if="!loading && agendamentos.length === 0" class="text-center py-12">
            <svg class="w-20 h-20 text-[#666360] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-[#999591] text-lg mb-2">
              {{ selectedDate ? 'Nenhum agendamento para esta data' : 'Selecione uma data para ver os agendamentos' }}
            </p>
            <p class="text-[#666360] text-sm">
              Seus agendamentos aparecerão aqui quando você fizer uma reserva.
            </p>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<style>
.custom-calendar .vc-pane-layout {
  background-color: #232129;
  border-radius: 10px;
}
.custom-calendar .vc-header {
  padding: 16px 16px 12px 16px;
  margin-bottom: 8px;
}
.custom-calendar .vc-title {
  color: #FF9000;
  font-weight: 700;
  font-size: 1.1rem;
}
.custom-calendar .vc-arrow {
  color: #FF9000;
  transform: scale(1.2);
}
.custom-calendar .vc-weekday {
  color: #999591;
  padding-bottom: 8px;
  font-weight: 500;
}
.custom-calendar .vc-day-content {
  color: #F4EDE8;
  border-radius: 9999px;
  width: 36px;
  height: 36px;
  font-weight: 500;
}
.custom-calendar .vc-day-content:hover {
  background-color: rgba(255, 144, 0, 0.2);
}
.custom-calendar .vc-day-content.is-disabled {
  color: #666360;
}
.custom-calendar .vc-day-content.is-selected {
  background-color: #FF9000;
  color: #312E38;
  font-weight: 600;
}

html, body {
  background-color: #312E38;
  min-height: 100vh;
}
</style>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { DatePicker as VDatePicker } from 'v-calendar';
import 'v-calendar/dist/style.css';
import api from '../services/api';

const selectedDate = ref(new Date());
const agendamentos = ref([]);
const loading = ref(true);
const loadingMore = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);

const calendarAttributes = computed(() => [
  {
    key: 'selected',
    highlight: {
      color: 'orange',
      fillMode: 'solid',
    },
    dates: selectedDate.value,
  },
  {
    key: 'today',
    dot: true,
    dates: new Date(),
  }
]);

const formattedDate = computed(() => {
  return selectedDate.value.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long' });
});

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR');
};

const handleDateSelect = (date) => {
  selectedDate.value = date.date;
  currentPage.value = 1;
  loadAgendamentos(1, date.date.toISOString().split('T')[0]);
};

const loadAgendamentos = async (page = 1, date = null) => {
  try {
    page === 1 ? loading.value = true : loadingMore.value = true;

    const selectedDateStr = date || selectedDate.value.toISOString().split('T')[0];

    const response = await api.get('/appointments/my', {
      params: {
        data: selectedDateStr,
        page: page
      }
    });

    if (response.data.success) {
      currentPage.value = response.data.current_page;
      lastPage.value = response.data.last_page;

      if (page === 1) {
        agendamentos.value = response.data.data;
      } else {
        agendamentos.value = [...agendamentos.value, ...response.data.data];
      }
    }
  } catch (error) {

  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

const loadMore = () => {
  if (currentPage.value < lastPage.value && !loadingMore.value) {
    loadAgendamentos(currentPage.value + 1);
  }
};


onMounted(() => {
  loadAgendamentos();
});
</script>
