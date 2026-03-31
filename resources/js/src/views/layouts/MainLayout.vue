<template>
  <div class="flex h-screen bg-slate-50 font-sans overflow-hidden">
    <aside class="w-16 lg:w-60 bg-white border-r border-slate-100 flex flex-col shadow-sm shrink-0 transition-all duration-300">

      <!-- Logo -->
      <div class="h-16 flex items-center justify-center lg:justify-start px-4 border-b border-slate-100">
        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
          </svg>
        </div>
        <span class="hidden lg:block ml-3 font-semibold text-slate-800 text-sm tracking-wide">LibrarySystem</span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 py-4 space-y-1 px-2">
        <router-link
          v-for="item in visibleNavItems" :key="item.label" :to="item.route"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
          active-class="bg-blue-50 text-blue-700"
          exact-active-class="bg-blue-50 text-blue-700"
          :class="$route.path.startsWith(item.route) ? 'bg-blue-50 text-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'"
        >
          <span class="shrink-0" v-html="item.icon"></span>
          <span class="hidden lg:block truncate">{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- Área do usuário com dropdown -->
      <div class="p-3 border-t border-slate-100 relative">
        <div
          @click="toggleUserMenu"
          class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors select-none"
        >
          <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
            <span class="text-blue-700 text-xs font-bold">{{ userInitial }}</span>
          </div>
          <div class="hidden lg:flex flex-1 min-w-0 items-center justify-between">
            <div class="min-w-0">
              <p class="text-xs font-semibold text-slate-700 truncate">{{ authStore.user?.name || 'Visitante' }}</p>
              <p class="text-xs text-slate-400 truncate">{{ authStore.user?.email || 'Visitante' }}</p>
            </div>
            <!-- Seta indicando que abre menu -->
            <svg
              :class="['w-3.5 h-3.5 text-slate-400 transition-transform shrink-0 ml-1', showUserMenu ? 'rotate-180' : '']"
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </div>

        <!-- Dropdown -->
        <Transition name="dropdown">
          <div
            v-if="showUserMenu"
            class="absolute bottom-full left-3 right-3 mb-1 bg-white rounded-xl border border-slate-100 shadow-lg overflow-hidden z-50"
          >
            <!-- Cabeçalho com dados da conta -->
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
              <p class="text-xs font-semibold text-slate-700 truncate">{{ authStore.user?.name }}</p>
              <p class="text-xs text-slate-400 truncate mt-0.5">{{ authStore.user?.email }}</p>
              <span
                v-if="authStore.isAdmin"
                class="inline-block mt-1.5 text-[10px] bg-blue-100 text-blue-700 font-semibold px-2 py-0.5 rounded-full"
              >
                Administrador
              </span>
            </div>

            <!-- Meus empréstimos (só para não-admin) -->
            <router-link
              v-if="!authStore.isAdmin"
              to="/my-loans"
              @click="showUserMenu = false"
              class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-600 hover:bg-slate-50 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
              </svg>
              Meus empréstimos
            </router-link>

            <!-- Logout -->
            <button
              @click="handleLogout"
              class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Sair da conta
            </button>
          </div>
        </Transition>
      </div>

    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../store/auth'
import { useRouter } from 'vue-router'

const authStore    = useAuthStore()
const router       = useRouter()
const showUserMenu = ref(false)

const userInitial = computed(() => {
  const name = authStore.user?.name
  return name ? name.charAt(0).toUpperCase() : 'V'
})

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

// Fecha o dropdown ao clicar fora
const handleClickOutside = (e) => {
  if (!e.target.closest('.relative')) {
    showUserMenu.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

const handleLogout = () => {
  authStore.logout()
  showUserMenu.value = false
  router.push('/login')
}

const allNavItems = [
  {
    label: 'Livros',
    route: '/books',
    requireAdmin: false,
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>`
  },
  {
    label: 'Empréstimos',
    route: '/loans',
    requireAdmin: true,
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>`
  },
  {
    label: 'Usuários',
    route: '/users',
    requireAdmin: true,
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>`
  },
  {
    label: 'Meus Empréstimos',
    route: '/my-loans',
    requireAdmin: false,
    requiresAuth: true,   
    onlyNonAdmin: true,   
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>`
  }
]

const visibleNavItems = computed(() => {
  return allNavItems.filter(item => {
    if (item.requireAdmin && !authStore.isAdmin) return false
    if (item.onlyNonAdmin && authStore.isAdmin) return false
    if (item.requiresAuth && !authStore.user) return false
    return true
  })
})
</script>

<style scoped>
.dropdown-enter-active { transition: opacity 0.15s ease, transform 0.15s ease; }
.dropdown-leave-active { transition: opacity 0.1s ease, transform 0.1s ease; }
.dropdown-enter-from  { opacity: 0; transform: translateY(4px); }
.dropdown-leave-to    { opacity: 0; transform: translateY(4px); }
</style>