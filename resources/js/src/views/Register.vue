<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';

const name      = ref('');
const email     = ref('');
const password  = ref('');
const confirmed = ref('');
const error     = ref('');
const loading   = ref(false);

const showPassword  = ref(false); // controla visibilidade da senha
const showConfirmed = ref(false); // controla visibilidade da confirmação

const router = useRouter();

const handleRegister = async () => {
    loading.value = true;
    error.value   = '';

    if (password.value !== confirmed.value) {
        loading.value = false;
        return error.value = 'Senha e confirmação de senha devem ser iguais.';
    }

    try {
        await axios.post('/api/register', {
            name:                  name.value,
            email:                 email.value,
            password:              password.value,
            password_confirmation: confirmed.value
        });

        toast.success('Conta criada com sucesso! Faça login para continuar.');
        router.push('/login');
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao criar conta.';
        toast.error(error.value);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-slate-100 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-center text-slate-800 mb-8">Livraria System</h2>

            <form @submit.prevent="handleRegister" class="space-y-6">

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nome</label>
                    <input
                        v-model="name"
                        type="text"
                        required
                        class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">E-mail</label>
                    <input
                        v-model="email"
                        type="email"
                        required
                        class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <!-- Senha com olhinho -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Senha</label>
                    <div class="relative mt-1">
                        <input
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            class="block w-full px-3 py-2 pr-10 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors"
                            tabindex="-1"
                        >
                            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Confirmação com olhinho -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Confirme sua senha</label>
                    <div class="relative mt-1">
                        <input
                            v-model="confirmed"
                            :type="showConfirmed ? 'text' : 'password'"
                            required
                            class="block w-full px-3 py-2 pr-10 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                        <button
                            type="button"
                            @click="showConfirmed = !showConfirmed"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600 transition-colors"
                            tabindex="-1"
                        >
                            <svg v-if="!showConfirmed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <p v-if="error" class="text-red-500 text-sm font-medium">{{ error }}</p>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                    {{ loading ? 'Criando conta...' : 'Registrar-se' }}
                </button>

                <p class="text-center text-sm text-slate-500">
                    Já tem conta?
                    <router-link to="/login" class="text-blue-600 hover:underline font-medium">
                        Fazer login
                    </router-link>
                </p>

            </form>
        </div>
    </div>
</template>