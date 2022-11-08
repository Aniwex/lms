<template>
  <div class="custom-search">
    <div class="container__search-from">
      <div class="search__form align-items-center">
        <b-form-input
          v-model="searchTerm"
          placeholder="Поиск"
          type="text"
          class="search__input"
          @input="handleSearch"
        />
      </div>
      <div class="create__appeal" v-if="role_id === 1">
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          variant="primary"
          to="NewIntegration"
          v-b-tooltip.hover.top="'Добавить интеграцию'"
          v-if="this.$route.path === '/Integration'"
        >
          Добавить интеграцию
        </b-button>
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          variant="primary"
          to="NewSource"
          v-b-tooltip.hover.top="'Добавить иcточник'"
          v-if="this.$route.path === '/Sources'"
        >
          Добавить иcточник
        </b-button>
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          variant="primary"
          to="NewTag"
          v-b-tooltip.hover.top="'Добавить тэг'"
          v-if="this.$route.path === '/Tags'"
        >
          Добавить тэг
        </b-button>
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          variant="primary"
          to="NewUser"
          v-b-tooltip.hover.top="'Добавить пользователя'"
          v-if="this.$route.path === '/Users'"
        >
          Добавить пользователя
        </b-button>
        <b-button
          v-ripple.400="'rgba(255, 255, 255, 0.15)'"
          variant="primary"
          to="NewProject"
          v-b-tooltip.hover.top="'Добавить проект'"
          v-if="this.$route.path === '/Projects'"
        >
          Добавить проект
        </b-button>
      </div>
    </div>
  </div>
</template>

<script>
import { BFormInput, BButton, VBTooltip } from "bootstrap-vue";
import Ripple from "vue-ripple-directive";
export default {
  props: ["rows", "role_id", "searchHistory"],
  components: {
    BFormInput,
    BButton,
    Ripple,
  },
  directives: {
    Ripple,
    "b-tooltip": VBTooltip,
  },

  data() {
    return {
      arraySearch: [],
      searchTerm: "",
    };
  },
  methods: {
    handleSearch() {
      this.arraySearch = [];
      let key = Object.keys(this.rows[0]);
      if (this.rows.length) {
        this.rows.filter((item) => {
          for (let k in key) {
            if (
              item[key[k]] !== null &&
              item[key[k]].toString().includes(this.searchTerm)
            ) {
              this.arraySearch.push(item);
            }
          }
        });
      }
      this.arraySearch = [...new Set(this.arraySearch)];
      this.$emit("arraySearch", this.arraySearch);
    },
  },
  mounted() {},
};
</script>

<style>
</style>