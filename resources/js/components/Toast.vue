<template>
  <div class="fixed top-6 right-6 z-[100]">
    <transition name="toast-slide">
      <div v-if="show"
           :class="[
             'min-w-[320px] max-w-[460px] px-5 py-4 rounded-xl shadow-2xl text-base text-white backdrop-blur-md border flex items-start gap-3 relative',
             type === 'success' && 'bg-emerald-600/95 border-emerald-400/40',
             type === 'error' && 'bg-rose-600/95 border-rose-400/40',
             type === 'info' && 'bg-slate-700/95 border-slate-400/40'
           ]"
           :style="{ '--toast-duration': (duration || 3500) + 'ms' }">
        <div class="mt-0.5">
          <span v-if="type === 'success'">✅</span>
          <span v-else-if="type === 'error'">⚠️</span>
          <span v-else>ℹ️</span>
        </div>
        <div class="pr-6 leading-5">{{ message }}</div>
        <button type="button" class="absolute top-2 right-2/0 text-white/80 hover:text-white"
                @click="$emit('close')" aria-label="Fechar">
          ✕
        </button>
        <div class="absolute left-0 bottom-0 h-[3px] w-full overflow-hidden rounded-b-xl">
          <div class="toast-bar"></div>
        </div>
      </div>
    </transition>
  </div>

</template>

<script setup>
defineProps({
  show: { type: Boolean, default: false },
  message: { type: String, default: '' },
  type: { type: String, default: 'info' },
  duration: { type: Number, default: 3500 }
});

defineEmits(['close']);
</script>

<style scoped>
/* Toast animations */
.toast-slide-enter-from {
  opacity: 0;
  transform: translateX(18px) scale(0.98);
}
.toast-slide-enter-active {
  transition: all 240ms cubic-bezier(0.22, 1, 0.36, 1);
}
.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(18px) scale(0.98);
}
.toast-slide-leave-active {
  transition: all 180ms ease;
}
.toast-bar {
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, rgba(255,255,255,0.85), rgba(255,255,255,0.55));
  transform-origin: left center;
  animation: toast-progress var(--toast-duration, 3500ms) linear forwards;
}
@keyframes toast-progress {
  from { transform: scaleX(1); }
  to { transform: scaleX(0); }
}
</style>
