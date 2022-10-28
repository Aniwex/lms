<template>
  <div>
    <div class="text-center" v-if="email === ''">
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
      email: "",
      project: "",
    };
  },
  methods: {
    getEmail() {
      axios.get("/sanctum/csrf-cookie").then((response) => {
        axios.get("api/user ").then((response) => {
          this.email = response.data.email;
        });
      });
    },
  },
  mounted() {
    this.getEmail();
    this.$store.commit("SET_ENTERED", true);
  },
};
</script>

<style>
</style>
