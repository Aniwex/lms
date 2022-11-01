<template>
  <div
    v-if="this.$store.getters.entered"
    class="navbar-container d-flex content align-items-center"
  >
    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link class="nav-link" @click="toggleVerticalMenuActive">
          <feather-icon icon="MenuIcon" size="21" />
        </b-link>
      </li>
    </ul>

    <!-- Left Col -->
    <div
      class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex"
    >
      <dark-Toggler class="d-none d-lg-block" />
      <b-button
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
        to="Projects"
        class="navbar__button"
      >
        Проекты
      </b-button>
      <b-nav>
        <b-nav-item to="Users" class="navbar__button">
          <user-icon
            size="1.75x"
            class="plus-icon align-middle mr-25"
          ></user-icon>
        </b-nav-item>
        <b-nav-item to="Integration" class="navbar__button">
          <TrendingUpIcon
            size="1.75x"
            class="plus-icon align-middle mr-25"
          ></TrendingUpIcon>
        </b-nav-item>
      </b-nav>
      <div v-if="get_projects">
        <b-form-select
          class="choose__project"
          v-model="choose_project"
          :options="get_projects"
          @change="selectProject"
        >
        </b-form-select>
      </div>
      <div v-else>
        <b-button variant="primary" disabled class="mr-1">
          <b-spinner small />
          Загрузка...
        </b-button>
      </div>
    </div>
    <div v-if="user.login === undefined">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>

    <b-navbar-nav v-else class="nav align-items-center ml-auto">
      <b-nav-item-dropdown
        right
        toggle-class="d-flex align-items-center dropdown-user-link"
        class="dropdown-user"
      >
        <template #button-content>
          <div class="d-sm-flex d-none user-nav">
            <span class="user-status">{{ user.login }}</span>
          </div>
          <b-avatar
            size="40"
            variant="light-primary"
            badge
            class="badge-minimal"
            badge-variant="success"
          />
        </template>

        <b-dropdown-item link-class="d-flex align-items-center">
          <feather-icon size="16" icon="UserIcon" class="mr-50" />
          <span>Profile</span>
        </b-dropdown-item>

        <b-dropdown-item
          @click.prevent="logout"
          link-class="d-flex align-items-center"
          v-b-tooltip.hover.bottom="'Выйти'"
        >
          <feather-icon size="16" icon="LogOutIcon" class="mr-50" />
          <!-- <span v-if="isLoggedIn"> | <a @click="logout">Logout</a></span> -->
          <a>Выйти</a>
        </b-dropdown-item>
      </b-nav-item-dropdown>
    </b-navbar-nav>
  </div>
</template>

<script>
import {
  BFormSelect,
  BLink,
  BNavbarNav,
  BNavItemDropdown,
  BDropdownItem,
  BDropdownDivider,
  BAvatar,
  BSpinner,
  BButton,
  VBTooltip,
  BNav,
  BNavItem,
} from "bootstrap-vue";
import Ripple from "vue-ripple-directive";
import DarkToggler from "@core/layouts/components/app-navbar/components/DarkToggler.vue";
import axios from "axios";
import { UserIcon, TrendingUpIcon } from "vue-feather-icons";
export default {
  components: {
    BNav,
    BNavItem,
    TrendingUpIcon,
    UserIcon,
    BFormSelect,
    BLink,
    BNavbarNav,
    BNavItemDropdown,
    BDropdownItem,
    BDropdownDivider,
    BAvatar,
    BSpinner,
    BButton,
    // Navbar Components
    DarkToggler,
  },
  directives: {
    Ripple,
    "b-tooltip": VBTooltip,
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {
      user: {},
      response: [],
      choose_project: null,
      projects: [],
    };
  },
  methods: {
    selectProject(project) {
      this.$store.commit("SET_PROJECT", project);
    },
    get_user() {
      axios.get("/sanctum/csrf-cookie").then((response) => {
        axios
          .get("api/user")
          .then((response) => {
            this.user = response.data;
            const token = response.config.headers["X-XSRF-TOKEN"];
            // const vNodesMsg = [`Вы успешно вошли как  ${response.data.login}`];
            // this.$bvToast.toast([vNodesMsg], {
            //   title: `Добро пожаловать`,
            //   variant: "success",
            //   solid: true,
            //   appendToast: true,
            //   toaster: "b-toaster-top-center",
            //   autoHideDelay: 3000,
            // });
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
    logout() {
      this.user.login = undefined;
      axios.get("/logout").then((resp) => {
        localStorage.removeItem(
          "x_xsrf_token",
          resp.config.headers["X-XSRF-TOKEN"]
        );
        this.$router.push("/");
        this.$store.commit("SET_ENTERED", false);
      });
    },
    async setProjects() {
      await this.$store.dispatch("SET_PROJECTS");
      this.projects = this.$store.getters.projects;
    },
  },

  created() {
    this.get_user();
    this.setProjects();
  },
  computed: {
    get_projects() {
      return this.$store.getters.projects;
    },
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    },
  },
};
</script>
