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
        tempFields: [],
        ObjectIdSources: "",
        tags: [],
        integrations: [],
        options_manager_check: [
            { value: "targeted", text: "целевой" },
            { value: "untargeted", text: "нецелевой" },
            { value: "unidentified", text: "не установленный" },
        ],
        options_client_check: [
            { value: "targeted", text: "целевой" },
            { value: "untargeted", text: "нецелевой" },
            { value: "unchecked", text: "не проверенный" },
        ],
        create_modal_window: false,
    },

    mutations: {
        SET_ENTERED: (state, response) => {
            state.entered = response;
        },
        SET_CREATE_MODAL_WINDOW: (state, response) => {
            state.create_modal_window = response;
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
        SET_INTEGRATIONS: (state, payload) => {
            state.integrations = payload;
        },
        SET_TEMPFIELDS: (state, payload) => {
            state.tempFields = payload;
        },
        SET_OBJECTIDSOURCES: (state, payload) => {
            state.ObjectIdSources = payload;
        },
    },
    actions: {
        getTempFields: async (ctx) => {
            await axios
                .get(
                    "api/projects/" +
                        ctx.getters.project.id +
                        "/sources/" +
                        ctx.getters.getObjectIDSources +
                        "/integration-fields"
                )
                .then((response) => {
                    let tempFields = response.data.fields;
                    ctx.commit("SET_TEMPFIELDS", tempFields);
                })
                .catch((error) => {
                    alert(error.response.data.error);
                });
        },
        SET_PROJECTS: async (ctx) => {
            await axios
                .get("/api/projects")
                .then((response) => {
                    const projects = response.data.projects;
                    ctx.commit("SET_PROJECTS", projects);
                })
                .catch((error) => {});
        },
        getSourceTable: async (ctx) => {
            await axios
                .get(" api/projects/" + ctx.getters.project.id + "/sources")
                .then((response) => {
                    let sources = response.data.sources;
                    ctx.commit("SET_SOURCES", sources);
                })
                .catch((error) => {});
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
                })
                .catch((error) => {});
        },
        getDataTable: async (ctx) => {
            await axios
                .get("api/projects/" + ctx.getters.project.id + "/claims")
                .then((response) => {
                    let claims = response.data.claims;
                    ctx.commit("SET_CLAIMS", claims);
                })
                .catch((error) => {});
        },
        getIntegrationTable: async (ctx) => {
            await axios
                .get("api/integrations")
                .then((response) => {
                    let integrations = response.data.integrations;
                    ctx.commit("SET_INTEGRATIONS", integrations);
                })
                .catch((error) => {});
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
                    const vNodesMsg = [
                        `${Object.values(error.response.data.errors)}`,
                    ];
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
        getIntegrations: (state) => {
            return state.integrations;
        },
        getSources: (state) => {
            return state.sources;
        },
        getTags: (state) => {
            return state.tags;
        },
        manager: (state) => {
            return state.manager;
        },
        get_create_modal_window: (state) => {
            return state.create_modal_window;
        },
        client: (state) => {
            return state.client;
        },
        options_manager_check: (state) => {
            return state.options_manager_check;
        },
        options_client_check: (state) => {
            return state.options_client_check;
        },
        getObjectIDSources: (state) => {
            return state.ObjectIdSources;
        },
        tempFields: (state) => {
            return state.tempFields;
        },
    },
    modules: {
        app,
        appConfig,
        verticalMenu,
    },
    strict: process.env.DEV,
});
