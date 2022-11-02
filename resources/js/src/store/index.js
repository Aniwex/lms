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
                projects.filter((item) => {
                    item["title"] = item["text"] = item.name;
                });
                projects.unshift({ value: null, text: "—" });
                ctx.commit("SET_PROJECTS", projects);
            });
        },
        getDataUser: async (ctx) => {
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
        user: (state) => {
            return state.user;
        },
        projects: (state) => {
            return state.projects;
        },
        project: (state) => {
            return state.project;
        },
    },
    modules: {
        app,
        appConfig,
        verticalMenu,
    },
    strict: process.env.DEV,
});
