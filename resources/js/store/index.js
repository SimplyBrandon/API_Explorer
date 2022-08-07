import { createStore } from 'vuex';

export const store = createStore({
    state() {
        return {
            isLoggedIn: false,
        }
    },
    mutations: {
        login() {
            this.state.isLoggedIn = true;
        }
    },
    actions: {
        login({ commit }) {
            commit('login');
        }
    }
})