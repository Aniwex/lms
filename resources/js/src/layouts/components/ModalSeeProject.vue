<template>
  <div>
    <!-- modal see project -->
    <b-modal
      id="modal__seeProject"
      centered
      title="Просмотр обращения"
      cancel-title="Отмена"
      size="lg"
      ref="modal__window"
      hide-footer
      no-close-on-esc
      no-close-on-backdrop
      @close="close_Function"
    >
      <swiper
        class="swiper-navigations"
        :options="swiperOptions"
        :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
        @slideNextTransitionStart="changeSlideNext"
        @slidePrevTransitionStart="changeSlidePrev"
      >
        <swiper-slide v-for="(data, index) in modalArray" :key="index"
          ><div class="see-project__form">
            <h3 style="margin-top: 30px">Диалог</h3>
            <div
              class="dialog__none"
              v-if="arrayChat === undefined || arrayChat.length === 0"
            >
              <p class="dialog__none-item">Диалог отсутствует</p>
            </div>
            <div class="container__dialog" v-else>
              <div
                v-for="(msgGrp, index) in arrayChat"
                :key="index"
                class="chat-content"
              >
                <p
                  :class="{
                    message_operator: msgGrp.value === 'operator',
                    message_subscriber: msgGrp.value === 'subscriber',
                  }"
                >
                  {{ msgGrp.message }}
                </p>
              </div>
            </div>
            <h3>Данные для редактирования</h3>
            <div class="container__see-project">
              <div class="row__lables">
                <div v-if="user.role.id === 1" class="row__datetime-lables">
                  <label class="row__lables-label">Дата и время</label>
                  <flat-pickr
                    placeholder="Выберите дату и время"
                    v-model="data.datetime"
                    class="form-control row__datetime-pickr"
                    :config="{
                      enableTime: true,
                      datetimeFormat: 'd.m.Y H:i:s',
                      enableSeconds: true,
                    }"
                  />
                </div>
                <div v-if="user.role.id === 1" class="row__duration-lables">
                  <label class="row__lables-label"
                    >Продолжительность звонка</label
                  >
                  <div class="modal__input">
                    <b-button
                      @click="quantity_minus(data.duration)"
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
                    <!-- <b-form-input
                      class="input__number form-control"
                      v-model="data.duration"
                      readonly="readonly"
                    /> -->
                    <b-button
                      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                      variant="primary"
                      type="button"
                      size="sm"
                      @click="quantity_plus(data.duration)"
                    >
                      <plus-icon
                        size="1x"
                        class="plus-icon align-middle mr-25"
                      ></plus-icon>
                    </b-button>
                  </div>
                </div>
                <div v-if="user.role.id === 1" class="row__tag-lables">
                  <label class="row__lables-label">Источник</label>
                  <multiselect
                    v-model="data.source_id"
                    onclick="this.querySelector('input').focus();"
                    :options="options_source_id"
                    selectedLabel="Выбрано"
                    class="multiselect-input"
                    label="name"
                    track-by="name"
                    placeholder="Выберите источник"
                    :showLabels="false"
                  >
                  </multiselect>
                </div>
                <div v-if="user.role.id === 1" class="row__user-lables">
                  <label class="row__lables-label">Пользователь</label>
                  <!-- <b-form-input
                    class="row__user-input"
                    type="text"
                    placeholder="+7 (999) 999-99-99"
                    v-model="data.phone"
                    v-mask="'+7 (###) ###-##-##'"
                    maxlength="18"
                  /> -->
                </div>
                <div
                  v-if="user.role.id === 1 || user.role.id === 2"
                  class="row__tag-lables"
                >
                  <label class="row__lables-label">Тэги</label>
                  <multiselect
                    v-model="data.tags"
                    onclick="this.querySelector('input').focus();"
                    :options="options_tags"
                    selectedLabel="Выбрано"
                    :multiple="true"
                    class="multiselect-input"
                    label="name"
                    track-by="name"
                    placeholder="Выберите тэг"
                    :showLabels="false"
                  >
                  </multiselect>
                </div>
                <div
                  v-if="user.role.id === 1 || user.role.id === 2"
                  class="row__tag-lables"
                >
                  <label class="row__lables-label">Проверка менеджера</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.manager_check"
                    :options="options_manager"
                  >
                  </b-form-select>
                </div>
                <div
                  v-if="user.role.id === 1 || user.role.id === 2"
                  class="row__comment-manager-lables"
                >
                  <label class="row__lables-label">Комментарий менеджера</label>
                  <b-form-textarea
                    class="form__textarea-manager"
                    placeholder="Комментарий менеджера"
                    rows="5"
                    no-resize
                    v-model="data.manager_comment"
                    :value="data.manager_comment"
                  />
                </div>
                <div v-if="user.role.id === 1" class="row__tag-lables">
                  <label class="row__lables-label">Проверка клиента</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.client_check"
                    :options="options_client"
                  >
                  </b-form-select>
                </div>
                <div class="row__comment-client-lables">
                  <label class="row__lables-label">Комментарий клиента</label>
                  <b-form-textarea
                    class="form__textarea-client"
                    placeholder="Комментарий клиента"
                    rows="5"
                    no-resize
                    v-model="data.client_comment"
                    :value="data.client_comment"
                  />
                </div>
                <div class="modal__form-buttons">
                  <!-- <b-button
                    v-if="rows[0].id !== data.id"
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="secondary"
                    @click="prevAppeal"
                  >
                    Назад
                  </b-button> -->
                  <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="secondary"
                    @click="hideModal"
                  >
                    Отменить
                  </b-button>
                  <b-button
                    @click="saveModal"
                    class="form__button-margin"
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="primary"
                  >
                    Сохранить
                  </b-button>
                  <!-- <b-button
                    v-if="rows[rows.length - 1].id !== data.id"
                    @click="nextAppeal"
                    class="form__button-margin"
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="primary"
                  >
                    Вперёд
                  </b-button> -->
                </div>
              </div>
            </div>
          </div>
        </swiper-slide>

        <!-- Arrows -->
        <div slot="button-next" class="swiper-button-next" />
        <div slot="button-prev" class="swiper-button-prev" />
        <div slot="pagination" class="swiper-pagination" />
      </swiper>
    </b-modal>
  </div>
