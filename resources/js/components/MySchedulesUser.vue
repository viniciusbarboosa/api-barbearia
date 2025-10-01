<template>
  <div class="min-h-screen bg-[#312E38] text-white p-4">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-[#F4EDE8]">Meus Agendamentos</h1>
    </div>

    <!-- Calendário -->
    <div class="bg-[#232129] rounded-lg p-4 mb-6">
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

    <!-- Lista de Agendamentos -->
    <div v-if="loading && agendamentos.length === 0" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-[#FF9000]"></div>
    </div>

    <div v-else class="space-y-4">
      <!-- Agendamentos -->
      <div
        v-for="agendamento in agendamentos"
        :key="agendamento.id"
        class="bg-[#232129] rounded-lg border border-[#3E3B47] p-4"
      >
        <!-- Header do Card -->
        <div class="flex justify-between items-center border-b border-[#3E3B47] pb-3 mb-3">
          <span class="text-[#F4EDE8] font-semibold">
            {{ formatDate(agendamento.data) }}
          </span>
          <span
            :class="[
              'px-3 py-1 rounded-full text-xs font-medium border',
              agendamento.status === 'cancelado'
                ? 'bg-red-600 border-red-400 text-white'
                : 'bg-green-600 border-green-400 text-white'
            ]"
          >
            {{ agendamento.status }}
          </span>
        </div>

        <!-- Corpo do Card -->
        <div class="space-y-3">
          <!-- Serviço -->
          <div class="flex items-center">
            <svg class="w-5 h-5 text-[#FF9000] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <span class="text-[#F4EDE8] font-semibold text-lg">{{ agendamento.servico_nome }}</span>
          </div>

          <!-- Descrição -->
          <p class="text-[#999591] text-sm">{{ agendamento.servico_descricao }}</p>

          <!-- Horário -->
          <div class="flex items-center">
            <svg class="w-4 h-4 text-[#FF9000] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-[#999591] text-sm">
              {{ agendamento.horario_inicio }} - {{ agendamento.horario_fim }}
              ({{ agendamento.servico_duracao }} min)
            </span>
          </div>

          <!-- Barbeiro -->
          <div class="flex items-center">
            <svg class="w-4 h-4 text-[#FF9000] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-[#999591] text-sm">{{ agendamento.barbeiro_nome }}</span>
          </div>

          <!-- Preço -->
          <div class="flex items-center">
            <svg class="w-4 h-4 text-[#FF9000] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            <span class="text-[#999591] text-sm">
              R$ {{ parseFloat(agendamento.servico_preco).toFixed(2) }}
            </span>
          </div>
        </div>

        <!-- Footer do Card -->
        <div class="flex justify-between mt-4 pt-3 border-t border-[#3E3B47]">
          <button
            v-if="agendamento.status !== 'cancelado'"
            @click="cancelarAgendamento(agendamento.id)"
            class="px-4 py-2 bg-[#3E3B47] text-[#F4EDE8] rounded-lg hover:bg-[#4A4550] transition-colors"
          >
            Cancelar
          </button>
          <div v-else></div>

          <button
            class="px-4 py-2 bg-[#FF9000] text-[#312E38] font-semibold rounded-lg hover:bg-[#FFA733] transition-colors"
          >
            Detalhes
          </button>
        </div>
      </div>

      <!-- Loading More -->
      <div v-if="loadingMore" class="flex justify-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#FF9000]"></div>
      </div>

      <!-- Sem Agendamentos -->
      <div v-if="!loading && agendamentos.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 text-[#666360] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <p class="text-[#666360] text-lg">Nenhum agendamento para esta data</p>
      </div>

      <!-- Botão Carregar Mais -->
      <div v-if="currentPage < lastPage && !loadingMore" class="flex justify-center pt-4">
        <button
          @click="loadMore"
          class="px-6 py-3 bg-[#FF9000] text-[#312E38] font-semibold rounded-lg hover:bg-[#FFA733] transition-colors"
        >
          Carregar Mais
        </button>
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
</style>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { DatePicker as VDatePicker } from 'v-calendar';
import 'v-calendar/dist/style.css';
import api from '../services/api';

// Estados
const selectedDate = ref(new Date());
const agendamentos = ref([]);
const loading = ref(true);
const loadingMore = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);

// Computed
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

// Métodos
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

    console.log('Resposta da API:', response.data);

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
    console.error('Erro ao carregar agendamentos:', {
      message: error.message,
      response: error.response,
      config: error.config
    });
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

const cancelarAgendamento = async (agendamentoId) => {
  if (!confirm('Tem certeza que deseja cancelar este agendamento?')) {
    return;
  }

  try {
    const response = await api.put(`/appointments/${agendamentoId}/status`, {
      status: 'cancelado'
    });

    if (response.data.success) {
      alert('Agendamento cancelado com sucesso!');
      // Recarrega os agendamentos
      loadAgendamentos(1, selectedDate.value.toISOString().split('T')[0]);
    }
  } catch (error) {
    console.error('Erro ao cancelar agendamento:', error);
    alert('Erro ao cancelar agendamento. Tente novamente.');
  }
};

// Lifecycle
onMounted(() => {
  loadAgendamentos();
});
</script>
