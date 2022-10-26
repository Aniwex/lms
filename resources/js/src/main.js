import Vue from "vue";
import { ToastPlugin, ModalPlugin } from "bootstrap-vue";
import VueCompositionAPI from "@vue/composition-api";

import router from "./router";
import store from "./store";
import App from "./App.vue";

// Global Components
import "./global-components";
import VueMask from "v-mask";
// 3rd party plugins
import "@/libs/portal-vue";
import "@/libs/toastification";

// BSV Plugin Registration
Vue.use(ToastPlugin);
Vue.use(ModalPlugin);
Vue.use(VueMask);
// Composition API
Vue.use(VueCompositionAPI);
// import core styles
require("@core/scss/core.scss");

// import assets styles
require("@/assets/scss/style.scss");

Vue.config.productionTip = false;
import VueSweetalert2 from "vue-sweetalert2";
Vue.use(VueSweetalert2);
new Vue({
    router,
    store,
    render: (h) => h(App),
}).$mount("#app");
