<template>
  <div class="forms">
    <div class="form__create-sorce">
      <div class="container">
        <div class="form__group">
          <label class="form__label">Интеграция </label>
          <multiselect
            onclick="this.querySelector('input').focus();"
            class="multiselect-input"
            v-model="integration_id"
            :options="options_integration_id"
            label="title"
            track-by="title"
            placeholder="Выберите интеграцию"
            selectLabel="Нажмите enter для выбора"
            deselectLabel="Нажмите enter для удаления"
          >
          </multiselect>
        </div>
        <div class="form__group">
          <label class="form__label">Название </label>
          <div>
            <b-form-input
              class="row__user-input"
              v-model="name"
              type="text"
              :placeholder="errors.name ? errors.name[1] : 'Название'"
              :class="{
                validation__input: errors.name ? true : false,
                validation__input_false: name !== '' ? true : false,
              }"
              @input="errorNullableName"
            />
            <errorValidation
              v-if="errors.name"
              :errors="errors"
              :error="errors.name"
            ></errorValidation>
          </div>
        </div>
        <div class="form__group">
          <label class="form__label">Код </label>
          <div>
            <b-form-input
              class="row__user-input"
              v-model="code"
              type="text"
              :placeholder="errors.code ? errors.code[1] : 'Код'"
              :class="{
                validation__input: errors.code ? true : false,
                validation__input_false: code !== '' ? true : false,
              }"
              @input="errorNullableCode"
            />

            <errorValidation
              v-if="errors.code"
              :errors="errors"
              :error="errors.code"
            ></errorValidation>
          </div>
        </div>
      </div>
    </div>
    <div class="form__buttons">
      <b-button
        to="Sources"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="secondary"
      >
        Отменить
      </b-button>
      <b-button
        @click="CreateAndAddSource"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enterAndAdd">Создать и добавить ещё</span>
        <span v-if="enterAndAdd">Загрузка...</span>
        <b-spinner v-if="enterAndAdd" small />
      </b-button>
      <b-button
        @click="addSource"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enter">Добавить источник</span>
        <span v-if="enter">Загрузка...</span>
        <b-spinner v-if="enter" small />
      </b-button>
    </div>
  </div>
</template>

<script>
import {
  BFormGroup,
  BFormInput,
  BFormSpinbutton,
  BFormSelect,
  BFormCheckbox,
  BFormTextarea,
  BButton,
  BSpinner,
} from "bootstrap-vue";
import flatPickr from "vue-flatpickr-component";
import "@core/scss/vue/libs/vue-flatpicker.scss";
import Ripple from "vue-ripple-directive";
import errorValidation from "../../views/error/errorValidation";
import axios from "axios";
export default {
  components: {
    errorValidation,
    BSpinner,
    BFormInput,
    flatPickr,
    BFormGroup,
    BFormSpinbutton,
    BFormSelect,
    BFormCheckbox,
    BFormTextarea,
    BButton,
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      integration_id: null,
      name: "",
      code: "",
      dataSource: [],
      enter: false,
      enterAndAdd: false,
      options_integration_id: [],
      project: [],
      errors: {},
    };
  },
  methods: {
    errorNullableName() {
      this.errors.name ? (this.errors.name = {}) : "";
    },
    errorNullableCode() {
      this.errors.code ? (this.errors.code = {}) : "";
    },
    async getIntegrations() {
      await axios
        .get("api/integrations")
        .then((response) => {
          this.options_integration_id = response.data.integrations;
        })
        .catch((error) => {
          const vNodesMsg = [`${Object.values(error.response.data.errors)}`];
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
    async addSource() {
      try {
        if (this.integration_id) {
          await axios
            .post("api/projects/" + this.project.id + "/sources", {
              integration_id: this.integration_id.id,
              name: this.name,
              code: this.code,
              data: this.dataSource,
            })
            .then(() => {
              this.$store.dispatch("getSourceTable");
              this.enter = true;
              this.$router.push("/Sources");
            })
            .catch((error) => {
              this.enter = false;
              this.errors = error.response.data.errors;

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
        } else {
          this.$bvToast.toast("Пожалуйста выберите интеграцию", {
            title: `Ошибка`,
            variant: "danger",
            solid: true,
            appendToast: true,
            toaster: "b-toaster-top-center",
            autoHideDelay: 2000,
          });
        }
      } catch (error) {}
    },
    async CreateAndAddSource() {
      try {
        if (this.integration_id) {
          await axios
            .post("api/projects/" + this.project.id + "/sources", {
              integration_id: this.integration_id.id,
              name: this.name,
              code: this.code,
              data: this.dataSource,
            })
            .then(() => {
              this.$store.dispatch("getSourceTable");
              this.enterAndAdd = true;
              this.integration_id = [];
              this.name = "";
              this.code = "";
              this.dataSource = [];
              this.enterAndAdd = false;
              this.errors = {};
            })
            .catch((error) => {
              this.errors = error.response.data.errors;
            });
        } else {
          this.$bvToast.toast("Пожалуйста выберите интеграцию", {
            title: `Ошибка`,
            variant: "danger",
            solid: true,
            appendToast: true,
            toaster: "b-toaster-top-center",
            autoHideDelay: 2000,
          });
        }
      } catch (error) {}
    },
  },
  mounted() {
    if (!this.$store.getters.project) {
      this.$router.push("/Home");
    } else {
      this.getIntegrations();
      this.project = this.$store.getters.project;
    }
    this.$store.commit("SET_ENTERED", true);
  },
};
</script>

<style scoped>
.form__create-sorce {
  width: 1470px;
  height: 390px;
  margin: 0 auto;
  background: #fff;
  border-radius: 15px;
  border-width: 1px;
}
.form__create-sorce.container {
  height: 592px;
}
.container {
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  height: 285px;
}
.form__label {
  display: flex;
  padding: 0px 32px 0px 32px;
  flex: 0 1 40%;
  font-size: 15px;
}
input {
  width: 416px !important;
  height: 36px !important;
  border-radius: 0.5rem;
  border-width: 1px;
  border-color: #bacad6;
  background-color: #fff;
}
.form__group {
  display: flex;

  flex-direction: row;
  margin: 24px 32px 24px 32px;
}

.btn {
  margin-left: 12px;
}
</style>