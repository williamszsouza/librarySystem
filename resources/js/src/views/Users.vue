<template>
  <div class="h-full flex flex-col">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm">
      <div>
        <h1 class="text-lg font-bold text-slate-800">Gerenciamento de Usuários</h1>
        <p class="text-xs text-slate-400 mt-0.5">Visualize e remova contas do sistema</p>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6">
      
      <div v-if="loading" class="flex justify-center items-center h-32">
        <p class="text-slate-500 font-medium animate-pulse">Carregando usuários...</p>
      </div>

      <div v-else-if="users.length === 0" class="flex flex-col items-center justify-center h-48 bg-white border border-dashed border-slate-300 rounded-xl">
        <p class="text-slate-500 font-medium">Nenhum usuário cadastrado no momento.</p>
      </div>

      <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-600">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-800">
            <tr>
              <th class="px-6 py-4 font-semibold">ID</th>
              <th class="px-6 py-4 font-semibold">Nome</th>
              <th class="px-6 py-4 font-semibold">E-mail</th>
              <th class="px-6 py-4 font-semibold">Data de Cadastro</th>
              <th class="px-6 py-4 font-semibold text-right">Ações</th>
            </tr>
          </thead>
          
          <tbody class="divide-y divide-slate-100">
            <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-4 font-medium text-slate-500">#{{ user.id }}</td>
              <td class="px-6 py-4 font-medium text-slate-800 capitalize">{{ user.name }}</td>
              <td class="px-6 py-4 text-slate-700">{{ user.email }}</td>
              <td class="px-6 py-4 text-slate-500">{{ formatDate(user.created_at) }}</td>
              
              <td class="px-6 py-4 text-right">
                <button 
                  @click="deleteUser(user.id)"
                  class="text-sm font-semibold text-red-600 hover:text-red-800 transition-colors bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 hover:bg-red-100"
                >
                  Excluir Conta
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const users = ref([]);
const loading = ref(true);

const fetchUsers = async () => {
  try {
    const response = await axios.get('/api/users');
    users.value = response.data; 
  } catch (error) {
    console.error("Erro ao buscar usuários:", error);
    toast.error('Erro ao carregar a lista de usuários.');
  } finally {
    loading.value = false;
  }
};

const deleteUser = async (userId) => {
  if (!confirm('Tem certeza que deseja BANIR este usuário? Esta ação apagará a conta dele.')) return;
  
  try {
    await axios.delete(`/api/users/${userId}`);
    
    toast.success('Usuário excluído com sucesso!');
    users.value = users.value.filter(user => user.id !== userId);
    
  } catch (error) {
    console.error("Erro ao excluir usuário:", error);
    toast.error(error.response?.data?.message || 'Erro ao remover conta.');
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('pt-BR');
};

onMounted(() => {
  fetchUsers();
});
</script>