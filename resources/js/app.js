import { createApp } from 'vue';

import Players from "../vue/components/Players.vue";
import LoginForm from "../vue/components/LoginForm.vue";
import MakeSquads from "../vue/components/MakeSquads.vue";
import Calendar from "../vue/components/Calendar.vue";

window.appContainerWidth = document.getElementById('appContainer').offsetWidth;

const app = createApp({
    components: {
        Players,
        LoginForm,
        MakeSquads,
        Calendar,
    }
});

//Set Vue.js configuration
app.config.productionTip = false;
app.config.warnHandler = (msg, instance, trace) => {};
app.mount('#app');
