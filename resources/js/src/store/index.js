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
        claims: [],
        sources: [],
        tags: [],
        manager_check: [
            { value: "целевой", icon: "CheckIcon" },
            { value: "не целевой", icon: "XCircleIcon" },
            { value: "не установленный", icon: "XSquareIcon" },
        ],
        client_check: [
            { value: "целевой", icon: "CheckIcon" },
            { value: "не целевой", icon: "XCircleIcon" },
            { value: "не проверенный", icon: "XSquareIcon" },
        ],
        historyArray: [],
        manager_object: {},
        client_object: {},
    },

    mutations: {
        SET_ENTERED: (state, response) => {
            state.entered = response;
        },
        SET_CLAIMS: (state, response) => {
            state.claims = response;
        },
        SET_SOURCES: (state, response) => {
            state.sources = response;
        },
        SET_TAGS: (state, response) => {
            state.tags = response;
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
        SET_HISTORY_ARRAY: (state, payload) => {
            state.historyArray = payload;
        },
        SET_MANAGER_OBJECT: (state, payload) => {
            state.manager_object = payload;
        },
        SET_CLIENT_OBJECT: (state, payload) => {
            state.client_object = payload;
        },
    },
    actions: {
        SET_PROJECTS: async (ctx) => {
            await axios.get("/api/projects").then((response) => {
                const projects = response.data.projects;
                ctx.commit("SET_PROJECTS", projects);
            });
        },
        getSourceTable: async (ctx) => {
            await axios
                .get(" api/projects/" + ctx.getters.project.id + "/sources")
                .then((response) => {
                    let sources = response.data.sources;
                    ctx.commit("SET_SOURCES", sources);
                });
        },
        getTagsTable: async (ctx) => {
            await axios
                .get(" api/projects/" + ctx.getters.project.id + "/tags")
                .then((response) => {
                    let tags = response.data.tags;
                    tags.filter((item) => {
                        item.client_plus_words =
                            item.client_plus_words.join("\n");
                        item.client_minus_words =
                            item.client_minus_words.join("\n");
                        item.operator_plus_words =
                            item.operator_plus_words.join("\n");
                        item.operator_minus_words =
                            item.operator_minus_words.join("\n");
                    });
                    ctx.commit("SET_TAGS", tags);
                });
        },
        getDataTable: async (ctx) => {
            axios
                .get("api/projects/" + ctx.getters.project.id + "/claims")
                .then((response) => {
                    let claims = response.data.claims;
                    ctx.commit("SET_CLAIMS", claims);
                    let managerObject = {};
                    let clientObject = {};
                    claims.forEach((row) => {
                        const activeManager =
                            ctx.getters.manager_check.find(
                                (m) => m.value == row.manager_check
                            ) || null;
                        this.$set(managerObject, row.id, activeManager);
                        ctx.commit("SET_MANAGER_OBJECT", managerObject);
                        const activeClient =
                            ctx.getters.client_check.find(
                                (c) => c.value == row.client_check
                            ) || null;
                        this.$set(clientObject, row.id, activeClient);
                        ctx.commit("SET_CLIENT_OBJECT", clientObject);
                    });
                    claims.filter((row, index, k) => {
                        k = 0;
                        for (let i = 0; i < index; i++) {
                            if (row.phone === claims[i].phone) {
                                k++;
                                if (k < 2) {
                                    // historyArray.push(this.rows[i].id);
                                    ctx.commit(
                                        "SET_HISTORY_ARRAY",
                                        this.rows[i].id
                                    );
                                }
                            }
                        }
                    });
                    ctx.commit("SET_HISTORY_ARRAY", [
                        ...new Set(ctx.getters.historyArray),
                    ]);
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
        getClaims: (state) => {
            return state.claims;
        },
        getSources: (state) => {
            return state.sources;
        },
        getTags: (state) => {
            return state.tags;
        },
        getManagerObject: (state) => {
            return state.manager_object;
        },
        getClientObject: (state) => {
            return state.client_object;
        },
        manager_check: (state) => {
            return state.manager_check;
        },
        client_check: (state) => {
            return state.client_check;
        },
        historyArray: (state) => {
            return state.historyArray;
        },
    },
    modules: {
        app,
        appConfig,
        verticalMenu,
    },
    strict: process.env.DEV,
});
