<template>
  <div class="forms">
    <div class="form__create-appeal">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Дата и время</label>
          <flat-pickr
            placeholder="Выберите дату и время"
            v-model="dateAppeal"
            class="form__apeal-control row__date-pickr"
            :config="{
              enableTime: true,
              dateFormat: 'd.m.Y H:i:s',
              enableSeconds: true,
            }"
            :class="{
              nonActiveDatePicr: !dateAppeal,
              activeDatePicr: dateAppeal,
            }"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Продолжительность звонка </label>
          <div>
            <b-form-spinbutton
              class="form__spinbutton"
              v-model="selected.rangeAppeal"
              min="0"
              max="1000"
              step="10"
              @input="inputSpinButton"
              :state="
                selected.rangeAppeal === '0 секунд' ||
                selected.rangeAppeal === 0
                  ? false
                  : true
              "
            />
            <label class="form__apeal-label-seconds">Значение в секундах</label>
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Источник</label>
          <div>
            <b-form-select
              class="form__appeal-input"
              v-model="selected.source"
              :options="options_source"
              :state="selected.source === null ? false : true"
            >
            </b-form-select>
            <!-- <b-form-checkbox
              v-model="selectedCheckBox"
              value="true"
              class="form__checkbox"
            >
              С удалённым
            </b-form-checkbox> -->
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Пользователь </label>
          <b-form-input
            class="form__appeal-input"
            type="text"
            placeholder="+7 (999) 999-99-99"
            v-model="form.phone"
            v-mask="'+7 (###) ###-##-##'"
            :state="form.phone === '' || form.phone === null ? false : true"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Тэги </label>
          <b-form-select
            class="form__appeal-input"
            v-model="selected.tag"
            :options="options_tag"
            :state="selected.tag === null ? false : true"
          >
          </b-form-select>
        </div>
      </div>
    </div>
    <h2 class="text-manager">Менеджер</h2>
    <div class="form__create-appeal-two">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Признак целевого</label>
          <b-form-select
            style="display: block; text-align: center"
            class="form__appeal-input"
            v-model="selected.manager"
            :options="options_manager"
            :state="selected.manager === null ? false : true"
          >
          </b-form-select>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Комментарий</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Комментарий"
            rows="5"
            no-resize
            v-model="selected.commentManager"
          />
        </div>
      </div>
    </div>
    <h2 class="text-manager">Клиент</h2>
    <div class="form__create-appeal-three">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Признак целевого</label>
          <b-form-select
            style="display: block; text-align: center"
            class="form__appeal-input"
            v-model="selected.client"
            :options="options_client"
            :state="selected.client === null ? false : true"
          >
          </b-form-select>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Комментарий</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Комментарий"
            rows="5"
            no-resize
            v-model="selected.commentClient"
          />
        </div>
      </div>
    </div>
    <div class="form__buttons">
      <b-button
        to="Home"
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
      selected: {
        tag: null,
        rangeAppeal: 0,
        source: null,
        manager: null,
        client: null,
        redirected: "",
        commentManager: "",
        commentClient: "",
      },
      enter: false,
      flatpicr: false,
      options_tag: [
        { value: null, text: "—" },
        { value: "балкон", text: "балкон" },
        { value: "окна", text: "окна" },
      ],
      options_source: [
        {
          value: null,
          text: "—",
        },
        {
          value: "Задарма: Adwords на nw61.ru + Я.Объявления",
          text: "Задарма: Adwords на nw61.ru + Я.Объявления",
        },
        { value: "Задарма: Оконский Директ", text: "Задарма: Оконский Директ" },
      ],
      options_manager: [
        { value: null, text: "—" },
        { value: "целевой", text: "целевой" },
        { value: "не целевой", text: "не целевой" },
        { value: "не установленный", text: "не установленный" },
      ],
      options_client: [
        { value: null, text: "—" },
        { value: "целевой", text: "целевой" },
        { value: "не целевой", text: "не целевой" },
        { value: "не проверенный", text: "не проверенный" },
      ],
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
        if (
          this.dateAppeal &&
          this.selected.rangeAppeal &&
          this.selected.source &&
          this.form.phone &&
          this.selected.tag &&
          this.selected.client &&
          this.selected.manager &&
          this.form.phone.length === 18
        ) {
          this.enter = true;
          await axios.post("/api/data", {
            id: Date.now(),
            date: this.dateAppeal,
            call: this.selected.rangeAppeal,
            source: this.selected.source,
            user: this.form.phone,
            tag: this.selected.tag,
            client: this.selected.client,
            manager: this.selected.manager,
            commentManager: this.selected.commentManager,
            commentClient: this.selected.commentClient,
          });
          this.$router.push("/Home");
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
        if (
          this.dateAppeal &&
          this.selected.rangeAppeal &&
          this.selected.source &&
          this.form.phone &&
          this.selected.tag &&
          this.selected.client &&
          this.selected.manager &&
          this.form.phone.length === 18
        ) {
          await axios.post("/api/data", {
            id: Date.now(),
            date: this.dateAppeal,
            call: this.selected.rangeAppeal,
            source: this.selected.source,
            user: this.form.phone,
            tag: this.selected.tag,
            client: this.selected.client,
            manager: this.selected.manager,
            commentManager: this.selected.commentManager,
            commentClient: this.selected.commentClient,
          });
          this.dateAppeal = null;
          this.selected.rangeAppeal = 0;
          this.selected.source = null;
          this.form.phone = null;
          this.selected.tag = null;
          this.selected.manager = null;
          this.selected.client = null;
          this.selected.commentManager = null;
          this.selected.commentClient = null;
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
  border:1px solid #ea5455;
}
.activeDatePicr {
  border:1px solid #28c76f;
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
</style>