<template>
  <div class="h-full flex flex-col bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm">
      <div>
        <h1 class="text-lg font-bold text-slate-800">Painel de Controle</h1>
        <p class="text-xs text-slate-400 mt-0.5">Gestão de acervo e monitoramento de prazos</p>
      </div>
      <div class="flex items-center gap-3">
        <button 
          @click="fetchAllData" 
          class="p-2 text-slate-400 hover:text-blue-600 transition-colors"
          title="Atualizar dados"
        >
          <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </header>

    <section class="px-6 py-6 grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold text-slate-400 uppercase">Total no Acervo</p>
          <span class="p-1.5 bg-slate-50 rounded-lg text-slate-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
          </span>
        </div>
        <p class="text-2xl font-bold text-slate-800 mt-2">{{ stats.total_books }}</p>
      </div>

      <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold text-emerald-500 uppercase">Disponíveis</p>
          <span class="p-1.5 bg-emerald-50 rounded-lg text-emerald-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </span>
        </div>
        <p class="text-2xl font-bold text-emerald-600 mt-2">{{ stats.available_books }}</p>
      </div>

      <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold text-blue-500 uppercase">Emprestados</p>
          <span class="p-1.5 bg-blue-50 rounded-lg text-blue-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
          </span>
        </div>
        <p class="text-2xl font-bold text-blue-600 mt-2">{{ stats.active_loans }}</p>
      </div>

      <div class="bg-white p-5 rounded-xl border shadow-sm transition-all hover:shadow-md" 
           :class="stats.overdue_loans > 0 ? 'border-red-200 bg-red-50/30' : 'border-slate-200'">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold uppercase" :class="stats.overdue_loans > 0 ? 'text-red-500' : 'text-slate-400'">Atrasados</p>
          <span class="p-1.5 rounded-lg" :class="stats.overdue_loans > 0 ? 'bg-red-100 text-red-500' : 'bg-slate-50 text-slate-400'">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </span>
        </div>
        <p class="text-2xl font-bold mt-2" :class="stats.overdue_loans > 0 ? 'text-red-600' : 'text-slate-800'">{{ stats.overdue_loans }}</p>
      </div>
    </section>

    <main class="flex-1 overflow-y-auto px-6 pb-6">
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="flex flex-col items-center gap-2">
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          <p class="text-slate-500 font-medium text-sm">Sincronizando dados...</p>
        </div>
      </div>

      <div v-else-if="loans.length === 0" class="flex flex-col items-center justify-center h-64 bg-white border border-dashed border-slate-300 rounded-2xl">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
        </div>
        <p class="text-slate-500 font-medium text-lg">Nenhum empréstimo ativo</p>
        <p class="text-slate-400 text-sm">O acervo está totalmente organizado no momento.</p>
      </div>

      <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-600">
          <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-500 uppercase text-[11px] tracking-widest font-bold">
            <tr>
              <th class="px-6 py-4">ID</th>
              <th class="px-6 py-4 text-slate-800">Usuário</th>
              <th class="px-6 py-4 text-slate-800">Livro</th>
              <th class="px-6 py-4">Empréstimo</th>
              <th class="px-6 py-4">Vencimento</th>
              <th class="px-6 py-4 text-center">Status</th>
              <th class="px-6 py-4 text-right">Ações</th>
            </tr>
          </thead>
          
          <tbody class="divide-y divide-slate-100">
            <tr v-for="loan in loans" :key="loan.id" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ loan.id }}</td>
              
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-[10px]">
                    {{ loan.usuario.charAt(0) }}
                  </div>
                  <span class="font-semibold text-slate-700 capitalize">{{ loan.usuario }}</span>
                </div>
              </td>
              
              <td class="px-6 py-4 text-slate-600 font-medium">{{ loan.livro }}</td>
              
              <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                {{ loan.data_emprestimo }}
              </td>
              
              <td class="px-6 py-4 font-semibold text-slate-700 whitespace-nowrap">
                {{ loan.data_vencimento }}
              </td>
              
              <td class="px-6 py-4 text-center">
                <span v-if="loan.atrasado" class="px-3 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-600 border border-red-200 uppercase tracking-tight">
                  Atrasado
                </span>
                <span v-else class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600 border border-emerald-200 uppercase tracking-tight">
                  No Prazo
                </span>
              </td>
              
              <td class="px-6 py-4 text-right">
                <button 
                  @click="returnLoan(loan.id)"
                  class="text-xs font-bold text-blue-600 hover:text-white hover:bg-blue-600 transition-all border border-blue-600 px-4 py-2 rounded-lg"
                >
                  DEVOLVER
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

const loans = ref([]);
const loading = ref(true);
const stats = ref({
  total_books: 0,
  available_books: 0,
  active_loans: 0,
  overdue_loans: 0
});

const fetchAllData = async () => {
  loading.value = true;
  try {
    const [loansRes, statsRes] = await Promise.all([
      axios.get('/api/dashboard/loans'),
      axios.get('/api/dashboard/stats')
    ]);
    
    loans.value = loansRes.data;
    stats.value = statsRes.data;
  } catch (error) {
    console.error("Erro ao sincronizar dados:", error);
    toast.error('Erro na conexão com o servidor.');
  } finally {
    loading.value = false;
  }
};

const returnLoan = async (loanId) => {
  if (!confirm('Confirmar recebimento físico do livro e liberar no acervo?')) return;
  
  try {
    await axios.patch(`/api/loans/${loanId}/return`);
    toast.success('Devolução registrada com sucesso!');
    
    // Atualiza apenas localmente primeiro para ser instantâneo
    loans.value = loans.value.filter(loan => loan.id !== loanId);
    // Depois sincroniza os stats do topo
    await fetchAllData(); 
    
  } catch (error) {
    const msg = error.response?.data?.message || 'Falha ao processar devolução.';
    toast.error(msg);
  }
};

onMounted(fetchAllData);
</script>