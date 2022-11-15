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
          />
        </div>
        <div class="form__group">
          <label class="form__label">Главный домен</label>
          <div>
            <b-form-input
              class="row__user-input"
              v-model="domain"
              type="text"
              :state="domain !== ''"
              :placeholder="errors.domain ? errors.domain[0] : 'Главный домен'"
            />
            <span style="color: red" class="db__tc" v-if="errors.domain">
              <span v-for="(err, index) in errors.domain" :key="index">{{
                err
              }}</span>
            </span>
          </div>
        </div>

        <div class="form__group">
          <label class="form__label">Список зеркал </label>
          <div>
            <b-form
              ref="form"
              :style="{ height: trHeight }"
              class="repeater-form"
              @submit.prevent="repeateAgain"
            >
              <!-- Row Loop -->
              <div
                v-for="(item, index) in mirrows"
                :id="index"
                :key="index"
                ref="row"
                style="margin: 0 20px 20px 13px"
              >
                <!-- Значение -->

                <b-form-group label="Зеркало">
                  <div>
                    <b-form-input
                      type="text"
                      v-model="item.value"
                      :state="item.value !== ''"
                      :placeholder="
                        errors['mirrows.' + index]
                          ? errors['mirrows.' + index][0]
                          : 'Зеркало'
                      "
                    />
                    <span
                      style="color: red"
                      class="db__tc"
                      v-if="errors['mirrows.' + index]"
                      >{{ errors["mirrows." + index][0] }}</span
                    >
                  </div>
                </b-form-group>
                <hr />
                <!-- Добавить Button -->
                <b-button
                  v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                  variant="primary"
                  @click="repeateAgain"
                  class="btn__plus-integration"
                >
                  <feather-icon icon="PlusIcon" class="mr-25" />
                  <span>Добавить ещё</span>
                </b-button>

                <!-- Удалить Button -->
                <b-button
                  v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                  variant="outline-danger"
                  @click="removeItem(index)"
                  class="btn__delete-integration"
                >
                  <feather-icon icon="XIcon" class="mr-25" />
                  <span>Удалить</span>
                </b-button>
              </div>
            </b-form>
          </div>
        </div>
        <div class="form__group">
          <label class="form__label">Пользователи </label>
          <multiselect
            v-model="users"
            :options="option_users"
            selectLabel="Нажмите enter для выбора"
            deselectLabel="Нажмите enter для удаления"
            selectedLabel="Выбрано"
            :multiple="true"
            class="multiselect-input"
            label="login"
            track-by="login"
            placeholder="Выберите пользовтеля"
          >
          </multiselect>
        </div>
      </div>
    </div>
    <div class="form__buttons">
      <b-button
        to="Projects"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="secondary"
      >
        Отменить
      </b-button>
      <b-button
        @click="CreateandAddProject"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enterAndAdd">Создать и добавить ещё</span>
        <span v-if="enterAndAdd">Загрузка...</span>
        <b-spinner v-if="enterAndAdd" small />
      </b-button>
      <b-button
        @click="addProject"
        v-ripple.400="'rgba(255, 255, 255, 0.15)'"
        variant="primary"
      >
        <span v-if="!enter">Добавить проект</span>
        <span v-if="enter">Загрузка...</span>
        <b-spinner v-if="enter" small />
      </b-button>
    </div>
  </div>
</template>

<script>
import {
  BForm,
  BFormGroup,
  BFormInput,
  BFormSpinbutton,
  BFormSelect,
  BFormCheckbox,
  BFormTextarea,
  BButton,
  BSpinner,
  BRow,
  BCol,
} from "bootstrap-vue";
import flatPickr from "vue-flatpickr-component";
import "@core/scss/vue/libs/vue-flatpicker.scss";
import Ripple from "vue-ripple-directive";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import axios from "axios";
export default {
  components: {
    vSelect,
    BRow,
    BCol,
    BForm,
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
      domain: "",
      users: [],
      enter: false,
      enterAndAdd: false,
      option_users: [],
      trHeight: 600,
      trMargin: 20,
      mirrows: [
        {
          value: "",
        },
      ],
      errors: {},
    };
  },
  methods: {
    repeateAgain() {
      this.mirrows.push({
        value: "",
      });
      this.trHeight += 220;
    },
    removeItem(index) {
      if (this.mirrows.length !== 1) {
        this.mirrows.splice(index, 1);
        this.trHeight -= 220;
      }
    },
    async getUsers() {
      await axios
        .get("api/users")
        .then((response) => {
          this.option_users = response.data.users;
          this.option_users.filter((item) => {
            item["title"] = item.login;
          });
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
    async addProject() {
      try {
        this.enter = true;
        let tempUserId = [];
        let tempArrayMirrow = [];
        this.users.filter((item) => {
          tempUserId.push(item.id);
        });
        this.mirrows.filter((item) => {
          tempArrayMirrow.push(item.value);
        });
        await axios
          .post("/api/projects", {
            name: this.name,
            mirrows: tempArrayMirrow,
            domain: this.domain,
            users: tempUserId,
          })
          .then(() => {
            this.$store.dispatch("SET_PROJECTS");
            this.$router.push("/Projects");
          })
          .catch(() => {
            this.errors = error.response.data.errors;
          });
        await this.$store.dispatch("SET_USER");
      } catch (error) {
        this.enter = false;
        this.errors = error.response.data.errors;
        const vNodesMsg = [`${error.response.data.error}`];
        this.$bvToast.toast([vNodesMsg], {
          name: `Ошибка`,
          variant: "danger",
          solid: true,
          appendToast: true,
          toaster: "b-toaster-top-center",
          autoHideDelay: 3000,
        });
      }
    },
    async CreateandAddProject() {
      try {
        let tempUserId = [];
        let tempArrayMirrow = [];
        this.users.filter((item) => {
          tempUserId.push(item.id);
        });
        this.mirrows.filter((item) => {
          tempArrayMirrow.push(item.value);
        });
        this.enterAndAdd = true;
        await axios
          .post("/api/projects", {
            name: this.name,
            mirrows: tempArrayMirrow,
            domain: this.domain,
            users: tempUserId,
          })
          .then(() => {
            this.$store.dispatch("SET_PROJECTS");
            this.$store.dispatch("SET_USER");
            this.mirrows = [{ value: "" }];
            this.name = "";
            this.domain = "";
            this.users = [];
            this.errors = {};
          });

        this.enterAndAdd = false;
      } catch (error) {
        this.enterAndAdd = false;
        this.errors = error.response.data.errors;
        const vNodesMsg = [`${error.response.data.error}`];
        this.$bvToast.toast([vNodesMsg], {
          name: `Ошибка`,
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
    this.$store.commit("SET_ENTERED", true);
    this.getUsers();
  },
  created() {},
  destroyed() {},
};
</script>

<style scoped>
.form__create-integration {
  width: 1470px;
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
  min-height: 285px;
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
.btn__plus-integration {
  margin: 0;
}
.btn__delete-integration {
  margin-left: 0px;
  float: right;
}
.repeater-form {
  margin-left: -15px;
}
</style>