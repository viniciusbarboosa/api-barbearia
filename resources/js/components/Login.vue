<template>
    <div class="min-h-screen bg-[#2b2430] flex items-center justify-center px-4">
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <!-- left: card -->
            <div class="max-w-md w-full mx-auto">
                <div class="mx-auto mb-6 text-center">
                    <div class="text-gray-300 text-sm">Awesome</div>
                    <h1 class="text-4xl font-extrabold tracking-widest text-white mt-2">Barbeia.ai</h1>
                    <div class="h-1 w-28 bg-amber-500 mx-auto my-3"></div>
                    <div class="text-gray-400 text-sm">Haircuts & Shaves</div>
                </div>

                <div class="form-area">
                    <h2 class="text-white text-center mb-6 login-title" style="margin-top: 80px;">Faça seu login</h2>

                    <form @submit.prevent="submit" class="flex flex-col gap-4">
                        <label class="relative block input-label">
                            <span class="sr-only">E-mail</span>
                            <div class="absolute inset-y-0 left-3 flex items-center text-blue-500 input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18"
                                    fill="none">
                                    <path
                                        d="M3.6 2.9519H16.4C17.28 2.9519 18 3.6323 18 4.4639V13.5359C18 14.3675 17.28 15.0479 16.4 15.0479H3.6C2.72 15.0479 2 14.3675 2 13.5359V4.4639C2 3.6323 2.72 2.9519 3.6 2.9519Z"
                                        stroke="#666360" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M18 5.04001L10 10.08L2 5.04001" stroke="#666360" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </div>
                            <input v-model="form.email" type="email" placeholder="E-mail"
                                class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input email-input"
                                required />
                        </label>

                        <label class="relative block input-label">
                            <span class="sr-only">Senha</span>
                            <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                                <svg width="20" height="19" viewBox="0 0 20 19" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_34_1204)">
                                        <path
                                            d="M15.4444 8.17145H4.55556C3.69645 8.17145 3 8.90407 3 9.80781V15.5351C3 16.4388 3.69645 17.1714 4.55556 17.1714H15.4444C16.3036 17.1714 17 16.4388 17 15.5351V9.80781C17 8.90407 16.3036 8.17145 15.4444 8.17145Z"
                                            stroke="#666360" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M6 8.17144V4.97144C6 3.91057 6.42143 2.89315 7.17157 2.14301C7.92172 1.39286 8.93913 0.971436 10 0.971436C11.0609 0.971436 12.0783 1.39286 12.8284 2.14301C13.5786 2.89315 14 3.91057 14 4.97144V8.17144"
                                            stroke="#666360" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_34_1204">
                                            <rect width="20" height="18" fill="white"
                                                transform="translate(0 0.0714417)" />
                                        </clipPath>
                                    </defs>
                                </svg>

                            </div>
                            <input v-model="form.password" type="password" placeholder="Senha"
                                class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 placeholder-gray-500 outline-none focus:ring-2 focus:ring-amber-500 login-input password-input"
                                required />
                        </label>

                        <button :disabled="loading"
                            class="mt-3 bg-amber-500 text-[#2b2430] login-button py-3 rounded-lg shadow-md">Entrar</button>

                        <div class="flex flex-col gap-2 mt-3 text-sm items-center text-center">
                            <a href="#" class="hover:underline forgot-link" style="margin-bottom: 80px;">Esqueci minha
                                senha</a>

                            <span class="flex items-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12.5 2.5L15.8333 2.5C16.2754 2.5 16.6993 2.6756 17.0118 2.98816C17.3244 3.30072 17.5 3.72464 17.5 4.16667L17.5 15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5L12.5 17.5"
                                        stroke="#FF9000" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M8.33334 14.1667L12.5 10L8.33334 5.83335" stroke="#FF9000"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12.5 10L2.5 10" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <a href="/register" class="login-link" style="color: #FF9000;">Criar conta</a>
                            </span>

                        </div>
                    </form>

                    <p v-if="error" class="text-sm text-red-400 mt-4 text-center">{{ error }}</p>
                </div>
            </div>

            <!-- right: image (hidden on small screens) -->
            <div class="hidden md:flex items-center justify-center h-screen">
                <img :src="localImage" alt="barber" class="shadow-lg w-full h-screen object-cover" />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { reactive, ref } from 'vue';
    import api from '../services/api';

    // Import local image via Vite so it's included in the build
    import localImgUrl from '/resources/images/Imagem.png?url';
    const localImage = localImgUrl;

    const form = reactive({ email: '', password: '' });
    const loading = ref(false);
    const error = ref('');

    async function submit() {
        loading.value = true;
        error.value = '';
        try {
            const res = await api.post('/users/login', { ...form });
            const token = res.data.token;
            if (token) {
                localStorage.setItem('auth_token', token);
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
            window.location.href = '/dashboard';
        } catch (err) {
            error.value = err.response?.data?.message || 'Erro ao efetuar login';
        } finally {
            loading.value = false;
        }
    }
</script>

<style scoped>
    .bg-cover {
        background-size: cover;
    }

    .grid {
        min-height: 100vh;
    }

    .login-title {
        font-family: 'Roboto Slab', serif;
        font-weight: 500;
        font-size: 24px;
        /* leading-trim is experimental; included because requested */
        leading-trim: none;
        line-height: 100%;
        letter-spacing: 0%;
        text-align: center;
    }

    .login-button {
        font-family: 'Roboto Slab', serif;
        font-weight: 500;
        font-size: 16px;
        /* leading-trim is experimental; included because requested */
        leading-trim: none;
        line-height: 100%;
        letter-spacing: 0%;
        text-align: center;
        width: 340px;
        height: 56px;
        opacity: 1;
        border-radius: 10px;
        transform: rotate(0deg);
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .login-link,
    .forgot-link {
        font-family: 'Roboto Slab', serif;
        font-weight: 400;
        font-size: 16px;
        /* leading-trim is experimental; included because requested */
        leading-trim: none;
        line-height: 100%;
        letter-spacing: 0%;
        text-align: center;
    }

    .forgot-link {
        color: #F4EDE8;
    }

    /* Container to allow absolute positioning relative to the form area */
    .form-area {
        position: relative;
    }

    .login-input {
        width: 340px;
        height: 56px;
        opacity: 1;
        transform: rotate(0deg);
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    /* Ensure the label that wraps input + icon is same width as input and centered */
    .input-label {
        width: 340px;
        margin-left: auto;
        margin-right: auto;
        display: block;
    }

    /* Icon inside label should be positioned relative to the label container */
    .input-icon {
        left: 12px;
        /* small offset inside the 340px label */
        color: #666360;
        pointer-events: none;
        z-index: 10;
        position: absolute;
    }

    /* Slightly lower the icon for the second input (password) to visually center it */
    .input-label:nth-of-type(2) .input-icon {
        transform: translateY(2px);
    }

    /* Force SVGs to inherit color and be visible */
    .input-icon svg {
        display: block;
        width: 20px;
        height: 20px;
        color: inherit;
        /* ensure strokes use current text color */
        stroke: currentColor !important;
        fill: none !important;
    }

    /* Also target paths/groups inside for extra safety */
    .input-icon svg path,
    .input-icon svg g {
        stroke: currentColor !important;
        fill: none !important;
    }

    /* If explicit top offsets are required, set them here (relative to .form-area)
       These mirror the values you provided; adjust if you want different spacing.
    */
    .email-input {
        position: relative;
        /* keep document flow, top offset via margin */
        margin-top: 0px;
        /* aligns with form flow */
    }

    .password-input {
        position: relative;
        margin-top: 8px;
        /* small gap from email input; adjust as needed */
    }
</style>