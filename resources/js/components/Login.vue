<template>
    <div class="min-h-screen bg-[#312E38] flex items-center justify-center px-4 overflow-hidden">
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6 items-center transition-container" :class="{ 'mode-register': mode==='register' }">
            <!-- left: card -->
            <div class="max-w-md w-full mx-auto">
                <div class="mx-auto mb-6 text-center">
                    <img :src="logoImage" alt="Barbeia.ai logo" class="mx-auto w-40 h-auto header-logo" />
                </div>

                <div class="form-area relative login-area">
                    <h2 class="text-white text-center mb-6 login-title" style="margin-top: 80px;">Faça seu login</h2>
                    <!-- LOGIN FORM -->
                    <form v-show="mode==='login'" @submit.prevent="submit" class="flex flex-col gap-4 form-panel form-login">
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

                            <button type="button" @click="mode='register'" class="flex items-center gap-2 login-link text-[#FF9000] hover:underline transition-colors">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5 2.5L15.8333 2.5C16.2754 2.5 16.6993 2.6756 17.0118 2.98816C17.3244 3.30072 17.5 3.72464 17.5 4.16667L17.5 15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5L12.5 17.5" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.33334 14.1667L12.5 10L8.33334 5.83335" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12.5 10L2.5 10" stroke="#FF9000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Criar conta
                            </button>

                        </div>
                    </form>

                    <p v-if="error && mode==='login'" class="text-sm text-red-400 mt-4 text-center">{{ error }}</p>

                    <!-- Registro image moved inside the login box so it can slide into the same area -->
                    <img src="/resources/images/registro.png" alt="register-slide" class="left-slide-image" />

                </div>
            </div>

            <!-- right: image (hidden on small screens) -->
            <div class="hidden md:flex items-center justify-center h-screen relative image-register-wrapper">
                <img :src="localImage" alt="barber" class="shadow-lg w-full h-screen object-cover image-side" />
                <!-- REGISTER PANEL ON RIGHT -->
                <div class="register-panel absolute top-0 right-0 h-full flex items-center justify-center w-[60%] max-w-xl px-10" :class="{ 'active': mode==='register' }">
                    <form v-if="mode==='register'" @submit.prevent="submitRegister" class="flex flex-col gap-4 w-full">
                        <!-- Logo above register form (appears only in register mode) -->
                        <div class="text-center mb-4 register-logo-wrap">
                            <img :src="logoImage" alt="Barbeia.ai logo" class="mx-auto w-40 h-auto register-logo" />
                        </div>
                        <h2 class="text-white text-center mb-6 login-title">Crie sua conta</h2>
                        <label class="relative block input-label">
                            <span class="sr-only">Tipo de usuário</span>
                            <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-3-3.87"/><path d="M4 21v-2a4 4 0 0 1 3-3.87"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <select v-model="register.role" class="w-full pl-10 pr-3 py-3 rounded-lg bg-[#241d26] text-gray-200 outline-none focus:ring-2 focus:ring-amber-500 login-input">
                                <option value="">Selecione...</option>
                                <option value="barber">Barbeiro</option>
                                <option value="client">Cliente</option>
                            </select>
                        </label>
                        <label class="relative block input-label">
                            <span class="sr-only">Nome</span>
                            <div class="absolute inset-y-0 left-3 flex items-center text-gray-500 input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666360" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5Z"/><path d="M3.2 20.2c.8-3.4 4-6.2 8.8-6.2s8 2.8 8.8 6.2"/></svg>
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
                            <button :disabled="loadingRegister" class="mt-1 bg-amber-500 text-[#2b2430] login-button py-3 rounded-lg shadow-md">Cadastrar</button>
                            <button type="button" @click="mode='login'" class="text-sm text-gray-300 hover:underline">Voltar para login</button>
                            <p v-if="error && mode==='register'" class="text-sm text-red-400 text-center">{{ error }}</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { reactive, ref } from 'vue';
    import api from '../services/api';

    // Import local image via Vite so it's included in the build
    import localImgUrl from '/resources/images/Imagem.png?url';
    import logoUrl from '/resources/images/logo.png?url';

    const localImage = localImgUrl;
    const logoImage = logoUrl;

    const form = reactive({ email: '', password: '' });
    const register = reactive({ role: '', name: '', email: '', password: '' });
    const mode = ref('login');
    const loadingRegister = ref(false);
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

    async function submitRegister() {
        loadingRegister.value = true;
        error.value = '';
        try {
            // Ajuste o endpoint conforme backend real
            const res = await api.post('/users/register', { ...register });
            // Se quiser já logar após cadastro:
            if (res.data?.token) {
                localStorage.setItem('auth_token', res.data.token);
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`;
                window.location.href = '/dashboard';
            } else {
                // Caso contrário volta para login
                mode.value = 'login';
                form.email = register.email;
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Erro ao cadastrar';
        } finally {
            loadingRegister.value = false;
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
    /* leading-trim removido (experimental e não suportado) */
        line-height: 100%;
        letter-spacing: 0%;
        text-align: center;
    }

    /* Header logo - allow smooth fade/slide when switching to register mode */
    .header-logo {
        transition: opacity .5s ease, transform .6s ease;
        will-change: opacity, transform;
    }

    /* hide logo and login title when the register image/panel is active */
    .mode-register .header-logo,
    .mode-register .login-title {
        opacity: 0;
        transform: translateY(-8px);
        pointer-events: none;
    }

    /* Logo shown above the register form */
    .register-logo {
        opacity: 0;
        transform: translateY(-6px);
        transition: opacity .5s ease .05s, transform .55s cubic-bezier(.22,.9,.3,1) .05s;
    }
    .mode-register .register-logo {
        opacity: 1;
        transform: translateY(0);
    }

    .login-button {
        font-family: 'Roboto Slab', serif;
        font-weight: 500;
        font-size: 16px;
    /* leading-trim removido (experimental e não suportado) */
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
    /* leading-trim removido (experimental e não suportado) */
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

    /* Panels animation */
    .form-area {
        min-height: 640px; /* reserve space for smooth swap */
    }
    .form-panel {
        transition: opacity .5s ease, transform .6s ease;
        will-change: opacity, transform;
    }
    .form-login {
        position: relative;
        z-index: 2;
        transition: opacity .5s ease;
    }
    .mode-register .form-login {
        opacity: 0;
        pointer-events: none;
    }
    .transition-container {
        position: relative;
        transition: transform .7s ease;
    }
    .image-register-wrapper {
        overflow: hidden;
        transition: transform .8s cubic-bezier(.65,.05,.36,1), width .4s ease;
        will-change: transform;
    }
    .image-side {
        transition: opacity .6s ease, transform .8s cubic-bezier(.65,.05,.36,1);
        transform-origin: center;
        transform: translateX(0);
        will-change: transform, opacity;
    }
    .mode-register .image-register-wrapper {
        z-index: 40;
        position: relative; 
    }
    .mode-register .image-register-wrapper .image-side {
        /* slide the image fully to the left inside its wrapper (wrapper has overflow:hidden) */
        transform: translateX(100%);
        opacity: 0.98;
        object-fit: cover;
        z-index: 50; /* keep image above left column */
        position: relative;
    }
    /* Left slide-in image that comes from the left edge when registering */
    .left-slide-image {
        position: fixed;
        left: -100vw;
        top: 0;
        height: 100vh; 
        object-fit: cover;
        z-index: 55; 
        transform: translateX(0);
        transition: transform .7s cubic-bezier(.22,.9,.3,1), left .7s cubic-bezier(.22,.9,.3,1);
    }
    .mode-register .left-slide-image {
        /* when registering, slide the image into the left:0 position smoothly */
        left: 0;
        right: auto;
        transform: translateX(0);
    }
    .login-area { position: relative; z-index: 1; transition: opacity .4s ease; }
    /* Dim the login area but keep it visible under the image overlay */
    .mode-register .login-area { opacity: 0.55; pointer-events: none; }
    .register-panel {
        backdrop-filter: blur(6px);
        transform: translateX(-100%);
        opacity: 0;
        transition: transform .6s cubic-bezier(.22,.9,.3,1), opacity .45s ease .05s;
        z-index: 60; 
    }
    .register-panel.active {
        transform: translateX(-30%);
        opacity: 1;
    }
    /* Mobile: image hidden, so ensure register panel fades cleanly */
    @media (max-width: 767px) {
        .form-register { transform: translateX(0); }
        .transition-container.mode-register { transform: none; }
        .form-area { min-height: auto; padding-bottom: 40px; }
        .login-button { width: 100%; }
        .register-panel {
            position: static;
            width: 100%;
            max-width: none;
            background: transparent;
            backdrop-filter: none;
            box-shadow: none;
            transform: none !important;
            opacity: 1 !important;
            padding: 0;
        }
        .image-side { display: none; }
    }
</style>