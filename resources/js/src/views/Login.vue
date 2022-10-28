<template>
  <div class="auth auth-v2">
    <!-- Login-->
    <div class="container__auth">
      <b-card-title title-tag="h2" class="font-weight-bold mb-1">
        Добро пожаловать на Vuyxy
      </b-card-title>
      <b-card-text class="mb-2">
        Пожалуйста, войдите в свою учетную запись
      </b-card-text>
      <!-- form -->
        <b-form class="auth-login-form mt-2" @submit.prevent="Login">
          <!-- login -->
          <b-form-group label="Login">
            <validation-provider
              #default="{ errors }"
              name="Login"
              rules="required"
            >
              <b-form-input
                id="login-login"
                v-model="login"
                :state="errors.length > 0 ? false : null"
                name="login-login"
                placeholder="john"
              />
              <small class="text-danger">{{ errors[0] }}</small>
            </validation-provider>
          </b-form-group>

          <!-- forgot password -->
          <b-form-group>
            <div class="d-flex justify-content-between">
              <label for="login-password">Password</label>
              <!-- <b-link :to="{ name: 'auth-forgot-password-v2' }">
                <small>Забыли пароль?</small>
              </b-link> -->
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

          <!-- checkbox -->
          <b-form-group>
            <b-form-checkbox
              id="remember-me"
              v-model="status"
              name="checkbox-1"
            >
              Запомнить меня
            </b-form-checkbox>
          </b-form-group>

          <!-- submit buttons -->
          <b-button type="submit" variant="primary" block>
            <span v-if="!enter">Войти</span>
            <span v-if="enter">Загрузка...</span>
            <b-spinner v-if="enter" small />
          </b-button>
        </b-form>

      <!-- divider -->
      <div class="divider my-2">
        <div class="divider-text">or</div>
      </div>

      <!-- social buttons -->
      <div class="auth-footer-btn d-flex justify-content-center">
        <b-button variant="facebook" href="javascript:void(0)">
          <feather-icon icon="FacebookIcon" />
        </b-button>
        <b-button variant="twitter" href="javascript:void(0)">
          <feather-icon icon="TwitterIcon" />
        </b-button>
        <b-button variant="google" href="javascript:void(0)">
          <feather-icon icon="MailIcon" />
        </b-button>
        <b-button variant="github" href="javascript:void(0)">
          <feather-icon icon="GithubIcon" />
        </b-button>
      </div>

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
  BSpinner,
} from "bootstrap-vue";
import { required } from "@validations";
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
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      status: "",
      password: "",
      login: "",
      enter: false,
      user: [],
      sideImg: require("@/assets/images/pages/login-v2.svg"),
      // validation rulesimport store from '@/store/index'
      required,
      response: "",
    };
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === "password" ? "EyeIcon" : "EyeOffIcon";
    },
  },
  methods: {
    Login() {
      this.enter = true;
      axios.get("/sanctum/csrf-cookie").then((response) => {
        axios
          .post("/login", {
            login: this.login,
            password: this.password,
          })
          .then((resp) => {
            localStorage.setItem(
              "x_xsrf_token",
              resp.config.headers["X-XSRF-TOKEN"]
            );
            this.$router.push("/Home");
          })
          .catch((error) => {
            this.$bvToast.toast("Неверный логин или пароль", {
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
  async mounted() {
    this.$store.commit("SET_ENTERED", false);
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
