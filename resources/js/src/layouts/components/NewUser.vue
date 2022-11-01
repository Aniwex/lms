<template>
  <div class="forms">
    <div class="form__create-sorce">
      <div class="container">
        <div class="form__group">
          <label class="form__label">Логин </label>
          <b-form-input
            class="row__user-input"
            v-model="login"
            type="text"
            placeholder="Логин"
            :state="login !== ''"
          />
        </div>
        <div class="form__group">
          <label class="form__label">Пароль </label>
          <b-form-input
            class="row__user-input"
            v-model="password"
            type="text"
            placeholder="Пароль"
            :state="password !== ''"
          />
        </div>
        <div class="form__group">
          <label class="form__label">Id роли </label>
          <b-form-input
            class="row__user-input"
            v-model="role_id"
            type="text"
            placeholder="Id роли"
            :state="role_id !== ''"
          />
        </div>
      </div>
    </div>
    <div class="form__buttons">
      <b-button
        to="Users"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="secondary"
      >
        Отменить
      </b-button>
      <b-button
        @click="CreateAndAddUser"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enterAndAdd">Создать и добавить ещё</span>
        <span v-if="enterAndAdd">Загрузка...</span>
        <b-spinner v-if="enterAndAdd" small />
      </b-button>
      <b-button
        @click="addUser"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enter">Добавить пользователя</span>
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
      login: "",
      password: "",
      role_id: "",
      enter: false,
      enterAndAdd: false,
    };
  },
  methods: {
    async addUser() {
      try {
        if (this.login && this.password && this.role_id) {
          this.enter = true;
          await axios
            .post("/api/users", {
              login: this.login,
              password: this.password,
              role_id: this.role_id,
            })
            .then(() => {
              this.$router.push("/Users");
            })
            .catch((error) => {
              this.enter = false;
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
          this.$bvToast.toast("Пожалуйста заполните все поля", {
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
    async CreateAndAddUser() {
      try {
        if (this.login && this.password && this.role_id) {
          this.enterAndAdd = true;
          await axios
            .post("/api/users", {
              login: this.login,
              password: this.password,
              role_id: this.role_id,
            })
            .catch((error) => {
              this.enter = false;
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
          this.login = "";
          this.password = "";
          this.role_id = "";
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
  },
  mounted() {
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