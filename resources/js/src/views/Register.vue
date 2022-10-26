<template>
  <div class="register auth-v2">
    <!-- Login-->
    <div class="container__register">
      <b-card-title title-tag="h2" class="font-weight-bold mb-1">
        Добро пожаловать на Vuyxy
      </b-card-title>
      <b-card-text class="mb-2">
        Пожалуйста, зарегистрируйте аккаунт
      </b-card-text>

      <!-- form -->
      <validation-observer ref="loginValidation">
        <b-form class="auth-login-form mt-2" @submit.prevent="Registration">
          <!-- name -->
          <b-form-group label="Name">
            <validation-provider
              #default="{ errors }"
              name="name"
              rules="required"
            >
              <b-form-input
                id="login-name"
                v-model="name"
                :state="errors.length > 0 ? false : null"
                placeholder="John"
              />
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
          <!-- email -->
          <b-form-group label="Email" label-for="login-email">
            <validation-provider
              #default="{ errors }"
              name="Email"
              rules="required|email"
            >
              <b-form-input
                id="login-email"
                v-model="email"
                :state="errors.length > 0 ? false : null"
                name="login-email"
                type="email"
                placeholder="john@example.com"
              />
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>

          <!-- forgot password -->
          <b-form-group>
            <div class="d-flex justify-content-between">
              <label for="login-password">Password</label>
            </div>
            <validation-provider
              #default="{ errors }"
              name="Password"
              rules="required"
            >
              <b-input-group
                class="input-group-merge"
                :class="errors.length > 0 ? 'is-invalid' : null"
              >
                <b-form-input
                  id="login-password"
                  v-model="password"
                  :state="errors.length > 0 ? false : null"
                  class="form-control-merge"
                  :type="passwordFieldType"
                  name="login-password"
                  placeholder="············"
                />
                <b-input-group-append is-text>
                  <feather-icon
                    class="cursor-pointer"
                    :icon="passwordToggleIcon"
                    @click="togglePasswordVisibility"
                  />
                </b-input-group-append>
              </b-input-group>
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
          <!-- forgot password -->
          <b-form-group>
            <div class="d-flex justify-content-between">
              <label for="login-password">Password confirm</label>
            </div>
            <validation-provider
              #default="{ errors }"
              name="Password"
              rules="required"
            >
              <b-input-group
                class="input-group-merge"
                :class="errors.length > 0 ? 'is-invalid' : null"
              >
                <b-form-input
                  id="login-password"
                  v-model="password_confirmation"
                  :state="errors.length > 0 ? false : null"
                  class="form-control-merge"
                  :type="passwordFieldType"
                  name="login-password"
                  placeholder="············"
                />
                <b-input-group-append is-text>
                  <feather-icon
                    class="cursor-pointer"
                    :icon="passwordToggleIcon"
                    @click="togglePasswordVisibility"
                  />
                </b-input-group-append>
              </b-input-group>
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>
          <div class="form-registration__buttons">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              to="/"
              v-b-tooltip.hover.bottom="'Назад'"
            >
              Назад
            </b-button>
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              v-b-tooltip.hover.bottom="'Зарегистрировать'"
              type="submit"
            >
              <span v-if="!enter">Зарегистрировать аккаунт</span>
              <b-spinner v-if="enter" small />
              <span v-if="enter">Загрузка...</span>
            </b-button>
          </div>
        </b-form>
      </validation-observer>
      <!-- /Login-->
    </div>
  </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from "vee-validate";
import VuexyLogo from "@core/layouts/components/Logo.vue";
import axios from "axios";
import {
  BRow,
  BCol,
  BLink,
  BFormGroup,
  BFormInput,
  BInputGroupAppend,
  BInputGroup,
  BFormCheckbox,
  BCardText,
  BCardTitle,
  BImg,
  BForm,
  BButton,
  VBTooltip,
  BSpinner,
} from "bootstrap-vue";
import { required, email } from "@validations";
import { togglePasswordVisibility } from "@core/mixins/ui/forms";
import Ripple from "vue-ripple-directive";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
export default {
  components: {
    BSpinner,
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    VuexyLogo,
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
    "b-tooltip": VBTooltip,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      status: "",
      password: "",
      email: "",
      name: "",
      password_confirmation: "",
      user: [],
      enter: false,
      sideImg: require("@/assets/images/pages/login-v2.svg"),
      // validation rulesimport store from '@/store/index'
      required,
      email,
      response: "",
    };
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === "password" ? "EyeIcon" : "EyeOffIcon";
    },
  },
  methods: {
    Registration() {
      if (this.password.length < 7) {
        this.$bvToast.toast("Длина пароля должна быть не менее 8 символов", {
          title: `Ошибка`,
          variant: "danger",
          solid: true,
          appendToast: true,
          toaster: "b-toaster-top-center",
          autoHideDelay: 2000,
        });
      } else {
        if (this.password === this.password_confirmation) {
          this.enter = true;
          axios.get("/sanctum/csrf-cookie").then((response) => {
            axios
              .post("/register", {
                name: this.name,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
              })
              .then((item) => {
                localStorage.setItem(
                  "x_xsrf_token",
                  item.config.headers["X-XSRF-TOKEN"]
                );
                this.$router.push("/");
              })
              .catch((error) => {
                this.$bvToast.toast("Аккаунт с таким email уже существует", {
                  title: `Ошибка`,
                  variant: "danger",
                  solid: true,
                  appendToast: true,
                  toaster: "b-toaster-top-center",
                  autoHideDelay: 2000,
                });
                this.enter = false;
              });
          });
        } else {
          this.enter = false;
          this.$bvToast.toast("Пароли не совпадают", {
            title: `Ошибка`,
            variant: "danger",
            solid: true,
            appendToast: true,
            toaster: "b-toaster-top-center",
            autoHideDelay: 2000,
          });
        }
      }
    },

    validationForm() {
      // this.$refs.loginValidation.validate().then((success) => {
      //   if (success) {
      //     this.$toast({
      //       component: ToastificationContent,
      //       props: {
      //         title: "Form Submitted",
      //         icon: "EditIcon",
      //         variant: "success",
      //       },
      //     });
      //   }
      // });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@core/scss/vue/pages/page-auth.scss";
.auth {
  height: 700px !important;
  display: flex;
}
.container__auth {
  display: flex;
  flex-direction: column;
  margin: 0 auto;
}
.auth-footer-btn a {
  margin-right: 10px;
}
</style>
