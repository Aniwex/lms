<template>
  <div class="forms">
    <div class="form__create-appeal">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Название</label>
          <div>
            <b-form-input
              class="form__appeal-input"
              v-model="name"
              objective="text"
              placeholder="Название"
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
        <div class="form__appeal-group">
          <label class="form__apeal-label">Тип</label>
          <b-form-checkbox v-model="objective">
            Целевой (да/нет)
          </b-form-checkbox>
        </div>
      </div>
    </div>
    <h2 class="text-manager">Ключевая информация</h2>
    <div class="form__create-appeal-two">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Плюс слова клиента</label>
          <div>
            <b-form-textarea
              class="form__appeal-textarea"
              placeholder="Плюс слова клиента"
              rows="5"
              no-resize
              v-model="client_plus_words"
              :class="{
                validation__input: errors['client_plus_words.0'] ? true : false,
                validation__input_false:
                  client_plus_words !== '' ? true : false,
              }"
              @input="errorNullableClientPlusWords"
            />
            <errorValidation
              v-if="errors['client_plus_words.0']"
              :errors="errors"
              :error="errors['client_plus_words.0']"
            ></errorValidation>
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Минус слова клиента</label>
          <div>
            <b-form-textarea
              class="form__appeal-textarea"
              placeholder="Минус слова клиента"
              rows="5"
              no-resize
              v-model="client_minus_words"
              :class="{
                validation__input: errors['client_minus_words.0']
                  ? true
                  : false,
                validation__input_false:
                  client_minus_words !== '' ? true : false,
              }"
              @input="errorNullableClientMinusWords"
            />
            <div v-if="client_minus_words === ''">
              <errorValidation
                v-if="errors['client_minus_words.0']"
                :errors="errors"
                :error="errors['client_minus_words.0']"
              ></errorValidation>
            </div>
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Плюс слова оператора</label>
          <div>
            <b-form-textarea
              class="form__appeal-textarea"
              placeholder="Плюс слова оператора"
              rows="5"
              no-resize
              v-model="operator_plus_words"
              :class="{
                validation__input: errors['operator_plus_words.0']
                  ? true
                  : false,
                validation__input_false:
                  operator_plus_words !== '' ? true : false,
              }"
              @input="errorNullableOperatorPlusWords"
            />

            <errorValidation
              v-if="errors['operator_plus_words.0']"
              :errors="errors"
              :error="errors['operator_plus_words.0']"
            ></errorValidation>
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Минус слова оператора</label>
          <div>
            <b-form-textarea
              class="form__appeal-textarea"
              placeholder="Минус слова оператора"
              rows="5"
              no-resize
              v-model="operator_minus_words"
              :class="{
                validation__input: errors['operator_minus_words.0']
                  ? true
                  : false,
                validation__input_false:
                  operator_minus_words !== '' ? true : false,
              }"
              @input="errorNullableOperatorMinusWords"
            />
            <errorValidation
              v-if="errors['operator_minus_words.0']"
              :errors="errors"
              :error="errors['operator_minus_words.0']"
            ></errorValidation>
          </div>
        </div>
      </div>
    </div>
    <div class="form__buttons">
      <b-button
        to="Tags"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="secondary"
      >
        Отменить
      </b-button>
      <b-button
        @click="CreateAndAddAppeal"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        Создать и добавить ещё
      </b-button>
      <b-button
        @click="addAppeal"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enter">Добавить тэг</span>
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
import errorValidation from "../../views/error/errorValidation";
import "@core/scss/vue/libs/vue-flatpicker.scss";
import Ripple from "vue-ripple-directive";
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
      dateAppeal: null,
      value_spinbutton: "",
      name: "",
      errors: {},
      objective: false,
      client_plus_words: "",
      client_minus_words: "",
      operator_plus_words: "",
      operator_minus_words: "",
      enter: false,
      selectedCheckBox: false,
      form: {
        phone: null,
      },
      dateNtim: null,
      project: [],
      temp_client_plus_words: [],
      temp_client_minus_words: [],
      temp_operator_plus_words: [],
      temp_operator_minus_words: [],
    };
  },
  methods: {
    errorNullableName() {
      this.errors.name ? (this.errors.name = {}) : "";
    },
    errorNullableClientPlusWords() {
      this.errors.client_plus_words ? (this.errors.client_plus_words = {}) : "";
    },
    errorNullableClientMinusWords() {
      this.errors.client_minus_words
        ? (this.errors.client_minus_words = {})
        : "";
    },
    errorNullableOperatorPlusWords() {
      this.errors.operator_plus_words
        ? (this.errors.operator_plus_words = {})
        : "";
    },
    errorNullableOperatorMinusWords() {
      this.errors.operator_minus_words
        ? (this.errors.operator_minus_words = {})
        : "";
    },
    async addAppeal() {
      try {
        this.temp_client_plus_words = [];
        this.temp_client_minus_words = [];
        this.temp_operator_plus_words = [];
        this.temp_operator_minus_words = [];
        this.temp_client_plus_words.push(
          this.client_plus_words.split(/(?=\/)|\s/).filter((item) => {
            return item !== "";
          })
        );
        this.temp_client_minus_words.push(
          this.client_minus_words.split(/(?=\/)|\s/).filter((item) => {
            return item !== "";
          })
        );
        this.temp_operator_plus_words.push(
          this.operator_plus_words.split(/(?=\/)|\s/).filter((item) => {
            return item !== "";
          })
        );
        this.temp_operator_minus_words.push(
          this.operator_minus_words.split(/(?=\/)|\s/).filter((item) => {
            return item !== "";
          })
        );
        console.log(this.temp_client_plus_words);
        await axios
          .post("api/projects/" + this.project.id + "/tags", {
            name: this.name,
            objective: this.objective,
            client_plus_words: this.temp_client_plus_words[0],
            client_minus_words: this.temp_client_minus_words[0],
            operator_plus_words: this.temp_operator_plus_words[0],
            operator_minus_words: this.temp_operator_minus_words[0],
          })
          .then(() => {
            this.$store.dispatch("getTagsTable");
            this.enter = true;
            this.$router.push("/Tags");
          })
          .catch((error) => {
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
      } catch (error) {}
    },
    async CreateAndAddAppeal() {
      try {
        this.temp_client_plus_words = [];
        this.temp_client_minus_words = [];
        this.temp_operator_plus_words = [];
        this.temp_operator_minus_words = [];
        this.temp_client_plus_words.push(
          this.client_plus_words.split(/(?=\/)|\s/)
        );
        this.temp_client_minus_words.push(
          this.client_minus_words.split(/(?=\/)|\s/)
        );
        this.temp_operator_plus_words.push(
          this.operator_plus_words.split(/(?=\/)|\s/)
        );
        this.temp_operator_minus_words.push(
          this.operator_minus_words.split(/(?=\/)|\s/)
        );
        await axios
          .post("api/projects/" + this.project.id + "/tags", {
            name: this.name,
            objective: this.objective,
            client_plus_words: this.temp_client_plus_words[0],
            client_minus_words: this.temp_client_minus_words[0],
            operator_plus_words: this.temp_operator_plus_words[0],
            operator_minus_words: this.temp_operator_minus_words[0],
          })
          .then(() => {
            this.name = "";
            this.objective = false;
            this.client_plus_words = "";
            this.client_minus_words = "";
            this.operator_plus_words = "";
            this.operator_minus_words = "";
            this.$store.dispatch("getTagsTable");
            this.errors = {};
          })
          .catch((error) => {
            this.errors = error.response.data.errors;
            this.errors;
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
      } catch (error) {
        const vNodesMsg = [`${error}`];
        this.$bvToast.toast([vNodesMsg], {
          title: `Ошибка`,
          variant: "danger",
          solid: true,
          appendToast: true,
          toaster: "b-toaster-top-center",
          autoHideDelay: 3000,
        });
      }
    },
    inputSpinButton(call) {
      this.selected.rangeAppeal = call + " секунд";
    },
  },

  mounted() {
    if (!this.$store.getters.project) {
      this.$router.push("/Home");
    } else {
      this.project = this.$store.getters.project;
    }
    this.$store.commit("SET_ENTERED", true);
  },
};
</script>

<style scoped>
.nonActiveDatePicr {
  border: 1px solid #ea5455;
}
.activeDatePicr {
  border: 1px solid #28c76f;
  background: #fff;
}
.text-manager {
  margin-top: 30px !important;
  margin-bottom: 12px !important;
}
.form__spinbutton {
  width: 416px !important;
  height: 36px;
}
.btn {
  margin-left: 12px;
}
.form__appeal-textarea {
  width: 416px;
  height: 150px;
  margin: 0 0;
}
.form__create-appeal-two {
  height: 900px;
}
.form__create-appeal {
  height: 200px;
}
</style>