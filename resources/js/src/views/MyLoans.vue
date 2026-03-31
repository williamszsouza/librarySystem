<template>
  <div class="h-full flex flex-col bg-slate-50">

    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm">
      <div>
        <h1 class="text-lg font-bold text-slate-800">Meus Empréstimos</h1>
        <p class="text-xs text-slate-400 mt-0.5">Acompanhe os livros que você está com emprestado</p>
      </div>
      <button
        @click="fetchMyLoans"
        class="p-2 text-slate-400 hover:text-blue-600 transition-colors"
        title="Atualizar dados"
      >
        <svg class="w-5 h-5" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </header>

    <!-- Cards de stats -->
    <section class="px-6 py-6 grid grid-cols-1 md:grid-cols-3 gap-4">

      <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold text-blue-500 uppercase">Emprestados</p>
          <span class="p-1.5 bg-blue-50 rounded-lg text-blue-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </span>
        </div>
        <p class="text-2xl font-bold text-blue-600 mt-2">{{ stats.active }}</p>
      </div>

      <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold text-slate-400 uppercase">Limite disponível</p>
          <span class="p-1.5 bg-slate-50 rounded-lg text-slate-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
        </div>
        <p class="text-2xl font-bold text-slate-800 mt-2">{{ 3 - stats.active }} de 3</p>
      </div>

      <div
        class="bg-white p-5 rounded-xl border shadow-sm transition-all hover:shadow-md"
        :class="stats.overdue > 0 ? 'border-red-200 bg-red-50/30' : 'border-slate-200'"
      >
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold uppercase" :class="stats.overdue > 0 ? 'text-red-500' : 'text-slate-400'">Atrasados</p>
          <span class="p-1.5 rounded-lg" :class="stats.overdue > 0 ? 'bg-red-100 text-red-500' : 'bg-slate-50 text-slate-400'">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
        </div>
        <p class="text-2xl font-bold mt-2" :class="stats.overdue > 0 ? 'text-red-600' : 'text-slate-800'">{{ stats.overdue }}</p>
      </div>

    </section>

    <main class="flex-1 overflow-y-auto px-6 pb-6">

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="flex flex-col items-center gap-2">
          <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          <p class="text-slate-500 font-medium text-sm">Carregando empréstimos...</p>
        </div>
      </div>

      <!-- Empty state -->
      <div
        v-else-if="loans.length === 0"
        class="flex flex-col items-center justify-center h-64 bg-white border border-dashed border-slate-300 rounded-2xl"
      >
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <p class="text-slate-500 font-medium text-lg">Nenhum empréstimo ativo</p>
        <p class="text-slate-400 text-sm mb-4">Você não possui livros emprestados no momento.</p>
        <router-link
          to="/books"
          class="text-xs font-semibold text-blue-600 hover:underline"
        >
          Ver acervo de livros
        </router-link>
      </div>

      <!-- Tabela -->
      <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-600">
          <thead class="bg-slate-50/50 border-b border-slate-200 text-slate-500 uppercase text-[11px] tracking-widest font-bold">
            <tr>
              <th class="px-6 py-4">ID</th>
              <th class="px-6 py-4 text-slate-800">Livro</th>
              <th class="px-6 py-4">Empréstimo</th>
              <th class="px-6 py-4">Vencimento</th>
              <th class="px-6 py-4 text-center">Status</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="loan in loans"
              :key="loan.id"
              class="hover:bg-slate-50/80 transition-colors"
            >
              <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ loan.id }}</td>

              <td class="px-6 py-4 text-slate-700 font-medium">{{ loan.livro }}</td>

              <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                {{ loan.data_emprestimo }}
              </td>

              <td class="px-6 py-4 font-semibold whitespace-nowrap" :class="loan.atrasado ? 'text-red-600' : 'text-slate-700'">
                {{ loan.data_vencimento }}
              </td>

              <td class="px-6 py-4 text-center">
                <span
                  v-if="loan.atrasado"
                  class="px-3 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-600 border border-red-200 uppercase tracking-tight"
                >
                  Atrasado
                </span>
                <span
                  v-else
                  class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600 border border-emerald-200 uppercase tracking-tight"
                >
                  No Prazo
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'

const loans   = ref([])
const loading = ref(true)

// Derivado da lista — sem fetch extra
const stats = computed(() => ({
  active:  loans.value.length,
  overdue: loans.value.filter(l => l.atrasado).length,
}))

const coverGradient = (title = '') => {
  const hue = [...title].reduce((acc, c) => acc + c.charCodeAt(0), 0) % 360
  return `linear-gradient(135deg, hsl(${hue}, 55%, 45%), hsl(${(hue + 40) % 360}, 60%, 35%))`
}

const fetchMyLoans = async () => {
  loading.value = true
  try {
    const { data } = await axios.post('/api/loans/my')
    loans.value = Array.isArray(data) ? data : []
  } catch (error) {
    loans.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchMyLoans())
</script>