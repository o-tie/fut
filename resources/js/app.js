import { createApp } from 'vue';

import Players from "../vue/components/Players.vue";
import LoginForm from "../vue/components/LoginForm.vue";
import MakeSquads from "../vue/components/MakeSquads.vue";

const app = createApp({
    components: {
        Players,
        LoginForm,
        MakeSquads,
    }
});

//Set Vue.js configuration
app.config.productionTip = false;
app.config.warnHandler = (msg, instance, trace) => {};
app.mount('#app');
