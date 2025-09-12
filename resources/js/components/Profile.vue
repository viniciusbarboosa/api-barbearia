<template>
  <div class="min-h-screen bg-[#28262E] text-white">

    <div class="h-36 bg-[#3E3B47] flex items-start p-8">
      <button @click="$router.back()" class="text-[#999591] hover:text-[#FF9000]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
      </button>
    </div>

    <div class="flex flex-col items-center -mt-20 px-4">

      <div class="relative w-44 h-44 mb-8">

        <img class="object-cover w-full h-full rounded-full border-4 border-[#28262E]" src="https://picsum.photos/seed/tiago-profile/300/300" alt="Foto do usuário">

        <label for="avatar-upload" class="absolute bottom-2 right-2 w-12 h-12 bg-[#FF9000] rounded-full flex items-center justify-center cursor-pointer hover:bg-opacity-90">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#312E38]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <input id="avatar-upload" type="file" class="hidden" />
        </label>
      </div>

      <form @submit.prevent="handleSubmit" class="w-full max-w-sm flex flex-col gap-3">
        <h2 class="text-2xl font-medium text-white text-center mb-6">Meu perfil</h2>

        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#666360]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
          </div>
          <input v-model="form.name" type="text" class="w-full py-4 pl-12 pr-4 text-[#F4EDE8] bg-[#312E38] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required />
        </div>

        <div class="relative">
          <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#666360]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
          </div>
          <input v-model="form.email" type="email" class="w-full py-4 pl-12 pr-4 text-[#F4EDE8] bg-[#312E38] rounded-lg outline-none focus:ring-2 focus:ring-[#FF9000]" required />
        </div>

        <div class="mt-8 flex flex-col gap-3">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#666360]">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <input v-model="form.old_password" type="password" placeholder="Senha atual" class="w-full py-4 pl-12 pr-4 text-[#F4EDE8] bg-[#312E38] rounded-lg placeholder-gray-500 outline-none focus:ring-2 focus:ring-[#FF9000]" />
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#666360]">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <input v-model="form.new_password" type="password" placeholder="Nova senha" class="w-full py-4 pl-12 pr-4 text-[#F4EDE8] bg-[#312E38] rounded-lg placeholder-gray-500 outline-none focus:ring-2 focus:ring-[#FF9000]" />
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-[#666360]">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <input v-model="form.password_confirmation" type="password" placeholder="Confirmar senha" class="w-full py-4 pl-12 pr-4 text-[#F4EDE8] bg-[#312E38] rounded-lg placeholder-gray-500 outline-none focus:ring-2 focus:ring-[#FF9000]" />
          </div>
        </div>

        <button type="submit" class="w-full py-4 mt-4 font-medium text-[#312E38] bg-[#FF9000] rounded-lg hover:bg-opacity-90">
          Confirmar mudanças
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';

const form = reactive({
  name: 'Tiago Luchtenberg',
  email: 'tiagoluchtenberg@gmail.com',
  old_password: '',
  new_password: '',
  password_confirmation: ''
});

function handleSubmit() {
  console.log('Formulário enviado:', form);
}
</script>
