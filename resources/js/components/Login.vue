<template>
  <div class="min-h-screen bg-[#312E38] flex items-center justify-center px-4 overflow-hidden relative" :class="{ 'mode-register': mode === 'register' }">
    <!-- Low-opacity centered background image behind forms -->
    <div class="auth-bg" :style="{ backgroundImage: `url(${bgImage})` }" aria-hidden="true" />

    <div class="w-full grid gap-6 items-center grid-cols-1 z-10">
      <div class="max-w-md w-full mx-auto">
        <div class="form-area relative">
          <div class="mx-auto mb-6 text-center">
            <img :src="logoImage" alt="Barbeia.ai logo" class="mx-auto w-40 h-auto header-logo" />
          </div>

          <!-- Single box: fade only the inner content between login/forgot/register -->
          <transition name="content-fade" mode="out-in">
            <!-- Login -->
            <div v-if="mode === 'login'" key="login">
              <h2 class="text-white text-center mb-6 login-title">Faça seu login</h2>
              <form @submit.prevent="submit" class="flex flex-col gap-4 form-panel form-login" :aria-hidden="mode !== 'login'">
                <label class="relative block input-label">
                  <span class="sr-only">E-mail</span>
                  <div class="absolute inset-y-0 left-3 flex items-center text-blue-500 input-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none">
                      <path d="M3.6 2.9519H16.4C17.28 2.9519 18 3.6323 18 4.4639V13.5359C18 14.3675 17.28 15.0479 16.4 15.0479H3.6C2.72 15.0479 2 14.3675 2 13.5359V4.4639C2 3.6323 2.72 2.9519 3.6 2.9519Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M18 5.04001L10 10.08L2 5.04001" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                  <input v-model="form.email" type="email" placeholder="E-mail" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input email-input" required />
                </label>

                <label class="relative block input-label">
                  <span class="sr-only">Senha</span>
                  <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                    <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_34_1204)">
                        <path d="M15.4444 8.17145H4.55556C3.69645 8.17145 3 8.90407 3 9.80781V15.5351C3 16.4388 3.69645 17.1714 4.55556 17.1714H15.4444C16.3036 17.1714 17 16.4388 17 15.5351V9.80781C17 8.90407 16.3036 8.17145 15.4444 8.17145Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6 8.17144V4.97144C6 3.91057 6.42143 2.89315 7.17157 2.14301C7.92172 1.39286 8.93913 0.971436 10 0.971436C11.0609 0.971436 12.0783 1.39286 12.8284 2.14301C13.5786 2.89315 14 3.91057 14 4.97144V8.17144" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      </g>
                      <defs>
                        <clipPath id="clip0_34_1204">
                          <rect width="20" height="18" fill="white" transform="translate(0 0.0714417)" />
                        </clipPath>
                      </defs>
                    </svg>
                  </div>
                  <input v-model="form.password" type="password" placeholder="Senha" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input password-input" required />
                </label>

                <button :disabled="loading" :aria-busy="loading ? 'true' : 'false'" class="mt-3 bg-amber-500 text-[#2b2430] login-button py-3 rounded-lg shadow-md disabled:opacity-70 disabled:cursor-not-allowed">
                  <span v-if="loading" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-[#2b2430]" viewBox="0 0 24 24" fill="none">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Entrando...
                  </span>
                  <span v-else>Entrar</span>
                </button>

                <div class="flex flex-col gap-2 mt-3 text-sm items-center text-center">
                  <a href="#" class="hover:underline forgot-link" style="margin-bottom: 80px" @click.prevent="(forgotStep = 1, mode = 'forgot')">Esqueci minha senha</a>

                  <button type="button" @click="mode = 'register'" class="flex items-center gap-2 login-link text-[#FF9000] hover:underline transition-colors">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12.5 2.5L15.8333 2.5C16.2754 2.5 16.6993 2.6756 17.0118 2.98816C17.3244 3.30072 17.5 3.72464 17.5 4.16667L17.5 15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5L12.5 17.5" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M8.33334 14.1667L12.5 10L8.33334 5.83335" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M12.5 10L2.5 10" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Criar conta
                  </button>
                </div>
              </form>
              <p v-if="error && mode === 'login'" class="text-sm text-red-400 mt-4 text-center">{{ error }}</p>
            </div>

            <!-- Forgot password -->
            <div v-else-if="mode === 'forgot'" key="forgot">
              <form class="flex flex-col gap-4 form-panel form-forgot" @submit.prevent="forgotStep === 1 ? sendForgotEmail() : submitResetWithCode()">
                <h2 class="text-white text-center mb-2 login-title">Recuperar senha</h2>

                <template v-if="forgotStep === 1">
                  <label class="relative block input-label">
                    <span class="sr-only">E-mail</span>
                    <div class="absolute inset-y-0 left-3 flex items-center text-blue-500 input-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none">
                        <path d="M3.6 2.9519H16.4C17.28 2.9519 18 3.6323 18 4.4639V13.5359C18 14.3675 17.28 15.0479 16.4 15.0479H3.6C2.72 15.0479 2 14.3675 2 13.5359V4.4639C2 3.6323 2.72 2.9519 3.6 2.9519Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M18 5.04001L10 10.08L2 5.04001" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </div>
                    <input v-model="forgot.email" type="email" placeholder="E-mail" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input email-input" required />
                  </label>

                  <button :disabled="forgotLoading" :aria-busy="forgotLoading ? 'true' : 'false'" class="mt-3 bg-amber-500 text-[#2b2430] login-button py-3 rounded-lg shadow-md disabled:opacity-70 disabled:cursor-not-allowed">
                    <span v-if="forgotLoading" class="inline-flex items-center gap-2">
                      <svg class="animate-spin h-5 w-5 text-[#2b2430]" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                      </svg>
                      Enviando...
                    </span>
                    <span v-else>Enviar código</span>
                  </button>
                  <button type="button" @click="mode = 'login'" class="mt-20 mx-auto block flex items-center justify-center gap-2 login-link text-[#FF9000] hover:underline transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11 5L6 10L11 15" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M6 10H18" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Voltar para login
                  </button>
                </template>

                <template v-else>
                  <label class="relative block input-label">
                    <span class="sr-only">Código</span>
                    <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v5l3 3" />
                      </svg>
                    </div>
                    <input v-model="forgot.code" type="text" placeholder="Código de verificação" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input code-input" required />
                  </label>

                  <label class="relative block input-label">
                    <span class="sr-only">Nova senha</span>
                    <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                      <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.4444 8.17145H4.55556C3.69645 8.17145 3 8.90407 3 9.80781V15.5351C3 16.4388 3.69645 17.1714 4.55556 17.1714H15.4444C16.3036 17.1714 17 16.4388 17 15.5351V9.80781C17 8.90407 16.3036 8.17145 15.4444 8.17145Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6 8.17144V4.97144C6 3.91057 6.42143 2.89315 7.17157 2.14301C7.92172 1.39286 8.93913 0.971436 10 0.971436C11.0609 0.971436 12.0783 1.39286 12.8284 2.14301C13.5786 2.89315 14 3.91057 14 4.97144V8.17144" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                  <input v-model="forgot.password" type="password" placeholder="Nova senha" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input" required />
                </label>

                <label class="relative block input-label">
                  <span class="sr-only">Confirmar senha</span>
                  <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                    <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M15.4444 8.17145H4.55556C3.69645 8.17145 3 8.90407 3 9.80781V15.5351C3 16.4388 3.69645 17.1714 4.55556 17.1714H15.4444C16.3036 17.1714 17 16.4388 17 15.5351V9.80781C17 8.90407 16.3036 8.17145 15.4444 8.17145Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M6 8.17144V4.97144C6 3.91057 6.42143 2.89315 7.17157 2.14301C7.92172 1.39286 8.93913 0.971436 10 0.971436C11.0609 0.971436 12.0783 1.39286 12.8284 2.14301C13.5786 2.89315 14 3.91057 14 4.97144V8.17144" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                  <input v-model="forgot.password_confirmation" type="password" placeholder="Confirmar senha" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input" required />
                </label>

                <button :disabled="forgotLoading" :aria-busy="forgotLoading ? 'true' : 'false'" class="mt-3 bg-amber-500 text-[#2b2430] login-button py-3 rounded-lg shadow-md disabled:opacity-70 disabled:cursor-not-allowed">
                  <span v-if="forgotLoading" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-[#2b2430]" viewBox="0 0 24 24" fill="none">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Redefinindo...
                  </span>
                  <span v-else>Redefinir senha</span>
                </button>
                <button type="button" @click="mode = 'login'" class="mt-20 mx-auto block flex items-center justify-center gap-2 login-link text-[#FF9000] hover:underline transition-colors">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 5L6 10L11 15" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 10H18" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Voltar para login
                </button>
              </template>
              </form>
            </div>

            <!-- Register inside same box -->
            <div v-else key="register">
              <h2 class="text-white text-center mb-6 login-title">Crie sua conta</h2>
              <!-- Segmented switch for user type (Barbeiro | Cliente) -->
              <div class="input-label">
                <label id="userTypeLabel" class="block text-sm text-gray-300 mb-2" style="text-align:left; font-family: 'Roboto Slab', serif;">Você é:</label>
                <div class="segmented-switch" role="radiogroup" aria-labelledby="userTypeLabel" :class="[{ 'has-value': !!register.user_type }, register.user_type ? (register.user_type === 'U' ? 'pos-u' : 'pos-b') : 'pos-none']">
                  <div class="segmented-thumb" aria-hidden="true"></div>
                  <label class="segmented-option">
                    <input type="radio" class="sr-only" name="user_type" value="B" v-model="register.user_type" aria-label="Barbeiro" />
                    <span class="segmented-label">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 3C6.89543 3 6 3.89543 6 5C6 6.10457 6.89543 7 8 7C9.10457 7 10 6.10457 10 5C10 3.89543 9.10457 3 8 3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3C14.8954 3 14 3.89543 14 5C14 6.10457 14.8954 7 16 7C17.1046 7 18 6.10457 18 5C18 3.89543 17.1046 3 16 3Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12L11 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 12L13 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 13L12 21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      Barbeiro
                    </span>
                  </label>
                  <label class="segmented-option">
                    <input type="radio" class="sr-only" name="user_type" value="U" v-model="register.user_type" aria-label="Cliente" />
                    <span class="segmented-label">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M4 21V19C4 16.7909 5.79086 15 8 15H16C18.2091 15 20 16.7909 20 19V21" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                      Cliente
                    </span>
                  </label>
                </div>
              </div>
              <form @submit.prevent="submitRegister" class="flex flex-col gap-4 w-full form-panel form-register" :aria-hidden="mode === 'login'">
                <label class="relative block input-label">
                  <span class="sr-only">Nome</span>
                  <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5Z"/><path d="M3.2 20.2c.8-3.4 4-6.2 8.8-6.2s8 2.8 8.8 6.2" /></svg>
                  </div>
                  <input v-model="register.name" type="text" placeholder="Nome" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input" required />
                </label>
                <label class="relative block input-label">
                  <span class="sr-only">E-mail</span>
                  <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none"><path d="M3.6 2.9519H16.4C17.28 2.9519 18 3.6323 18 4.4639V13.5359C18 14.3675 17.28 15.0479 16.4 15.0479H3.6C2.72 15.0479 2 14.3675 2 13.5359V4.4639C2 3.6323 2.72 2.9519 3.6 2.9519Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 5.04001L10 10.08L2 5.04001" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </div>
                  <input v-model="register.email" type="email" placeholder="E-mail" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input" required />
                </label>
                <label class="relative block input-label">
                  <span class="sr-only">Senha</span>
                  <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                    <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.4444 8.17145H4.55556C3.69645 8.17145 3 8.90407 3 9.80781V15.5351C3 16.4388 3.69645 17.1714 4.55556 17.1714H15.4444C16.3036 17.1714 17 16.4388 17 15.5351V9.80781C17 8.90407 16.3036 8.17145 15.4444 8.17145Z" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 8.17144V4.97144C6 3.91057 6.42143 2.89315 7.17157 2.14301C7.92172 1.39286 8.93913 0.971436 10 0.971436C11.0609 0.971436 12.0783 1.39286 12.8284 2.14301C13.5786 2.89315 14 3.91057 14 4.97144V8.17144" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </div>
                  <input v-model="register.password" type="password" placeholder="Senha" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input" required />
                </label>
                <div class="flex flex-col gap-3 mt-2">
                  <button :disabled="loadingRegister" :aria-busy="loadingRegister ? 'true' : 'false'" class="mt-1 bg-amber-500 text-[#2b2430] login-button py-3 rounded-lg shadow-md disabled:opacity-70 disabled:cursor-not-allowed">
                    <span v-if="loadingRegister" class="inline-flex items-center gap-2">
                      <svg class="animate-spin h-5 w-5 text-[#2b2430]" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                      Cadastrando...
                    </span>
                    <span v-else>Cadastrar</span>
                  </button>
                  <button type="button" @click="mode = 'login'" class="mt-20 mx-auto block flex items-center justify-center gap-2 login-link text-[#FF9000] hover:underline transition-colors">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 5L6 10L11 15" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 10H18" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Voltar para login
                  </button>
                  <p v-if="error && mode === 'register'" class="text-sm text-red-400 text-center">{{ error }}</p>
                </div>
              </form>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <Toast :show="toast.show" :message="toast.message" :type="toast.type" :duration="toast.duration" @close="toast.show = false" />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import Toast from './Toast.vue';
import api from '@/services/api';

import logoImage from '@images/logo.png?url';
import bgImage from '@images/bg.jpeg?url';

const mode = ref('login'); // 'login' | 'forgot' | 'register'
const forgotStep = ref(1); // 1: request code, 2: enter code + new password

const form = reactive({
  email: '',
  password: ''
});

const forgot = reactive({
  email: '',
  code: '',
  password: '',
  password_confirmation: ''
});

const register = reactive({
  name: '',
  email: '',
  password: '',
  user_type: 'U' // default Cliente
});

const loading = ref(false);
const forgotLoading = ref(false);
const loadingRegister = ref(false);
const error = ref('');

const toast = reactive({ show: false, message: '', type: 'info', duration: 3500 });
let toastTimer = 0;

function showToast(message, type = 'info', duration = 3500) {
  toast.message = message;
  toast.type = type;
  toast.duration = duration;
  toast.show = true;
  if (toastTimer) window.clearTimeout(toastTimer);
  toastTimer = window.setTimeout(() => (toast.show = false), duration);
}

async function submit() {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.post('/users/login', { ...form });
    const token = res.data.token;
    const user = res.data.user;
    if (token) {
      localStorage.setItem('auth_token', token);
      if (user) localStorage.setItem('user', JSON.stringify(user));
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
    window.location.href = '/';
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao efetuar login';
  } finally {
    loading.value = false;
  }
}

async function sendForgotEmail() {
  forgotLoading.value = true;
  try {
    const emailRe = /^\S+@\S+\.\S+$/;
    if (!emailRe.test(forgot.email)) {
      showToast('Informe um e-mail válido.', 'error');
      return;
    }
    await api.post('/users/password/forgot', { email: forgot.email });
    showToast('Código de recuperação enviado para seu e-mail.', 'success');
    forgotStep.value = 2;
  } catch (err) {
    const backendErrors = err.response?.data?.errors;
    if (backendErrors) {
      const firstField = Object.keys(backendErrors)[0];
      showToast(backendErrors[firstField][0] || 'Erro ao solicitar o código.', 'error');
    } else {
      const msg = err.response?.data?.message || 'Erro ao solicitar o código.';
      showToast(msg, 'error');
    }
  } finally {
    forgotLoading.value = false;
  }
}

async function submitResetWithCode() {
  forgotLoading.value = true;
  try {
    const payload = {
      email: forgot.email,
      code: forgot.code,
      password: forgot.password,
      password_confirmation: forgot.password_confirmation
    };
    const res = await api.post('/users/password/reset', payload);
    showToast(res.data?.message || 'Senha atualizada com sucesso.', 'success');
    // after success, go back to login and prefill email
    mode.value = 'login';
    form.email = forgot.email;
    // reset local state
    forgot.email = '';
    forgot.code = '';
    forgot.password = '';
    forgot.password_confirmation = '';
    forgotStep.value = 1;
  } catch (err) {
    const backendErrors = err.response?.data?.errors;
    if (backendErrors) {
      const firstField = Object.keys(backendErrors)[0];
      showToast(backendErrors[firstField][0] || 'Erro ao resetar a senha.', 'error');
    } else {
      const msg = err.response?.data?.message || 'Erro ao resetar a senha.';
      showToast(msg, 'error');
    }
  } finally {
    forgotLoading.value = false;
  }
}

async function submitRegister() {
  loadingRegister.value = true;
  error.value = '';
  try {
    if (!register.user_type) throw new Error('Selecione o tipo de usuário');
    if (!register.name || !register.name.trim()) throw new Error('Informe seu nome');
    const emailRe = /^\S+@\S+\.\S+$/;
    if (!emailRe.test(register.email)) throw new Error('Informe um e-mail válido');
    if (!register.password || register.password.length < 8) throw new Error('A senha deve ter no mínimo 8 caracteres');

    const payload = {
      name: register.name,
      email: register.email,
      password: register.password,
      user_type: register.user_type,
    };

    const res = await api.post('/users/register', payload);
    if (res.data?.success) {
      mode.value = 'login';
      form.email = register.email;
    } else {
      throw new Error(res.data?.error || 'Erro ao cadastrar');
    }
  } catch (err) {
    const backendErrors = err.response?.data?.errors;
    if (backendErrors) {
      const firstField = Object.keys(backendErrors)[0];
      error.value = backendErrors[firstField][0];
    } else {
      error.value = err.response?.data?.message || err.message || 'Erro ao cadastrar';
    }
  } finally {
    loadingRegister.value = false;
  }
}
</script>

<style scoped>
.grid { min-height: 100vh; }

.login-title {
  font-family: 'Roboto Slab', serif;
  font-weight: 500;
  font-size: 24px;
  line-height: 100%;
  text-align: center;
}

/* Header logo - fade only */
.header-logo { transition: opacity 300ms ease; will-change: opacity; }
.mode-register .header-logo, .mode-register .login-area .login-title { opacity: 0; pointer-events: none; }

.login-button {
  font-family: 'Roboto Slab', serif;
  font-weight: 500;
  font-size: 16px;
  line-height: 100%;
  text-align: center;
  width: 100%;
  max-width: 340px;
  height: 56px;
  border-radius: 10px;
  display: block;
  margin-left: auto; margin-right: auto;
  background-color: #ff9000;
  background-image: linear-gradient(180deg, #ffb23a, #ff9000);
  box-shadow: 0 10px 18px rgba(255, 144, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.25);
  transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
}
.login-button:hover { transform: translateY(-1px); box-shadow: 0 14px 22px rgba(255, 144, 0, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.3); filter: saturate(1.05); }
.login-button:active { transform: translateY(0); box-shadow: 0 8px 14px rgba(255, 144, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.22); }

.login-link, .forgot-link {
  font-family: 'Roboto Slab', serif;
  font-weight: 400;
  font-size: 16px;
  line-height: 100%;
  text-align: center;
}
.forgot-link { color: #f4ede8; }

/* Inputs */
.login-input { width: 100%; max-width: 340px; height: 56px; display: block; margin: 0 auto; border: 1px solid rgba(255,255,255,0.08); background-color: #2a2230; background-image: linear-gradient(180deg, rgba(40,34,45,0.9), rgba(35,28,40,0.9)); transition: border-color .2s, box-shadow .2s, background .2s; }
.login-input::placeholder { color: rgba(229,231,235,0.6); }
.input-label { width: 100%; max-width: 340px; margin: 0 auto; display: block; }
.input-icon { left: 12px; color: #666360; pointer-events: none; z-index: 10; position: absolute; }
.input-label:focus-within .login-input { border-color: rgba(255,144,0,0.55); box-shadow: 0 0 0 3px rgba(255,144,0,0.18); }
.input-label:focus-within .input-icon { color: #FF9000; }

/* Background layer behind forms */
.auth-bg { position: absolute; inset: 0; background-position: center; background-repeat: no-repeat; background-size: cover; opacity: 0.08; pointer-events: none; z-index: 0; }

/* Card visuals */
.form-area { padding: 16px 12px 24px; border-radius: 16px; min-height: 640px; overflow-anchor: none; background:
  radial-gradient(1200px 400px at 20% -10%, rgba(255, 144, 0, 0.07), transparent 50%),
  radial-gradient(1000px 300px at 120% 10%, rgba(255, 144, 0, 0.06), transparent 60%),
  rgba(36, 29, 38, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.06);
  box-shadow: 0 10px 30px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.04);
}

/* Switch */
.segmented-switch { position: relative; display: grid; grid-template-columns: 1fr 1fr; align-items: center; width: 100%; max-width: 340px; height: 56px; margin: 0 auto 8px; background: rgba(42,34,48,0.9); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; box-shadow: 0 10px 18px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.06); overflow: hidden; }
.segmented-thumb { position: absolute; top: 6px; left: 6px; width: calc(50% - 12px); height: calc(100% - 12px); background: linear-gradient(180deg, rgba(255, 178, 58, 0.18), rgba(255, 144, 0, 0.18)); border: 1px solid rgba(255, 144, 0, 0.45); border-radius: 10px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.15), 0 6px 14px rgba(255,144,0,0.16); transition: transform 220ms cubic-bezier(0.2, 0.8, 0.2, 1), background 150ms ease, border-color 150ms ease; will-change: transform; }
.segmented-switch.pos-none .segmented-thumb { opacity: 0; }
.segmented-switch.has-value .segmented-thumb { opacity: 1; transition: transform 220ms cubic-bezier(0.2, 0.8, 0.2, 1), opacity 140ms ease; }
.segmented-switch.pos-u .segmented-thumb { transform: translateX(100%); }
.segmented-option { position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 8px; height: 100%; cursor: pointer; user-select: none; color: #cfcbd4; transition: color 120ms ease, filter 120ms ease; }
.segmented-option:hover { color: #ffffff; filter: saturate(1.02); }
.segmented-label { display: inline-flex; align-items: center; gap: 8px; font-family: 'Roboto Slab', serif; font-weight: 500; font-size: 14px; }
.segmented-option svg { color: currentColor; }
.pos-b .segmented-option:first-of-type .segmented-label, .pos-u .segmented-option:last-of-type .segmented-label { color: #ffffff; }

/* Fade only for inner content swaps */
.content-fade-enter-active, .content-fade-leave-active { transition: opacity 220ms ease; }
.content-fade-enter-from, .content-fade-leave-to { opacity: 0; }

/* Mobile tweaks */
@media (max-width: 767px) {
  .form-panel { gap: 12px; }
  .form-forgot .login-link, .form-register .login-link { margin-top: 24px !important; }
  .input-label, .login-input, .segmented-switch { width: 100% !important; }
  .login-input { height: 52px; }
  .form-area { min-height: auto; }
}
</style>

