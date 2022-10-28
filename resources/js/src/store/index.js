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
        token: localStorage.getItem("user-token") || "",
        entered: false,
        seeAction: {},
        searchHistory: "",
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
        SET_PROJECT: (state, payload) => {
            state.project = payload;
        },
    },
    actions: {
        
    },
    getters: {
        entered: (state) => {
            return state.entered;
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
        project: (state) => {
            return state.project;
        }
    },
    modules: {
        app,
        appConfig,
        verticalMenu,
    },
    strict: process.env.DEV,
});