</template>

<script>
import {
  BBadge,
  BPagination,
  BFormGroup,
  BFormInput,
  BFormSelect,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BDropdownGroup,
  BDropdownForm,
  BButton,
  BModal,
  VBModal,
  VBTooltip,
  BFormTextarea,
  BSpinner,
} from "bootstrap-vue";
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
import flatPickr from "vue-flatpickr-component";
import "@core/scss/vue/libs/vue-flatpicker.scss";
import "vue-good-table/dist/vue-good-table.css";
import Ripple from "vue-ripple-directive";
import "@core/scss/vue/libs/vue-select.scss";
import axios from "axios";
import {
  CheckIcon,
  XCircleIcon,
  AlertCircleIcon,
  XSquareIcon,
  EyeIcon,
  TrashIcon,
  Edit2Icon,
  PlusIcon,
  MinusIcon,
} from "vue-feather-icons";
export default {
  components: {
    BSpinner,
    Swiper,
    SwiperSlide,
    BFormTextarea,
    BBadge,
    BModal,
    BPagination,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BDropdown,
    BDropdownItem,
    BDropdownGroup,
    BDropdownForm,
    CheckIcon,
    MinusIcon,
    XCircleIcon,
    AlertCircleIcon,
    XSquareIcon,
    PlusIcon,
    EyeIcon,
    BDropdownDivider,
    BButton,
    flatPickr,
    TrashIcon,
    Edit2Icon,
  },
  props: [
    "user",
    "modalArray",
    "options_source_id",
    "options_tags",
    "options_manager",
    "options_client",
    "client_check",
    "manager_check",
  ],
  directives: {
    Ripple,
    "b-tooltip": VBTooltip,
    "b-modal": VBModal,
  },
  data() {
    return {
      modalArrayNext: [],
      modalArrayPrev: [],
      modalCounter: 0,
      swiperOptions: {
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        pagination: {
          el: ".swiper-pagination",
          type: "progressbar",
        },
      },
      arrayChat: [],
    };
  },
  methods: {
    close_Function() {
    
    },
    quantity_minus(duration) {
      let temp = duration.toString().replace(/[^0-9]/g, "");
      duration = Number(temp);
      if (duration >= 10) {
        duration -= 10;
        this.modalArray[this.modalCounter].duration = "";
        this.modalArray[this.modalCounter].duration += duration + " секунд";
      }
    },
    quantity_plus(duration) {
      duration = duration.toString().replace(/[^0-9]/g, "");
      duration = Number(duration);
      duration += 10;

      this.modalArray[this.modalCounter].duration = "";
      this.modalArray[this.modalCounter].duration += duration + " секунд";
    },
    changeSlideNext() {
      this.modalCounter++;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    changeSlidePrev() {
      this.modalCounter--;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    resetFilters() {
      this.selected.tags = null;
      this.selected.source_id = null;
      this.selected.datetime = null;
      this.selected.manager_check = null;
      this.selected.client_check = null;
      this.selected.deleted = null;
      this.selected.value_range = 0;
      this.phone = "";
      this.sortedFilter = [];
    },
    hideModal() {
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        await axios
          .put("/api/data/" + this.modalArray[this.modalCounter].id, {
            id: this.modalArray[this.modalCounter].id,
            datetime: this.modalArray[this.modalCounter].datetime,
            duration: this.modalArray[this.modalCounter].duration,
            manager_comment: this.modalArray[this.modalCounter].manager_comment,
            source_id: this.modalArray[this.modalCounter].source_id,
            phone: this.modalArray[this.modalCounter].phone,
            tags: this.modalArray[this.modalCounter].tags,
            client_comment: this.modalArray[this.modalCounter].client_comment,
            manager_check: this.modalArray[this.modalCounter].manager_check,
            client_check: this.modalArray[this.modalCounter].client_check,
            dialog: this.modalArray[this.modalCounter].dialog,
          })
          .then(() => {
            this.$refs["modal__window"].hide();
          })
          .catch((error) => {
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
      } catch (error) {}
    },
    // nextAppeal() {
    //   try {
    //     let j = 0;
    //     this.modalPrevCounter = 0;
    //     for (let i in this.getDataTable) {
    //       if (this.getDataTable[i].id === this.modalArrayNext[0].id) {
    //         this.modalCounter = i;
    //         this.modalArrayNext = [];
    //         this.modalArrayNext.push(this.getDataTable[j + 1]);
    //         this.modalArray = this.modalArrayNext[0];
    //       } else {
    //         j++;
    //       }
    //     }
    //   } catch (error) {
    //     console.log(error.message);
    //   }
    // },
    // prevAppeal() {
    //   let j = this.modalCounter;
    //   this.modalPrevCounter = this.modalPrevCounter + 1;
    //   for (let i in this.getDataTable) {
    //     if (this.getDataTable[j - this.modalPrevCounter].id === undefined) {
    //     }
    //     this.modalArrayNext = [];
    //     this.modalArrayNext.push(this.getDataTable[j - this.modalPrevCounter]);
    //     this.modalArray = this.modalArrayNext[0];
    //   }
    // },
    inputSpinButton(duration) {
      this.modalArray[this.modalCounter].duration = duration + " секунд";
    },
  },
  computed: {},
  created() {},
  mounted() {},
};
</script>
<style lang="scss">
@import "~@core/scss/base/pages/app-chat.scss";
@import "~@core/scss/base/pages/app-chat-list.scss";
</style>
