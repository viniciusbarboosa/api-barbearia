<template>
  <div class="min-h-screen bg-[#28262E] text-white p-4 sm:p-8">
  
    <div v-if="isLoading" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-[#FF9000]"></div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
      <div class="flex items-center gap-4">
        <router-link to="/" class="text-[#999591] hover:text-[#FF9000] transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </router-link>
        <h1 class="text-3xl font-bold text-[#F4EDE8]">Meus Serviços</h1>
      </div>
      <button @click="openCreateModal" class="flex items-center gap-2 px-4 py-2 font-medium text-[#312E38] bg-[#FF9000] rounded-lg hover:bg-opacity-90">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        <span>Adicionar Serviço</span>
      </button>
    </div>

    <div v-if="!isLoading && services.length > 0" class="space-y-4">
      <div v-for="service in services" :key="service.id" class="bg-[#3E3B47] p-4 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex-1">
          <p class="text-xl font-bold text-[#F4EDE8]">{{ service.name }}</p>
          <div class="flex items-center gap-4 text-sm text-[#999591] mt-1">
            <span>R$ {{ parseFloat(service.price).toFixed(2).replace('.', ',') }}</span>
            <span>•</span>
            <span>{{ service.duration_minutes }} min</span>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <button @click="toggleServiceStatus(service)" :class="['px-3 py-1 text-xs font-bold rounded-full', service.active ? 'bg-green-600' : 'bg-gray-600']">
            {{ service.active ? 'ATIVO' : 'INATIVO' }}
          </button>
          <button @click="openEditModal(service)" class="p-2 text-[#999591] hover:text-[#FF9000]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-if="!isLoading && services.length === 0" class="text-center py-16">
      <p class="text-[#999591]">Você ainda não cadastrou nenhum serviço.</p>
    </div>

    <div v-if="pagination.last_page > 1" class="flex justify-center mt-8">
      <button @click="fetchServices(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="px-4 py-2 mx-1 bg-[#3E3B47] rounded disabled:opacity-50">Anterior</button>
      <button @click="fetchServices(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="px-4 py-2 mx-1 bg-[#3E3B47] rounded disabled:opacity-50">Próximo</button>
    </div>

    <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
      <div class="bg-[#3E3B47] p-8 rounded-xl w-full max-w-lg border-2 border-[#FF9000]">
        <h3 class="text-2xl font-bold mb-6 text-center text-[#F4EDE8]">{{ modalTitle }}</h3>
        <form @submit.prevent="handleFormSubmit" class="space-y-4">
          <div>
            <label for="name" class="block mb-2 text-sm font-medium text-[#999591]">Nome do Serviço</label>
            <input v-model="serviceForm.name" type="text" id="name" class="w-full p-3 text-[#F4EDE8] bg-[#28262E] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required>
          </div>
          <div class="flex gap-4">
            <div class="flex-1">
              <label for="price" class="block mb-2 text-sm font-medium text-[#999591]">Preço (R$)</label>
              <input v-model="serviceForm.price" type="number" step="0.01" id="price" class="w-full p-3 text-[#F4EDE8] bg-[#28262E] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required>
            </div>

            <div class="flex-1">
              <label for="duration" class="block mb-2 text-sm font-medium text-[#999591]">Duração (minutos)</label>
              <select v-model="serviceForm.duration_minutes" id="duration" class="w-full p-3 text-[#F4EDE8] bg-[#28262E] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required>
                <option disabled value="">Selecione...</option>
                <option v-for="time in durationOptions" :key="time" :value="time">
                  {{ time }} min
                </option>
              </select>
            </div>

          </div>
          <div class="flex justify-between gap-4 pt-4">
            <button @click="closeModal" type="button" class="w-full py-3 font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">Cancelar</button>
            <button type="submit" class="w-full py-3 font-medium text-[#312E38] bg-[#FF9000] rounded-lg hover:bg-opacity-90">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import api from '../services/api';

const durationOptions = [15, 30, 45, 60, 75, 90, 105, 120];

const services = ref([]);
const isLoading = ref(true);
const isModalOpen = ref(false);
const editingService = ref(null);
const pagination = ref({});

const serviceForm = reactive({
  id: null,
  name: '',
  price: '',
  duration_minutes: ''
});

const modalTitle = computed(() => editingService.value ? 'Editar Serviço' : 'Adicionar Novo Serviço');

async function fetchServices(page = 1) {
  isLoading.value = true;
  try {
    const response = await api.get('/services', { params: { page } });
    services.value = response.data.data;
    const { data, ...paginationInfo } = response.data;
    pagination.value = paginationInfo;
  } catch (error) {
    console.error("Erro ao buscar serviços:", error);
    alert('Não foi possível carregar os serviços.');
  } finally {
    isLoading.value = false;
  }
}

function openCreateModal() {
  editingService.value = null;
  Object.assign(serviceForm, { id: null, name: '', price: '', duration_minutes: 30 });
  isModalOpen.value = true;
}

function openEditModal(service) {
  editingService.value = service;
  Object.assign(serviceForm, {
    id: service.id,
    name: service.name,
    price: service.price,
    duration_minutes: service.duration_minutes
  });
  isModalOpen.value = true;
}

function closeModal() {
  isModalOpen.value = false;
}

async function handleFormSubmit() {
  isLoading.value = true;
  const payload = { ...serviceForm };
  try {
    if (editingService.value) {
      await api.put(`/services/${editingService.value.id}`, payload);
    } else {
      await api.post('/services', payload);
    }
    closeModal();
    await fetchServices(pagination.value.current_page || 1);
    alert(`Serviço ${editingService.value ? 'atualizado' : 'criado'} com sucesso!`);
  } catch (error) {
    console.error("Erro ao salvar serviço:", error);
    alert(error.response?.data?.message || 'Ocorreu um erro ao salvar.');
  } finally {
    isLoading.value = false;
  }
}

async function toggleServiceStatus(service) {
  const originalStatus = service.active;
  service.active = !service.active;
  try {
    await api.patch(`/services/${service.id}/toggle`);
  } catch (error) {
    service.active = originalStatus;
    console.error("Erro ao alterar status:", error);
    alert('Não foi possível alterar o status do serviço.');
  }
}

onMounted(() => {
  fetchServices();
});
</script>
