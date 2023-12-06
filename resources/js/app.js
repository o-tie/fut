import { createApp } from 'vue';

import Players from "../vue/components/Players.vue";
import LoginForm from "../vue/components/LoginForm.vue";

const app = createApp({
    components: {
        Players,
        LoginForm
    }
});

//Set Vue.js configuration
app.config.productionTip = false;
app.config.warnHandler = (msg, instance, trace) => {};
app.mount('#app');
