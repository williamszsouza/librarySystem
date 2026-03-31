<template>
  <div class="flex h-screen bg-slate-50 font-sans overflow-hidden">

    
    <!-- ─── Main Content ─── -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

      <!-- Header -->
      <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm">
        <div>
          <h1 class="text-lg font-bold text-slate-800">Painel de Empréstimos</h1>
          <p class="text-xs text-slate-400 mt-0.5">Gerencie todos os empréstimos ativos</p>
        </div>
        <button
          @click="openNewLoanModal"
          class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-medium px-4 py-2 rounded-lg transition-all duration-150 shadow-sm shadow-blue-200"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          <span class="hidden sm:inline">Novo Empréstimo</span>
        </button>
      </header>

      <!-- Scrollable area -->
      <main class="flex-1 overflow-y-auto p-6 space-y-6">

        <!-- ─── Stat Cards ─── -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Total -->
          <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
              </svg>
            </div>
            <div>
              <p class="text-xs text-slate-500 font-medium">Total de Empréstimos</p>
              <p class="text-2xl font-bold text-slate-800 mt-0.5">
                <span v-if="loading" class="inline-block w-12 h-6 bg-slate-100 rounded animate-pulse"></span>
                <span v-else>{{ stats.total }}</span>
              </p>
            </div>
          </div>

          <!-- Atrasados -->
          <div class="bg-white rounded-xl border border-red-50 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <p class="text-xs text-slate-500 font-medium">Livros Atrasados</p>
                <span v-if="!loading && stats.overdue > 0" class="text-xs font-semibold bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full">Atenção</span>
              </div>
              <p class="text-2xl font-bold text-slate-800 mt-0.5">
                <span v-if="loading" class="inline-block w-12 h-6 bg-slate-100 rounded animate-pulse"></span>
                <span v-else :class="stats.overdue > 0 ? 'text-red-600' : ''">{{ stats.overdue }}</span>
              </p>
            </div>
          </div>

          <!-- Disponíveis -->
          <div class="bg-white rounded-xl border border-emerald-50 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-xs text-slate-500 font-medium">Livros Disponíveis</p>
              <p class="text-2xl font-bold text-slate-800 mt-0.5">
                <span v-if="loading" class="inline-block w-12 h-6 bg-slate-100 rounded animate-pulse"></span>
                <span v-else class="text-emerald-600">{{ stats.available }}</span>
              </p>
            </div>
          </div>
        </div>

        <!-- ─── Table Card ─── -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">

          <!-- Table Header / Search -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <div>
              <h2 class="text-sm font-semibold text-slate-700">Empréstimos Ativos</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ filteredLoans.length }} registro(s) encontrado(s)</p>
            </div>
            <!-- Search -->
            <div class="relative w-full sm:w-64">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar livro ou usuário..."
                class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg placeholder-slate-400 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
              />
            </div>
          </div>

          <!-- Loading Skeleton -->
          <div v-if="loading" class="divide-y divide-slate-100">
            <div v-for="n in 4" :key="n" class="px-5 py-4 flex items-center gap-4 animate-pulse">
              <div class="w-8 h-8 bg-slate-100 rounded-lg shrink-0"></div>
              <div class="flex-1 space-y-2">
                <div class="h-3 bg-slate-100 rounded w-1/3"></div>
                <div class="h-2.5 bg-slate-100 rounded w-1/4"></div>
              </div>
              <div class="hidden sm:block w-24 h-3 bg-slate-100 rounded"></div>
              <div class="hidden md:block w-20 h-5 bg-slate-100 rounded-full"></div>
              <div class="w-28 h-7 bg-slate-100 rounded-lg"></div>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-else-if="filteredLoans.length === 0"
            class="flex flex-col items-center justify-center py-16 text-center px-6"
          >
            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
              </svg>
            </div>
            <p class="text-sm font-semibold text-slate-700">Nenhum empréstimo encontrado</p>
            <p class="text-xs text-slate-400 mt-1 max-w-xs">
              {{ searchQuery ? 'Tente ajustar os termos de busca.' : 'Nenhum empréstimo ativo no momento. Clique em "Novo Empréstimo" para começar.' }}
            </p>
          </div>

          <!-- Table -->
          <div v-else class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
              <thead>
                <tr class="bg-slate-50/70">
                  <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Livro</th>
                  <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Usuário</th>
                  <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Data Empréstimo</th>
                  <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vencimento</th>
                  <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                  <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Ações</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr
                  v-for="loan in filteredLoans"
                  :key="loan.id"
                  class="hover:bg-slate-50/60 transition-colors duration-100"
                  :class="{ 'bg-red-50/30': loan.atrasado }"
                >
                  <!-- Livro -->
                  <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                      </div>
                      <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate max-w-[160px]">{{ loan.livro }}</p>
                        <p class="text-xs text-slate-400 truncate max-w-[160px]">{{ loan.autor }}</p>
                      </div>
                    </div>
                  </td>

                  <!-- Usuário -->
                  <td class="px-5 py-3.5">
                    <p class="font-medium text-slate-700 truncate max-w-[140px]">{{ loan.usuario }}</p>
                    <p class="text-xs text-slate-400 truncate max-w-[140px]">{{ loan.email }}</p>
                  </td>

                  <!-- Data empréstimo -->
                  <td class="px-5 py-3.5 text-slate-600 text-xs whitespace-nowrap">
                    {{ formatDate(loan.data_emprestimo) }}
                  </td>

                  <!-- Vencimento -->
                  <td class="px-5 py-3.5 text-xs whitespace-nowrap" :class="loan.atrasado ? 'text-red-500 font-medium' : 'text-slate-600'">
                    {{ formatDate(loan.data_vencimento) }}
                  </td>

                  <!-- Status -->
                  <td class="px-5 py-3.5">
                    <span
                      class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                      :class="loan.atrasado
                        ? 'bg-red-100 text-red-600'
                        : 'bg-emerald-100 text-emerald-700'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="loan.atrasado ? 'bg-red-500' : 'bg-emerald-500'"></span>
                      {{ loan.atrasado ? 'Atrasado' : 'Em dia' }}
                    </span>
                  </td>

                  <!-- Ações -->
                  <td class="px-5 py-3.5 text-right">
                    <button
                      @click="returnLoan(loan)"
                      :disabled="returningId === loan.id"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                      :class="loan.atrasado
                        ? 'border-red-200 text-red-600 hover:bg-red-50 active:scale-95'
                        : 'border-slate-200 text-slate-600 hover:bg-slate-50 active:scale-95'"
                    >
                      <svg
                        v-if="returningId === loan.id"
                        class="w-3.5 h-3.5 animate-spin"
                        fill="none" viewBox="0 0 24 24"
                      >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                      </svg>
                      <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                      </svg>
                      {{ returningId === loan.id ? 'Processando...' : 'Registrar Devolução' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// ─── Sub-component inline (evita criar arquivo separado) ───────────────────
const NavItem = {
  props: ['icon', 'label', 'active'],
  template: `
    <button
      class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
      :class="active
        ? 'bg-blue-50 text-blue-700'
        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
    >
      <span class="shrink-0" v-html="icon"></span>
      <span class="hidden lg:block truncate">{{ label }}</span>
    </button>
  `
}

// ─── Nav Items ─────────────────────────────────────────────────────────────
const navItems = [
  {
    label: 'Livros',
    active: false,
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
    </svg>`
  },
  {
    label: 'Empréstimos',
    active: true,
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
    </svg>`
  },
  {
    label: 'Usuários',
    active: false,
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
    </svg>`
  }
]

// ─── State ─────────────────────────────────────────────────────────────────
const loans      = ref([])
const loading    = ref(false)
const returningId = ref(null)
const searchQuery = ref('')

// ─── Computed ───────────────────────────────────────────────────────────────
const stats = computed(() => ({
  total:     loans.value.length,
  overdue:   loans.value.filter(l => l.atrasado).length,
  available: loans.value.filter(l => !l.atrasado).length,
}))

const filteredLoans = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return loans.value
  return loans.value.filter(l =>
    l.livro?.toLowerCase().includes(q) ||
    l.usuario?.toLowerCase().includes(q)
  )
})

// ─── API calls ──────────────────────────────────────────────────────────────
async function fetchLoans() {
  loading.value = true
  try {
    const { data } = await api.get('/dashboard/loans')
    loans.value = data
  } catch (err) {
    console.error('Erro ao buscar empréstimos:', err)
  } finally {
    loading.value = false
  }
}

async function returnLoan(loan) {
  if (returningId.value) return
  returningId.value = loan.id
  try {
    await api.patch(`/loans/${loan.id}/return`)
    // Remove o empréstimo da lista localmente após devolução
    loans.value = loans.value.filter(l => l.id !== loan.id)
  } catch (err) {
    console.error('Erro ao registrar devolução:', err)
  } finally {
    returningId.value = null
  }
}

function openNewLoanModal() {
  console.log('Abrir modal de novo empréstimo')
}

// ─── Utils ──────────────────────────────────────────────────────────────────
function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(dateStr))
}

// ─── Lifecycle ──────────────────────────────────────────────────────────────
onMounted(fetchLoans)
</script>