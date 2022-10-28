<template>
  <div>
    <div class="text-center" v-if="user.login === ''">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <good-table v-else />
  </div>
</template>

<script>
import { BButton, BCard, BCardText, BLink, BSpinner } from "bootstrap-vue";
import GoodTable from "../layouts/components/GoodTable.vue";
import axios from "axios";
export default {
  components: {
    BButton,
    BSpinner,
    BCard,
    BCardText,
    BLink,
    GoodTable,
  },
  data() {
    return {
      user: "",
      project: "",
    };
  },
  methods: {
    getUser() {
      axios.get("/sanctum/csrf-cookie").then((response) => {
        axios.get("api/user ").then((response) => {
          this.user = response.data;
        });
      });
    },
  },
  mounted() {
    this.getUser();
    this.$store.commit("SET_ENTERED", true);
  },
};
</script>

<style>
</style>
