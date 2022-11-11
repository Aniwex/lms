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
        v-if="get_projects.role !== undefined && get_projects.role.id === 1"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
        to="Projects"
        class="navbar__button"
      >
        Проекты
      </b-button>
      <b-nav
        v-if="get_projects.role !== undefined && get_projects.role.id === 1"
      >
        <b-nav-item to="Users" class="navbar__button">
          <user-icon
            size="1.75x"
            class="plus-icon align-middle mr-25"
          ></user-icon>
        </b-nav-item>
        <b-nav-item
          to="Integration"
          class="navbar__button"
          v-if="get_projects.role !== undefined && get_projects.role.id === 1"
        >
          <TrendingUpIcon
            size="1.75x"
            class="plus-icon align-middle mr-25"
          ></TrendingUpIcon>
        </b-nav-item>
      </b-nav>
      <div v-if="get_projects.projects && get_choose_project">
        <multiselect
          v-model="get_choose_project"
          :options="get_projects.projects"
          selectedLabel="Выбрано"
          class="choose__project"
          label="name"
          track-by="name"
          placeholder="Выберите проект"
          @select="selectProject"
          :showLabels="false"
        >
        </multiselect>
      </div>
      <div v-if="get_projects.projects">
        <div v-if="get_projects.projects.length === 0">
          Доступных проектов нет
        </div>
      </div>
      <div v-if="!get_projects.projects">
        <b-button variant="primary" disabled class="mr-1">
          <b-spinner small />
          Загрузка...
        </b-button>
      </div>
    </div>
    <div v-if="get_projects.login === undefined">
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
            <span class="user-status">{{ get_projects.login }}</span>
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
    async selectProject(project) {
      await this.$store.commit("SET_PROJECT", project);
      await this.$store.dispatch("getDataTable");
      await this.$store.dispatch("getSourceTable");
      await this.$store.dispatch("getTagsTable");
    },
    async get_user() {
      await this.$store.dispatch("SET_USER");
    },
    logout() {
      this.user.login = undefined;
      axios.get("/logout").then((resp) => {
        this.$store.commit("SET_PROJECT", "");
        localStorage.removeItem(
          "x_xsrf_token",
          resp.config.headers["X-XSRF-TOKEN"]
        );
        localStorage.removeItem("project");
        this.$router.push("/");
        this.$store.commit("SET_ENTERED", false);
      });
    },
    async setProjects() {
      await this.$store.dispatch("SET_USER");
    },
  },

  created() {
    if (!this.getProject) {
      this.$router.push("/Home");
    }
    this.get_user();
    this.setProjects();
  },
  computed: {
    getProject() {
      return this.$store.getters.project;
    },
    get_projects() {
      return this.$store.getters.getUser;
    },
    get_choose_project() {
      return this.$store.getters.project;
    },
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    },
  },
};
</script>
