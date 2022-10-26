<template>
  <div class="forms">
    <div class="form__create-appeal">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Название</label>
          <b-form-input
            class="form__appeal-input"
            v-model="name"
            type="text"
            placeholder="Название"
            :state="name !== ''"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Тип</label>
          <b-form-checkbox v-model="type" value="true">
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
            v-model="plus_words_client"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Минус слова клиента</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Минус слова клиента"
            rows="5"
            no-resize
            v-model="minus_words_client"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Плюс слова оператора</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Плюс слова оператора"
            rows="5"
            no-resize
            v-model="plus_words_operator"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Минус слова оператора</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Минус слова оператора"
            rows="5"
            no-resize
            v-model="minus_words_operator"
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
      type: false,
      plus_words_client: "",
      minus_words_client: "",
      plus_words_operator: "",
      minus_words_operator: "",
      enter: false,
      selectedCheckBox: false,
      form: {
        phone: null,
      },
      dateNtim: null,
    };
  },
  methods: {
    async addAppeal() {
      try {
        if (this.name && this.type) {
          this.enter = true;
          await axios.post("/api/tags", {
            id: Date.now(),
            name: this.name,
            type: this.type,
            plus_words_client: this.plus_words_client,
            minus_words_client: this.minus_words_client,
            plus_words_operator: this.plus_words_operator,
            minus_words_operator: this.minus_words_operator,
          });
          this.$router.push("/Tags");
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
      } catch (error) {}
    },
    async CreateAndAddAppeal() {
      try {
        if (this.name && this.type) {
          await axios.post("/api/tags", {
            id: Date.now(),
            name: this.name,
            type: this.type,
            plus_words_client: this.plus_words_client,
            minus_words_client: this.minus_words_client,
            plus_words_operator: this.plus_words_operator,
            minus_words_operator: this.minus_words_operator,
          });
          this.name = null;
          this.type = 0;
          this.selected.source = null;
          this.plus_words_client = null;
          this.minus_words_client = null;
          this.plus_words_operator = null;
          this.minus_words_operator = null;
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
      } catch (error) {}
    },
    inputSpinButton(call) {
      this.selected.rangeAppeal = call + " секунд";
    },
  },
  mounted() {
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