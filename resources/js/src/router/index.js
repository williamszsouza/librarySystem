import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../store/auth';

import Login      from '../views/Login.vue';
import Register   from '../views/Register.vue';
import MainLayout from '../views/layouts/MainLayout.vue';
import Books      from '../views/Books.vue';
import Loans      from '../views/Loans.vue';
import Users      from '../views/Users.vue';
import Dashboard  from '../views/Dashboard.vue';
import MyLoans    from '../views/MyLoans.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/',
    component: MainLayout,
    children: [
      {
        // Rota padrão → livros (público, qualquer um pode ver)
        path: '',
        redirect: '/books'
      },
      {
        path: 'books',
        name: 'Books',
        component: Books
        // Sem meta.requiresAuth — livros são públicos
      },
      {
        path: 'loans',
        name: 'Loans',
        component: Loans,
        meta: { requiresAdmin: true }
      },
      {
        path: 'users',
        name: 'Users',
        component: Users,
        meta: { requiresAdmin: true }
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAdmin: true }
      },
      {
        // Empréstimos do usuário logado — requer auth mas NÃO admin
        path: 'my-loans',
        name: 'MyLoans',
        component: MyLoans,
        meta: { requiresAuth: true }
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  // Rota exclusiva para admin
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return next('/books');
  }

  // Rota que exige apenas estar logado
  if (to.meta.requiresAuth && !authStore.user) {
    return next('/login');
  }

  next();
});

export default router;