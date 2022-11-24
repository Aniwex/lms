<template>
  <div class="forms">
    <div class="form__create-appeal">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Дата и время</label>
          <flat-pickr
            placeholder="Выберите дату и время"
            v-model="datetime"
            class="form__apeal-control row__date-pickr"
            :config="{
              enableTime: true,
              dateFormat: 'Y-m-d H:i:s',
              enableSeconds: true,
            }"
          />
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Продолжительность звонка </label>
          <div>
            <div class="modal__input">
              <b-button
                @click="quantity_minus()"
                type="button"
                size="sm"
                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                variant="primary"
              >
                <minus-icon
                  size="1x"
                  class="plus-icon align-middle mr-25"
                ></minus-icon>
              </b-button>
              <b-form-input
                class="input__number form-control"
                v-model="selected.duration"
                type="number"
                :class="{
                  validation__input: errors.duration ? true : false,
                  validation__input_false: selected.duration !== 0,
                }"
              />
              <b-button
                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                variant="primary"
                type="button"
                size="sm"
                style="margin: 0 !important"
                @click="quantity_plus()"
              >
                <plus-icon
                  size="1x"
                  class="plus-icon align-middle mr-25"
                ></plus-icon>
              </b-button>
            </div>
            <label class="form__apeal-label-seconds">Значение в секундах</label>
            <div v-if="selected.duration === 0">
              <errorValidation
                v-if="errors.duration"
                :errors="errors"
                :error="errors.duration"
              ></errorValidation>
            </div>
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Источник</label>
          <div>
            <multiselect
              v-model="selected.source_id"
              :options="getSourceTable"
              selectLabel="Нажмите enter для выбора"
              deselectLabel="Нажмите enter для удаления"
              selectedLabel="Выбрано"
              class="multiselect-input"
              label="name"
              track-by="name"
              placeholder="Выберите источник"
            >
            </multiselect>
            <div v-if="selected.source_id.length === 0">
              <errorValidation
                v-if="errors.source_id"
                :errors="errors"
                :error="errors.source_id"
              ></errorValidation>
            </div>

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
          <div>
            <b-form-input
              class="form__appeal-input"
              type="text"
              placeholder="+7 (999) 999-99-99"
              v-model="phone"
              v-mask="'+7 (###) ###-##-##'"
              :class="{
                validation__input: errors.phone ? true : false,
                validation__input_false: phone !== null && phone !== '',
              }"
            />
            <div v-if="phone === null || phone === ''">
              <errorValidation
                v-if="errors.phone"
                :errors="errors"
                :error="errors.phone"
              ></errorValidation>
            </div>
          </div>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Тэги </label>
          <multiselect
            v-model="tags"
            :options="getTagsTable"
            selectLabel="Нажмите enter для выбора"
            deselectLabel="Нажмите enter для удаления"
            selectedLabel="Выбрано"
            :multiple="true"
            class="multiselect-input"
            label="name"
            track-by="name"
            placeholder="Выберите тэг"
          >
          </multiselect>
        </div>
      </div>
    </div>
    <h2 class="text-manager">Менеджер</h2>
    <div class="form__create-appeal-two">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Признак целевого</label>
          <multiselect
            v-model="selected.manager_check"
            :options="options_manager_check"
            selectLabel="Нажмите enter для выбора"
            deselectLabel="Нажмите enter для удаления"
            selectedLabel="Выбрано"
            class="multiselect-input"
            label="text"
            track-by="text"
            placeholder="Выберите целевое"
          >
          </multiselect>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Комментарий</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Комментарий"
            rows="5"
            no-resize
            v-model="selected.manager_comment"
          />
        </div>
      </div>
    </div>
    <h2 class="text-manager">Клиент</h2>
    <div class="form__create-appeal-three">
      <div class="container__appeal">
        <div class="form__appeal-group">
          <label class="form__apeal-label">Признак целевого</label>
          <multiselect
            v-model="selected.client_check"
            :options="options_client_check"
            selectLabel="Нажмите enter для выбора"
            deselectLabel="Нажмите enter для удаления"
            selectedLabel="Выбрано"
            class="multiselect-input"
            label="text"
            track-by="text"
            placeholder="Выберите целевое"
          >
          </multiselect>
        </div>
        <div class="form__appeal-group">
          <label class="form__apeal-label">Комментарий</label>
          <b-form-textarea
            class="form__appeal-textarea"
            placeholder="Комментарий"
            rows="5"
            no-resize
            v-model="selected.client_comment"
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
import { PlusIcon, MinusIcon } from "vue-feather-icons";
import flatPickr from "vue-flatpickr-component";
import "@core/scss/vue/libs/vue-flatpicker.scss";
import errorValidation from "../../views/error/errorValidation";
import Ripple from "vue-ripple-directive";
import axios from "axios";
export default {
  components: {
    errorValidation,
    PlusIcon,
    MinusIcon,
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
      datetime: "",
      value_spinbutton: "",
      tags: [],
      errors: {},
      selected: {
        duration: 0,
        source_id: [],
        manager_check: "",
        client_check: "",
        redirected: "",
        manager_comment: "",
        client_comment: "",
      },
      temp_manager_comment: [],
      temp_client_comment: [],
      enter: false,
      flatpicr: false,
      options_tags: [{ value: "балкон" }, { value: "окна" }],
      options_source_id: [
        {
          value: null,
          text: "—",
        },
        {
          value: 1,
          text: 1,
        },
        { value: 2, text: 2 },
      ],

      selectedCheckBox: false,
      phone: null,
      dateNtim: null,
    };
  },
  methods: {
    quantity_minus() {
      if (this.selected.duration >= 1) {
        this.selected.duration--;
      }
    },
    quantity_plus() {
      this.selected.duration++;
    },
    async addAppeal() {
      let tempTagsId = [];
      this.tags.filter((item) => {
        tempTagsId.push(item.id);
      });
      try {
        await axios
          .post("api/projects/" + this.getProject.id + "/claims", {
            duration: this.selected.duration,
            datetime: this.datetime,
            source_id: this.selected.source_id.id,
            phone: this.phone,
            manager_check: this.selected.manager_check.value,
            client_check: this.selected.client_check.value,
            manager_comment: this.selected.manager_comment,
            client_comment: this.selected.client_comment,
            tags: tempTagsId,
          })
          .then(() => {
            this.$store.dispatch("getDataTable");
            this.enter = true;
            this.$router.push("/Home");
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
    async CreateAndAddAppeal() {
      try {
        let tempTagsId = [];
        if (this.tags) {
          this.tags.filter((item) => {
            tempTagsId.push(item.id);
          });
        }

        {
          await axios
            .post(" api/projects/" + this.getProject.id + "/claims", {
              duration: this.selected.duration,
              datetime: this.datetime,
              source_id: this.selected.source_id.id,
              phone: this.phone,
              manager_check: this.selected.manager_check.value,
              client_check: this.selected.client_check.value,
              manager_comment: this.selected.manager_comment,
              client_comment: this.selected.client_comment,
              tags: tempTagsId,
            })
            .then(() => {
              this.$store.dispatch("getDataTable");
              this.errors = {};
              this.datetime = null;
              this.selected.duration = 0;
              this.selected.source_id = null;
              this.phone = null;
              this.tags = [];
              this.selected.manager_check = "";
              this.selected.client_check = "";
              this.selected.manager_comment = "";
              this.selected.client_comment = "";
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
        }
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
    // inputSpinButton(duration) {
    //   this.selected.duration = duration + " секунд";
    // },
  },
  computed: {
    getProject() {
      return this.$store.getters.project;
    },
    getTagsTable() {
      return this.$store.getters.getTags;
    },
    getSourceTable() {
      return this.$store.getters.getSources;
    },
    options_manager_check() {
      return this.$store.getters.options_manager_check;
    },
    options_client_check() {
      return this.$store.getters.options_client_check;
    },
  },
  mounted() {
    if (!this.$store.getters.project) {
      this.$router.push("/Home");
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
</style>