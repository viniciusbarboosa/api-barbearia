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
        <div class="flex items-center gap-2 sm:gap-4">
          <img class="object-cover w-12 h-12 sm:w-16 sm:h-16 rounded-full" src="https://i.pravatar.cc/150?u=tiago" alt="Foto do usuário">
          <div class="hidden sm:block text-left">
            <p class="text-base text-gray-300">Bem vindo,</p>
            <p class="text-lg font-bold text-[#FF9000]">Vinicius Barbosa</p>
          </div>
        </div>
        <button class="ml-2 sm:ml-4 md:ml-8 text-[#999591] hover:text-[#FF9000]">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
          </svg>
        </button>
      </div>
    </header>

    <main class="flex-1 p-4 sm:p-8 md:p-12 overflow-y-auto">
      <h1 class="text-3xl sm:text-4xl font-bold font-roboto-slab mb-8 sm:mb-10">Barbeiros</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="barber in barbers"
          :key="barber.id"
          class="bg-[#3E3B47] rounded-lg p-4 sm:p-6 flex items-center gap-4 sm:gap-5 cursor-pointer hover:ring-2 hover:ring-[#FF9000] transition-all"
        >
          <img class="object-cover w-16 h-16 sm:w-20 sm:h-20 rounded-full" :src="barber.avatar" :alt="barber.name">
          <div class="flex-1">
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
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import logoUrl from '../assets/imgs/logo.png';

const barbers = ref([
  { id: 1, name: 'Leonardo Minatti', avatar: 'https://i.pravatar.cc/150?u=leo' },
  { id: 2, name: 'Leonardo Minatti', avatar: 'https://i.pravatar.cc/150?u=leo2' },
  { id: 3, name: 'Leonardo Minatti', avatar: 'https://i.pravatar.cc/150?u=leo3' },
  { id: 4, name: 'Leonardo Minatti', avatar: 'https://i.pravatar.cc/150?u=leo4' },
]);
</script>

<style scoped>
.font-roboto-slab {
  font-family: 'Roboto Slab', serif;
}
</style>
