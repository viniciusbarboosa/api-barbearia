<template>
  <div class="flex flex-col items-start p-8 text-[#F4EDE8]">
    <h1 class="text-4xl font-bold mb-4">
      Dashboard do Barbeiro
    </h1>
    <p class="text-lg text-[#999591] mb-12">
      Aqui você gerencia sua agenda, clientes e serviços.
    </p>

    <router-link
      to="/horarios"
      class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-[#FF9000] text-[#312E38] font-bold text-lg rounded-lg hover:bg-opacity-90 transition-all shadow-lg shadow-black/20"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <span>Gerenciar Meus Horários</span>
    </router-link>
  </div>
</template>

<script setup>
</script>
