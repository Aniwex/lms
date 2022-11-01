<template>
  <div class="forms">
    <div class="form__create-integration">
      <div class="container" :style="{ height: trHeight + 'px' }">
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
          <div>
            <b-form
              ref="form"
              :style="{ height: trHeight }"
              class="repeater-form"
              @submit.prevent="repeateAgain"
            >
              <!-- Row Loop -->
              <div
                v-for="(item, index) in config"
                :id="index"
                :key="index"
                ref="row"
                :style="{ margin: trMargin + 'px' }"
              >
                <!-- Ключ -->
                <b-form-group label="Ключ">
                  <b-form-input
                    type="text"
                    placeholder="Ключ"
                    v-model="item.key"
                    :state="item.key !== null"
                  />
                </b-form-group>
                <!-- Значение -->

                <b-form-group label="Значение">
                  <b-form-input
                    type="text"
                    placeholder="Значение"
                    v-model="item.value"
                    :state="item.value !== null"
                  />
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
import axios from "axios";
export default {
  components: {
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
      title: "",
      slug: "",
      config: [
        {
          key: null,
          value: null,
        },
      ],
      enter: false,
      enterAndAdd: false,
      trHeight: 600,
      trMargin: 20,
      items: [
        {
          id: 1,
          selected: "male",
          selected1: "designer",
          prevHeight: 0,
        },
      ],
    };
  },
  methods: {
    repeateAgain() {
      this.config.push({
        key: null,
        value: null,
      });
      this.trHeight += 220;
    },
    removeItem(index) {
      if (this.config.length !== 1) {
        this.config.splice(index, 1);
        this.trHeight -= 220;
      }
    },

    async addIntegration() {
      try {
        this.enter = true;
        await axios
          .post("/api/integrations", {
            title: this.title,
            slug: this.slug,
            config: this.config,
          })
          .then(() => this.$router.push("/Integration"));
      } catch (error) {
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
      }
    },
    async CreateAndAddIntegration() {
      try {
        this.enterAndAdd = true;
        await axios
          .post("/api/integrations", {
            id: Date.now(),
            title: this.title,
            slug: this.slug,
            config: this.config,
          })
          .then(() => {
            this.config = [{ key: null, value: null }];
            this.title = "";
            this.slug = "";
            this.trHeight = 600;
          });

        this.enterAndAdd = false;
      } catch (error) {
        this.enterAndAdd = false;
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
    this.$store.commit("SET_ENTERED", true);
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