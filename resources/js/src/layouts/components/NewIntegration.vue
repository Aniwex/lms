<template>
  <div class="forms">
    <div class="form__create-integration">
      <div class="container">
        <div class="form__group">
          <label class="form__label">Название </label>
          <b-form-input
            class="row__user-input"
            v-model="title"
            type="text"
            placeholder="Название"
            :state="title !== ''"
          />
        </div>
        <div class="form__group">
          <label class="form__label">Код </label>
          <b-form-input
            class="row__user-input"
            v-model="slug"
            type="text"
            placeholder="Код"
            :state="slug !== ''"
          />
        </div>
        <div class="form__group">
          <label class="form__label">Настройки </label>
          <div v-if="config.length">
            <table>
              <tr>
                <th>KEY</th>
                <th>VALUE</th>
              </tr>
              <tr v-for="(c, index) in config" :key="index">
                <th>{{ c.key }}</th>
                <th>{{ c.value }}</th>
              </tr>
            </table>
          </div>
          <label v-else style="font-size: 15px">Настроек нет</label>
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
      title: "",
      slug: "",
      config: [{ key: "test", value: "test" }],
      enter: false,
      enterAndAdd: false,
    };
  },
  methods: {
    async addIntegration() {
      try {
        if (this.title && this.slug) {
          this.enter = true;
          await axios.post("/api/integrations", {
            title: this.title,
            slug: this.slug,
            config: this.config,
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
      } catch (error) {
        console.log(error);
      }
    },
    async CreateAndAddIntegration() {
      try {
        if (this.title && this.slug) {
          this.enterAndAdd = true;
          await axios.post("/api/integrations", {
            id: Date.now(),
            title: this.title,
            slug: this.slug,
          });
          this.title = "";
          this.slug = "";
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
table {
  border-collapse: collapse;
  width: 420px;
  /*убираем пустые промежутки между ячейками*/
  border: 1px solid black;
  /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
}
th,
td {
  border: 1px solid black;
  padding: 10px 15px;
}
th {
  width: 20%;
}
td:first-child {
  width: 320%;
}
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
</style>