<template>
  <div class="h-full flex flex-col bg-slate-50">

    <!-- Header -->
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 shadow-sm">
      <div>
        <h1 class="text-lg font-bold text-slate-800">Acervo de Livros</h1>
        <p class="text-xs text-slate-400 mt-0.5">
          {{ pagination.total ? `${pagination.total} livros no acervo` : 'Catálogo completo da biblioteca' }}
        </p>
      </div>
      <!-- Qualquer usuário logado pode cadastrar -->
      <button
        v-if="authStore.user"
        @click="openCreateModal"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-medium px-4 py-2 rounded-lg transition-all shadow-sm"
      >
        <span>+ Adicionar Livro</span>
      </button>
    </header>

    <main class="flex-1 overflow-y-auto p-6 flex flex-col gap-6">

      <!-- Banner para usuário deslogado -->
      <div
        v-if="!authStore.user"
        class="flex items-center justify-between gap-4 bg-blue-50 border border-blue-100 rounded-xl px-5 py-4"
      >
        <div class="flex items-center gap-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-blue-700 font-medium">
            Faça login para cadastrar livros e realizar empréstimos.
          </p>
        </div>
        <router-link
          to="/login"
          class="shrink-0 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition-all"
        >
          Entrar
        </router-link>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div v-for="n in pagination.perPage || 10" :key="n"
          class="bg-white rounded-xl border border-slate-100 shadow-sm h-64 animate-pulse" />
      </div>

      <!-- Empty state -->
      <div v-else-if="books.length === 0"
        class="flex flex-col items-center justify-center h-64 text-slate-400 gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <p class="text-sm font-medium">Nenhum livro encontrado</p>
      </div>

      <!-- Grid de livros -->
      <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        <div
          v-for="(book, index) in books"
          :key="book?.id ?? index"
          @click="openModal(book)"
          class="group bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 overflow-hidden cursor-pointer flex flex-col"
        >
          <div
            class="h-40 flex items-center justify-center text-3xl font-bold text-white select-none relative"
            :style="{ background: coverGradient(book.title) }"
          >
            {{ book.title?.charAt(0)?.toUpperCase() }}
            <span :class="['absolute top-2 right-2 w-2.5 h-2.5 rounded-full border-2 border-white', book.is_available ? 'bg-emerald-400' : 'bg-red-400']" />
          </div>

          <div class="p-3 flex flex-col gap-1 flex-1">
            <p class="text-sm font-semibold text-slate-800 leading-tight line-clamp-2">{{ book.title }}</p>
            <p class="text-xs text-slate-400 truncate">{{ book.author }}</p>
            <div class="mt-auto pt-2 flex items-center justify-between gap-1">
              <!-- Badge "meu livro" -->
              <span
                v-if="authStore.user?.id === book.user_id"
                class="text-[10px] bg-slate-100 text-slate-500 font-medium px-2 py-0.5 rounded-full"
              >meu livro</span>
              <span v-else class="flex-1" />
              <span class="text-[10px] text-slate-300">{{ book.year }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Paginação -->
      <div v-if="pagination.lastPage > 1" class="flex items-center justify-between mt-auto pt-2">
        <p class="text-xs text-slate-400">
          Página {{ pagination.currentPage }} de {{ pagination.lastPage }}
          <span class="ml-1">({{ pagination.from }}–{{ pagination.to }} de {{ pagination.total }})</span>
        </p>
        <div class="flex items-center gap-1">
          <button :disabled="pagination.currentPage === 1 || loading" @click="goToPage(pagination.currentPage - 1)"
            class="px-3 py-1.5 text-xs rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
            ← Anterior
          </button>
          <button v-for="page in visiblePages" :key="page" @click="page !== '...' && goToPage(page)"
            :class="['w-8 h-8 text-xs rounded-lg border transition-all', page === pagination.currentPage ? 'bg-blue-600 border-blue-600 text-white font-medium' : page === '...' ? 'border-transparent text-slate-400 cursor-default' : 'border-slate-200 text-slate-600 hover:bg-slate-100']">
            {{ page }}
          </button>
          <button :disabled="pagination.currentPage === pagination.lastPage || loading" @click="goToPage(pagination.currentPage + 1)"
            class="px-3 py-1.5 text-xs rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
            Próxima →
          </button>
        </div>
      </div>

    </main>

    <!-- ══════════════════════════════════════
         MODAL DE DETALHES
    ══════════════════════════════════════ -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selectedBook" class="fixed inset-0 z-[9998] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal" />

          <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col overflow-hidden max-h-[90vh]">

            <!-- Capa -->
            <div class="h-52 flex items-center justify-center relative shrink-0" :style="{ background: coverGradient(selectedBook.title) }">
              <span class="text-[100px] font-bold text-white/20 select-none leading-none" style="font-family: Georgia, serif;">
                {{ selectedBook.title?.charAt(0)?.toUpperCase() }}
              </span>
              <button @click="closeModal" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/25 hover:bg-black/40 flex items-center justify-center text-white transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Conteúdo -->
            <div class="overflow-y-auto flex flex-col gap-5 p-6">

              <!-- Título + status -->
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <h2 class="text-xl font-bold text-slate-800 leading-snug">{{ selectedBook.title }}</h2>
                  <p class="text-sm text-slate-500 mt-1">{{ selectedBook.author }}</p>
                </div>
                <div :class="['shrink-0 flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full', selectedBook.is_available ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600']">
                  <span :class="['w-1.5 h-1.5 rounded-full', selectedBook.is_available ? 'bg-emerald-500' : 'bg-red-500']" />
                  {{ selectedBook.is_available ? 'Disponível' : 'Indisponível' }}
                </div>
              </div>

              <!-- Botão de empréstimo -->
              <button
                v-if="selectedBook.is_available && authStore.user"
                @click="realizarEmprestimo(selectedBook)"
                :disabled="loanLoading"
                class="w-full flex items-center justify-center gap-2 py-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-all shadow-sm"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                </svg>
                {{ loanLoading ? 'Processando...' : 'Realizar Empréstimo' }}
              </button>

              <!-- Aviso indisponível -->
              <div v-else-if="!selectedBook.is_available" class="flex items-center gap-3 p-3.5 bg-red-50 border border-red-100 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <p class="text-xs text-red-600 font-medium">Este livro está emprestado no momento.</p>
              </div>

              <!-- Sinopse -->
              <div v-if="selectedBook.description">
                <p class="text-[10px] text-blue-600 uppercase tracking-wider font-semibold mb-2">Sinopse</p>
                <p class="text-sm text-slate-600 leading-relaxed">{{ selectedBook.description }}</p>
              </div>

            </div>

            <!-- Rodapé: botões de edição/exclusão condicionais -->
            <div class="px-6 py-4 border-t border-slate-100 flex gap-2 shrink-0 bg-slate-50">
              <button @click="closeModal"
                class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl transition-all">
                Fechar
              </button>

              <!-- Editar: admin sempre pode; usuário normal só se for dono -->
              <button
                v-if="canEditOrDelete(selectedBook)"
                @click="openEditModal(selectedBook)"
                class="flex-1 py-2.5 text-sm font-semibold text-white bg-slate-700 hover:bg-slate-800 rounded-xl transition-all"
              >
                Editar
              </button>

              <!-- Excluir: mesma regra -->
              <button
                v-if="canEditOrDelete(selectedBook)"
                @click="deleteBook(selectedBook)"
                class="py-2.5 px-4 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all"
              >
                Excluir
              </button>
            </div>

          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ══════════════════════════════════════
         MODAL DE FORMULÁRIO (criar / editar)
    ══════════════════════════════════════ -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showFormModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeFormModal" />

          <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
              <h2 class="text-base font-bold text-slate-800">{{ isEditing ? 'Editar Livro' : 'Novo Livro' }}</h2>
              <button @click="closeFormModal" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <form @submit.prevent="saveBook" class="p-6 flex flex-col gap-4">

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Título <span class="text-red-400">*</span></label>
                <input
                  v-model="bookForm.title"
                  type="text"
                  placeholder="Ex: Dom Quixote"
                  required
                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-300"
                />
                <p v-if="formErrors.title" class="text-xs text-red-500">{{ formErrors.title }}</p>
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Autor <span class="text-red-400">*</span></label>
                <input
                  v-model="bookForm.author"
                  type="text"
                  placeholder="Ex: Miguel de Cervantes"
                  required
                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-300"
                />
                <p v-if="formErrors.author" class="text-xs text-red-500">{{ formErrors.author }}</p>
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                  Sinopse <span class="text-slate-300 font-normal normal-case">(opcional)</span>
                </label>
                <textarea
                  v-model="bookForm.description"
                  rows="4"
                  placeholder="Breve descrição do livro..."
                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none placeholder:text-slate-300"
                />
                <p class="text-[10px] text-slate-300 text-right">{{ bookForm.description?.length ?? 0 }}/1000</p>
              </div>

              <div class="flex gap-3 mt-2">
                <button type="button" @click="closeFormModal"
                  class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                  Cancelar
                </button>
                <button type="submit" :disabled="formLoading"
                  class="flex-1 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 rounded-xl transition-all">
                  {{ formLoading ? 'Salvando...' : (isEditing ? 'Salvar alterações' : 'Cadastrar') }}
                </button>
              </div>

            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import { useAuthStore } from '../store/auth'

const authStore = useAuthStore()

// ── Estado principal ──────────────────────────────────────────────
const books       = ref([])
const loading     = ref(true)
const loanLoading = ref(false)
const selectedBook = ref(null)

// ── Estado do formulário ──────────────────────────────────────────
const showFormModal = ref(false)
const isEditing     = ref(false)
const formLoading   = ref(false)
const formErrors    = ref({})
const bookForm      = ref({ id: null, title: '', author: '', description: '' })

// ── Paginação ─────────────────────────────────────────────────────
const pagination = ref({
  currentPage: 1, lastPage: 1, total: 0, from: 0, to: 0, perPage: 10,
})

// ── Helpers ───────────────────────────────────────────────────────
const coverGradient = (title = '') => {
  const hue = [...title].reduce((acc, c) => acc + c.charCodeAt(0), 0) % 360
  return `linear-gradient(135deg, hsl(${hue}, 55%, 45%), hsl(${(hue + 40) % 360}, 60%, 35%))`
}

/**
 * Espelha exatamente a BookPolicy do Laravel:
 * - admin (before()) → sempre true
 * - usuário normal → só se for dono do livro
 */
const canEditOrDelete = (book) => {
  if (!authStore.user) return false
  if (authStore.user.is_admin) return true
  return authStore.user.id === book.user_id
}

// ── Controle de modais ────────────────────────────────────────────
const openModal = (book) => {
  selectedBook.value = book
  document.body.style.overflow = 'hidden'
}

const closeModal = () => {
  selectedBook.value = null
  document.body.style.overflow = ''
}

const openCreateModal = () => {
  bookForm.value = { id: null, title: '', author: '', description: '' }
  formErrors.value = {}
  isEditing.value = false
  showFormModal.value = true
  document.body.style.overflow = 'hidden'
}

const openEditModal = (book) => {
  bookForm.value = { id: book.id, title: book.title, author: book.author, description: book.description ?? '' }
  formErrors.value = {}
  isEditing.value = true
  closeModal()
  showFormModal.value = true
  document.body.style.overflow = 'hidden'
}

const closeFormModal = () => {
  showFormModal.value = false
  formErrors.value = {}
  document.body.style.overflow = ''
}

// ── CRUD ──────────────────────────────────────────────────────────
const fetchBooks = async (page = 1) => {
  loading.value = true
  try {
    const { data } = await axios.get('api/books', { params: { page } })
    books.value = (Array.isArray(data.data) ? data.data : []).filter(Boolean)
    pagination.value = {
      currentPage: data.current_page ?? 1,
      lastPage:    data.last_page    ?? 1,
      total:       data.total        ?? 0,
      from:        data.from         ?? 0,
      to:          data.to           ?? 0,
      perPage:     data.per_page     ?? 10,
    }
  } catch {
    books.value = []
  } finally {
    loading.value = false
  }
}

const saveBook = async () => {
  formLoading.value = true
  formErrors.value = {}

  // Monta o payload — só envia description se tiver valor (campo nullable)
  const payload = {
    title:  bookForm.value.title,
    author: bookForm.value.author,
    ...(bookForm.value.description ? { description: bookForm.value.description } : {}),
  }

  try {
    if (isEditing.value) {
      await axios.put(`api/books/${bookForm.value.id}`, payload)
      toast.success('Livro atualizado com sucesso!')
    } else {
      await axios.post('api/books', payload)
      toast.success('Livro cadastrado com sucesso!')
    }
    closeFormModal()
    fetchBooks(pagination.value.currentPage)
  } catch (error) {
    if (error.response?.status === 422) {
      // Erros de validação do Laravel — mapeia por campo
      formErrors.value = Object.fromEntries(
        Object.entries(error.response.data.errors ?? {}).map(([k, v]) => [k, v[0]])
      )
    } else if (error.response?.status === 403) {
      toast.error('Você não tem permissão para editar este livro.')
      closeFormModal()
    } else {
      toast.error(error.response?.data?.message || 'Erro ao salvar o livro.')
    }
  } finally {
    formLoading.value = false
  }
}

const deleteBook = async (book) => {
  if (!confirm(`Excluir "${book.title}"? Esta ação não pode ser desfeita.`)) return
  try {
    await axios.delete(`api/books/${book.id}`)
    toast.success('Livro excluído com sucesso!')
    closeModal()
    // Se era o último da página, volta uma página
    const targetPage = books.value.length === 1 && pagination.value.currentPage > 1
      ? pagination.value.currentPage - 1
      : pagination.value.currentPage
    fetchBooks(targetPage)
  } catch (error) {
    if (error.response?.status === 403) {
      toast.error('Você não tem permissão para excluir este livro.')
    } else {
      toast.error(error.response?.data?.message || 'Erro ao excluir o livro.')
    }
  }
}

const realizarEmprestimo = async (book) => {
  loanLoading.value = true
  try {
    await axios.post('api/loans', { book_id: book.id })
    // Atualiza localmente sem refetch
    book.is_available = false
    selectedBook.value = { ...book }
    toast.success('Empréstimo realizado com sucesso!')
  } catch (error) {
    toast.error(error.response?.data?.message || 'Erro ao realizar empréstimo.')
  } finally {
    loanLoading.value = false
  }
}

// ── Paginação ─────────────────────────────────────────────────────
const visiblePages = computed(() => {
  const { currentPage, lastPage } = pagination.value
  if (lastPage <= 7) return Array.from({ length: lastPage }, (_, i) => i + 1)
  const pages = new Set([1, lastPage, currentPage, currentPage - 1, currentPage + 1].filter(p => p >= 1 && p <= lastPage))
  const sorted = [...pages].sort((a, b) => a - b)
  const result = []
  for (let i = 0; i < sorted.length; i++) {
    if (i > 0 && sorted[i] - sorted[i - 1] > 1) result.push('...')
    result.push(sorted[i])
  }
  return result
})

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.lastPage) return
  fetchBooks(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// ── Lifecycle ─────────────────────────────────────────────────────
const onKeydown = (e) => {
  if (e.key === 'Escape') { closeModal(); closeFormModal() }
}

onMounted(() => {
  window.addEventListener('keydown', onKeydown)
  fetchBooks()
})

onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.modal-enter-active { transition: opacity 0.2s ease; }
.modal-leave-active { transition: opacity 0.18s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.25s cubic-bezier(0.34, 1.4, 0.64, 1), opacity 0.2s ease;
}
.modal-enter-from .relative { transform: scale(0.94) translateY(20px); opacity: 0; }
.modal-leave-to .relative   { transform: scale(0.96) translateY(10px); opacity: 0; }
</style>