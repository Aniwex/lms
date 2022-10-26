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
    </div>
    <div v-if="user.email === undefined">
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
            <p class="user-name font-weight-bolder mb-0">
              {{ user.name }}
            </p>
            <span class="user-status">{{ user.email }}</span>
          </div>
          <b-avatar
            size="40"
            variant="light-primary"
            badge
            :src="require('@/assets/images/avatars/13-small.png')"
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
  BLink,
  BNavbarNav,
  BNavItemDropdown,
  BDropdownItem,
  BDropdownDivider,
  BAvatar,
  BSpinner,
  BButton,
  VBTooltip,
} from "bootstrap-vue";
import DarkToggler from "@core/layouts/components/app-navbar/components/DarkToggler.vue";
import axios from "axios";
export default {
  components: {
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
    };
  },
  methods: {
    get_user() {
      axios.get("/sanctum/csrf-cookie").then((response) => {
        axios.get("api/user").then((response) => {
          this.user = response.data;
          const vNodesMsg = [`Вы успешно вошли как  ${response.data.name}`];
          this.$bvToast.toast([vNodesMsg], {
            title: `Добро пожаловать`,
            variant: "success",
            solid: true,
            appendToast: true,
            toaster: "b-toaster-top-center",
            autoHideDelay: 3000,
          });
        });
      });
    },
    logout() {
      this.user.email = undefined;
      axios.post("/logout").then((resp) => {
        localStorage.removeItem(
          "x_xsrf_token",
          resp.config.headers["X-XSRF-TOKEN"]
        );
        this.$router.push("/");
        this.$store.commit("SET_ENTERED", false);
      });
      //
      // this.$store.dispatch("logout").then(() => {
      //   this.$router.push("/");
      // });
    },
  },

  created() {
    this.get_user();
  },
  computed: {
    isLoggedIn() {
      return this.$store.getters.isLoggedIn;
    },
  },
};
</script>
