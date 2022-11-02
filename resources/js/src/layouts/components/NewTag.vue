<template>
  <div class="forms">
    <div class="form__create-appeal">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Название</label>
          <b-form-input
            class="form__appeal-input"
            v-model="name"
            objective="text"
            placeholder="Название"
            :state="name !== ''"
          />
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
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Плюс слова клиента"
            rows="5"
            no-resize
            v-model="client_plus_words"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Минус слова клиента</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Минус слова клиента"
            rows="5"
            no-resize
            v-model="client_minus_words"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Плюс слова оператора</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Плюс слова оператора"
            rows="5"
            no-resize
            v-model="operator_plus_words"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Минус слова оператора</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Минус слова оператора"
            rows="5"
            no-resize
            v-model="operator_minus_words"
          />
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
        <span v-if="!enter">Добавить обращение</span>
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
import axios from "axios";
export default {
  components: {
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
    async addAppeal() {
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
      try {
        if (this.name) {
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
              this.enter = true;
              this.$router.push("/Tags");
            });
        } else {
          this.$bvToast.toast("Пожалуйтса заполните все поля", {
            title: `Ошибка`,
            variant: "danger",
            solid: true,
            appendToast: true,
            toaster: "b-toaster-top-center",
            autoHideDelay: 2000,
          });
        }
      } catch (error) {
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
    },
    async CreateAndAddAppeal() {
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
      try {
        if (this.name) {
          await axios.post("api/projects/" + this.project.id + "/tags", {
            name: this.name,
            objective: this.objective,
            client_plus_words: this.temp_client_plus_words[0],
            client_minus_words: this.temp_client_minus_words[0],
            operator_plus_words: this.temp_operator_plus_words[0],
            operator_minus_words: this.temp_operator_minus_words[0],
          });
          this.name = "";
          this.objective = false;
          this.client_plus_words = "";
          this.client_minus_words = "";
          this.operator_plus_words = "";
          this.operator_minus_words = "";
        } else {
          this.$bvToast.toast("Пожалуйтса заполните все поля", {
            title: `Ошибка`,
            variant: "danger",
            solid: true,
            appendToast: true,
            toaster: "b-toaster-top-center",
            autoHideDelay: 2000,
          });
        }
      } catch (error) {
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
  height: 800px;
}
.form__create-appeal {
  height: 200px;
}
</style>