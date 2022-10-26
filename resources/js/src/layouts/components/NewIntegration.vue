<template>
  <div class="forms">
    <div class="form__create-integration">
      <div class="container">
        <div class="form__group">
          <label class="form__label">Название </label>
          <b-form-input
            class="row__user-input"
            v-model="name"
            type="text"
            placeholder="Название"
            :state="name !== ''"
          />
        </div>
        <div class="form__group">
          <label class="form__label">Код </label>
          <b-form-input
            class="row__user-input"
            v-model="code"
            type="text"
            placeholder="Код"
            :state="code !== ''"
          />
        </div>
      </div>
    </div>
    <div class="form__buttons">
      <b-button
        to="Integration"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="secondary"
      >
        Отменить
      </b-button>
      <b-button
        @click="CreateAndAddIntegration"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enterAndAdd">Создать и добавить ещё</span>
        <span v-if="enterAndAdd">Загрузка...</span>
        <b-spinner v-if="enterAndAdd" small />
      </b-button>
      <b-button
        @click="addIntegration"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enter">Добавить интеграцию</span>
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
      name: "",
      code: "",
      enter: false,
      enterAndAdd: false,
    };
  },
  methods: {
    async addIntegration() {
      try {
        if (this.name && this.code) {
          this.enter = true;
          await axios.post("/api/integrations", {
            id: Date.now(),
            name: this.name,
            code: this.code,
          });
          this.$router.push("/Integration");
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
    async CreateAndAddIntegration() {
      try {
        if (this.name && this.code) {
          this.enterAndAdd = true;
          await axios.post("/api/integrations", {
            id: Date.now(),
            name: this.name,
            code: this.code,
          });
          this.name = "";
          this.code = "";
          this.enterAndAdd = false;
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
.form__create-integration {
  width: 1470px;
  height: 390px;
  margin: 0 auto;
  background: #fff;
  border-radius: 15px;
  border-width: 1px;
}
.form__create-integration.container {
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