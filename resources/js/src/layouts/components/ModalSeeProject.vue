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
      @close="closeWindow"
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
                <div v-if="user.role.id === 1" class="row__tag-lables">
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
                <div v-if="user.role.id === 1" class="row__tag-lables">
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
                    <b-form-input
                      class="input__number form-control"
                      v-model="data.duration.original"
                    />
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
                    v-model="data.source"
                    onclick="this.querySelector('input').focus();"
                    :options="options_source"
                    selectedLabel="Выбрано"
                    class="multiselect-input mutiselect-margin"
                    label="name"
                    track-by="name"
                    placeholder="Выберите источник"
                    :showLabels="false"
                  >
                  </multiselect>
                </div>
                <div v-if="user.role.id === 1" class="row__user-lables">
                  <label class="row__lables-label">Пользователь</label>
                  <b-form-input
                    class="row__user-input"
                    type="text"
                    placeholder="+7 (999) 999-99-99"
                    v-model="data.phone.formatted"
                    v-mask="'+7 (###) ###-##-##'"
                    maxlength="18"
                  />
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
                    class="multiselect-input mutiselect-margin"
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
                  <multiselect
                    v-model="chooseManager"
                    onclick="this.querySelector('input').focus();"
                    :options="options_manager"
                    selectedLabel="Выбрано"
                    class="multiselect-input mutiselect-margin"
                    label="text"
                    track-by="text"
                    placeholder="Выберите источник"
                    :showLabels="false"
                  >
                  </multiselect>
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
                    v-model="chooseManagerComment"
                  />
                </div>
                <div v-if="user.role.id === 1" class="row__tag-lables">
                  <label class="row__lables-label">Проверка клиента</label>
                  <multiselect
                    v-model="chooseClient"
                    onclick="this.querySelector('input').focus();"
                    :options="options_client"
                    selectedLabel="Выбрано"
                    class="multiselect-input mutiselect-margin"
                    label="text"
                    track-by="text"
                    placeholder="Выберите источник"
                    :showLabels="false"
                  >
                  </multiselect>
                </div>
                <div class="row__comment-client-lables">
                  <label class="row__lables-label">Комментарий клиента</label>
                  <b-form-textarea
                    class="form__textarea-client"
                    placeholder="Комментарий клиента"
                    rows="5"
                    no-resize
                    v-model="chooseClientComment"
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
    "options_source",
    "options_tags",
    "options_manager",
    "options_client",
    "client_check",
    "manager_check",
    "project",
  ],
  directives: {
    Ripple,
    "b-tooltip": VBTooltip,
    "b-modal": VBModal,
  },
  data() {
    return {
      chooseManager: null,
      chooseClient: null,
      chooseManagerComment: null,
      chooseClientComment: null,
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
    closeWindow() {
      this.$store.commit("SET_CREATE_MODAL_WINDOW", false);
    },
    quantity_minus(duration) {
      if (duration.original >= 1) {
        duration.original--;
      }
    },
    quantity_plus(duration) {
      duration.original++;
    },
    changeSlideNext() {
      this.modalCounter++;
      this.chooseManager = this.modalArray[this.modalCounter].manager.check;
      this.chooseClient = this.modalArray[this.modalCounter].client.check;
      this.chooseManagerComment =
        this.modalArray[this.modalCounter].manager.comment;
      this.chooseClientComment =
        this.modalArray[this.modalCounter].client.comment;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    changeSlidePrev() {
      this.modalCounter--;
      this.chooseManager = this.modalArray[this.modalCounter].manager.check;
      this.chooseClient = this.modalArray[this.modalCounter].client.check;
      this.chooseManagerComment =
        this.modalArray[this.modalCounter].manager.comment;
      this.chooseClientComment =
        this.modalArray[this.modalCounter].client.comment;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    hideModal() {
      this.$refs["modal__window"].hide();
      this.$store.commit("SET_CREATE_MODAL_WINDOW", false);
    },
    async saveModal() {
      if (this.chooseManager === null) {
        this.chooseManager = {
          value: "unidentified",
          text: "не установленный",
        };
      }
      if (this.chooseClient === null) {
        this.chooseClient = {
          value: "unchecked",
          text: "не проверенный",
        };
      }
      try {
        this.modalArray[this.modalCounter].phone.original = this.modalArray[
          this.modalCounter
        ].phone.formatted.replace(/[^0-9]/g, "");
        let tempTagsId = [];
        this.modalArray[this.modalCounter].tags.filter((item) => {
          tempTagsId.push(item.id);
        });

        await axios
          .put(
            " api/projects/" +
              this.project.id +
              "/claims/" +
              this.modalArray[this.modalCounter].id,
            {
              id: this.modalArray[this.modalCounter].id,
              datetime: this.modalArray[this.modalCounter].datetime,
              duration: this.modalArray[this.modalCounter].duration.original,
              manager_comment:
                this.modalArray[this.modalCounter].manager_comment,
              source_id: this.modalArray[this.modalCounter].source.id,
              phone: this.modalArray[this.modalCounter].phone.original,
              tags: tempTagsId,
              client_comment: this.modalArray[this.modalCounter].client_comment,
              manager_check: this.chooseManager.value,
              client_check: this.chooseClient.value,
              dialog: this.modalArray[this.modalCounter].dialog,
              manager_comment: this.chooseManagerComment,
              client_comment: this.chooseClientComment,
            }
          )
          .then(() => {
            this.modalArray[this.modalCounter].manager.check.value =
              this.chooseManager.value;
            this.modalArray[this.modalCounter].manager.check.text =
              this.chooseManager.text;

            this.modalArray[this.modalCounter].client.check.value =
              this.chooseClient.value;
            this.modalArray[this.modalCounter].client.check.text =
              this.chooseClient.text;

            this.modalArray[this.modalCounter].manager.comment =
              this.chooseManagerComment;

            this.modalArray[this.modalCounter].client.comment =
              this.chooseClientComment;

            this.modalArray[this.modalCounter].duration.formatted =
              Math.floor(
                this.modalArray[this.modalCounter].duration.original / 60
              ) -
              Math.floor(
                this.modalArray[this.modalCounter].duration.original / 60 / 60
              ) *
                60 +
              " мин" +
              " " +
              (this.modalArray[this.modalCounter].duration.original % 60) +
              " сек";
            this.$refs["modal__window"].hide();
            this.$store.commit("SET_CREATE_MODAL_WINDOW", false);
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
  created() {
    this.chooseManager = this.modalArray[this.modalCounter].manager.check;
    this.chooseClient = this.modalArray[this.modalCounter].client.check;
    this.chooseManagerComment =
      this.modalArray[this.modalCounter].manager.comment;
    this.chooseClientComment =
      this.modalArray[this.modalCounter].client.comment;
  },
  mounted() {},
};
</script>
<style lang="scss">
@import "~@core/scss/base/pages/app-chat.scss";
@import "~@core/scss/base/pages/app-chat-list.scss";
</style>
