import Vue from "vue";
import VueRouter from "vue-router";
import axios from "axios";
Vue.use(VueRouter);

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    scrollBehavior() {
        return { x: 0, y: 0 };
    },
    routes: [
        {
            path: "/",
            name: "Login",
            component: () => import("@/views/Login.vue"),
            meta: {
                breadcrumb: [
                    {
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/Home",
            name: "Home",
            component: () => import("@/views/Home.vue"),
            meta: {
                pageTitle: "Список обращений",
                breadcrumb: [
                    {
                        text: "Список обращений",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/Integration",
            name: "Integration",
            component: () => import("@/views/Integration.vue"),
            meta: {
                pageTitle: "Интеграции",
                breadcrumb: [
                    {
                        text: "Интеграции",
                        active: true,
                    },
                ],
            },
        },
        // {
        //     path: "/Users",
        //     name: "Users",
        //     component: () => import("@/views/Users.vue"),
        //     meta: {
        //         pageTitle: "Пользователи",
        //         breadcrumb: [
        //             {
        //                 text: "Пользователи",
        //                 active: true,
        //             },
        //         ],
        //     },
        // },
        // {
        //     path: "/Projects",
        //     name: "Projects",
        //     component: () => import("@/views/Projects.vue"),
        //     meta: {
        //         pageTitle: "Проекты",
        //         breadcrumb: [
        //             {
        //                 text: "Проекты",
        //                 active: true,
        //             },
        //         ],
        //     },
        // },
        {
            path: "/Sources",
            name: "Sources",
            component: () => import("@/views/Sources.vue"),
            meta: {
                pageTitle: "Источники обращений",
                breadcrumb: [
                    {
                        text: "Источники обращений",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/Tags",
            name: "Tags",
            component: () => import("@/views/Tags.vue"),
            meta: {
                pageTitle: "Тэги обращений",
                breadcrumb: [
                    {
                        text: "Тэги обращений",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/NewAppeal",
            name: "Добавить обращение",
            component: () => import("../layouts/components/NewAppeal"),
            meta: {
                pageTitle: "Добавить обращение",
                breadcrumb: [
                    {
                        text: "Добавить обращение",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/NewIntegration",
            name: "Добавить интеграцию",
            component: () => import("../layouts/components/NewIntegration"),
            meta: {
                pageTitle: "Добавить интеграцию",
                breadcrumb: [
                    {
                        text: "Добавить интеграцию",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/NewSource",
            name: "Добавить иcточник",
            component: () => import("../layouts/components/NewSource"),
            meta: {
                pageTitle: "Добавить иcточник",
                breadcrumb: [
                    {
                        text: "Добавить иcточник",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/NewTag",
            name: "Добавить тэг",
            component: () => import("../layouts/components/NewTag"),
            meta: {
                pageTitle: "Добавить тэг",
                breadcrumb: [
                    {
                        text: "Добавить тэг",
                        active: true,
                    },
                ],
            },
        },
        {
            path: "/Register",
            name: "Регистрация",
            component: () => import("@/views/Register.vue"),
            meta: {
                layout: "full",
            },
        },
        {
            path: "/error-404",
            name: "error-404",
            component: () => import("@/views/error/Error404.vue"),
            meta: {
                layout: "full",
            },
        },
        {
            path: "*",
            redirect: "error-404",
        },
    ],
});

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
    const appLoading = document.getElementById("loading-bg");
    // Remove initial loading

    if (appLoading) {
        appLoading.style.display = "none";
    }
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("x_xsrf_token");
    if (!token && to.name === "Регистрация") {
        next();
    }
    if (
        (to.name === "Home" ||
            to.name === "Добавить обращение" ||
            to.name === "Tags" ||
            to.name === "Sources" ||
            to.name === "Integration" ||
            to.name === "Добавить интеграцию") &&
        !token
    ) {
        return next({
            name: "Login",
        });
    }
    if (token) {
        next();
    }

    next();
});
export default router;
