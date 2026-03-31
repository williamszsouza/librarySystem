import { defineStore } from "pinia";
import { ref,computed } from "vue";

export const useAuthStore = defineStore('auth', () => {
    const user = ref(JSON.parse(localStorage.getItem('user')) || null)
    const token = ref(localStorage.getItem('token') || null)

    const isAdmin = computed(() => {
        return user.value?.is_admin === 1 || user.value?.is_admin === true;
    });

    function setAuth(userData,authToken)
    {
        user.value = userData
        token.value = authToken
        localStorage.setItem('token',authToken)
        localStorage.setItem('user',JSON.stringify(userData))
    }

    function logout() {
        user.value = null;
        token.value = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }

    return {user, token, setAuth, logout, isAdmin};


})