import Vue from "vue";
import Vuex, { mapGetters } from "vuex";
import axios from "axios";
// Modules
import app from "./app";
import appConfig from "./app-config";
import verticalMenu from "./vertical-menu";
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        status: "",
        user: {},
        users: [],
        token: localStorage.getItem("user-token") || "",
        entered: false,
        seeAction: {},
        searchHistory: "",
        projects: "",
        project: "",
    },

    mutations: {
        SET_ENTERED: (state, response) => {
            state.entered = response;
        },
        SET_SEEACTION: (state, payload) => {
            state.seeAction = payload;
        },
        SET_SEARCH: (state, payload) => {
            state.searchHistory = payload;
        },
        SET_USER: (state, payload) => {
            state.user = payload;
        },
        SET_USERS: (state, payload) => {
            state.users = payload;
        },
        SET_PROJECTS: (state, payload) => {
            state.projects = payload;
        },
        SET_PROJECT: (state, payload) => {
            state.project = payload;
        },
    },
    actions: {
        SET_PROJECTS: async (ctx) => {
            await axios.get("/api/projects").then((response) => {
                const projects = response.data.projects;
                ctx.commit("SET_PROJECTS", projects);
            });
        },
        SET_USER: async (ctx) => {
            await axios.get("/sanctum/csrf-cookie").then((response) => {
                axios
                    .get("api/user")
                    .then((response) => {
                        const user = response.data;
                        ctx.commit("SET_USER", user);
                    })
                    .catch((error) => {
                        if (error.response.status === 401) {
                            axios.get("/logout").then((resp) => {
                                localStorage.removeItem(
                                    "x_xsrf_token",
                                    resp.config.headers["X-XSRF-TOKEN"]
                                );
                                this.$router.push("/");
                                this.$store.commit("SET_ENTERED", false);
                            });
                        } else {
                            const vNodesMsg = [`${error.response.data.error}`];
                            this.$bvToast.toast([vNodesMsg], {
                                title: `Ошибка`,
                                variant: "danger",
                                solid: true,
                                appendToast: true,
                                toaster: "b-toaster-top-center",
                                autoHideDelay: 3000,
                            });
                        }
                    });
            });
        },
        getDataUsers: async (ctx) => {
            await axios
                .get("/api/users")
                .then((response) => {
                    const users = response.data.users;
                    ctx.commit("SET_USERS", users);
                })
                .catch((error) => {
                    const vNodesMsg = [`${error.response.data.error}`];
                    this.$bvToast.toast([vNodesMsg], {
                        title: `Ошибка`,
                        variant: "danger",
                        solid: true,
                        appendToast: true,
                        toaster: "b-toaster-top-center",
                        autoHideDelay: 3000,
                    });
                });
        },
    },
    getters: {
        entered: (state) => {
            return state.entered;
        },
        getUsers: (state) => {
            return state.users;
        },
        seeAction: (state) => {
            return state.seeAction;
        },
        searchHistory: (state) => {
            return state.searchHistory;
        },
        projects: (state) => {
            return state.projects;
        },
        project: (state) => {
            return state.project;
        },
        getUser: (state) => {
            return state.user;
        },
    },
    modules: {
        app,
        appConfig,
        verticalMenu,
    },
    strict: process.env.DEV,
});
