<template>
  <div class="flex flex-col h-screen bg-[#28262E] text-[#F4EDE8]">
    <header class="h-28 bg-[#28262E] flex items-center justify-between px-4 sm:px-8 md:px-12 border-b-2 border-[#3E3B47]">
      <div class="flex items-center gap-4 md:gap-8">
        <img :src="logoUrl" alt="GoBarber Logo" class="h-20 w-auto" />
        <div class="hidden md:flex items-center gap-2 text-[#FF9000] cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span class="font-medium">Barbeiros</span>
        </div>
      </div>
      <div class="flex items-center gap-2 sm:gap-4">
        <router-link
          to="/perfil"
          class="flex items-center gap-2 sm:gap-4 cursor-pointer hover:opacity-80 transition-opacity"
        >
          <img
            v-if="profilePhotoUrl"
            class="object-cover w-12 h-12 sm:w-16 sm:h-16 rounded-full"
            :src="profilePhotoUrl"
            alt="Foto do usuário"
          >
          <!-- Adicionei um placeholder caso a foto ainda não tenha carregado ou não exista -->
          <div v-else class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gray-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div class="hidden sm:block text-left">
            <p class="text-base text-gray-300">Bem vindo,</p>
            <p class="text-lg font-bold text-[#FF9000]">{{ user?.name }}</p>
          </div>
        </router-link>

        <button @click="logout" class="ml-2 sm:ml-4 md:ml-8 text-[#999591] hover:text-[#FF9000]">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
          </svg>
        </button>
      </div>
    </header>

    <router-view />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import logoUrl from '/resources/images/logo.png?url';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import defaultAvatar from '/resources/images/defaultavatar.jpg'; 

const router = useRouter();
const user = ref(null);
const profilePhotoUrl = ref(defaultAvatar);

onMounted(async () => {
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }
  await fetchProfilePhoto();
});

async function fetchProfilePhoto() {
  try {
    const response = await api.get('/users/photo');
    profilePhotoUrl.value = `${response.data.foto_url}?t=${new Date().getTime()}`;
  } catch (error) {
    if (error.response && error.response.status === 404) {
      console.log('Usuário não possui foto de perfil, usando avatar padrão.');
    } else {
      console.error("Erro ao buscar a foto de perfil:", error);
    }
  }
}

async function logout() {
  try {
    await api.post('/users/logout');
  } catch (error) {
    console.error("Erro ao fazer logout no backend, mas limpando localmente mesmo assim.", error);
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    //REMOVE GLOBAL INSTANCE CREGIRED AT LOGIN
    delete window.axios.defaults.headers.common['Authorization'];

    router.push('/login');
  }
}
</script>
