import { createApp } from 'vue';

import App from './App.vue';
import { router } from './router';
import { store } from './store';

import moment from 'moment';

const app = createApp(App);
app.use(router);
app.use(store);

app.config.globalProperties.$filters = {
    timeAgo(value) {
        return moment(value).fromNow();
    }
};

app.mount('#app');