<template>
  <div>
    <div class="text-center" v-if="!getDT || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      @arraySearch="pushArraySearch"
      :rows="rows"
      v-if="getDT && user"
      :role_id="user.role.id"
    />
    <!-- filers -->
    <filters
      @sortedFilter="pushSortedFilter"
      @arrayCheckboxUser="pushChangeCheckBox"
      @selected="pushSelected"
      :rowSelection="rowSelection"
      :rows="rows"
      :role_id="user.role.id"
      :options_source="options_source"
      v-if="getDT && user"
    />

    <!-- table -->
    <vue-good-table
      v-if="getDT"
      :columns="columns"
      :rows="sorted"
      :search-options="{
        enabled: true,
      }"
      :select-options="{
        enabled: true,
        selectOnCheckboxOnly: true, // only select when checkbox is clicked instead of the row
        selectionInfoClass: 'custom-class',
        selectionText: 'rows selected',
        clearSelectionText: 'clear',
        disableSelectInfo: true, // disable the select info panel on top
        selectAllByGroup: true, // when used in combination with a grouped table, add a checkbox in the header row to check/uncheck the entire group
      }"
      :pagination-options="{
        enabled: true,
        perPage: pageLength,
      }"
      @on-selected-rows-change="selectionChanged"
      @on-cell-click="onCellClick"
    >
      <template slot="table-row" slot-scope="props">
        <!-- Column: Name -->
        <!-- Column: Date -->
        <span v-if="props.column.field === 'Date'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.date }}</span>
        </span>
        <!-- Column: call -->
        <span
          v-else-if="props.column.field === 'call'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.call }}</span>
        </span>
        <!-- Column: source -->
        <span v-else-if="props.column.field === 'source'">
          <span>{{ props.row.source }}</span>
        </span>
        <!-- Column: user -->
        <span class="db__tc" v-else-if="props.column.field === 'user'">
          <span class="text-nowrap">{{ props.row.user }}</span>
          <div v-for="(history, index) in historyArray" :key="index">
            <b-button
              v-if="history === props.row.id"
              @click="historyUser(props.row)"
              style="margin-top: 5px"
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
            >
              История
            </b-button>
          </div>
        </span>
        <!-- Column: Managment -->
        <span v-else-if="props.column.field === 'Managment'">
          <div class="dropdown db__tc">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              type="button"
              id="dropdownMenuButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <feather-icon
                v-if="managerObject[props.row.id]"
                :icon="managerObject[props.row.id].icon"
                size="16"
                class="align-middle mr-25"
              />
            </b-button>
            <div
              class="dropdown-menu"
              style="min-width: 50px"
              aria-labelledby="dropdownMenuButton"
              v-if="user.role.id === 1"
            >
              <button
                v-for="(m, index) in manager"
                :key="index"
                class="dropdown-item"
                @click="changeIconManager(props.row.id, m)"
              >
                <feather-icon
                  :icon="m.icon"
                  size="20"
                  class="align-middle mr-25"
                />
              </button>
            </div>
          </div>
        </span>
        <!-- Column: Client -->
        <span v-else-if="props.column.field === 'Client'">
          <div class="dropdown db__tc">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              type="button"
              id="dropdownMenuButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <feather-icon
                v-if="clientObject[props.row.id]"
                :icon="clientObject[props.row.id].icon"
                size="16"
                class="align-middle mr-25"
              />
            </b-button>
            <div
              class="dropdown-menu"
              style="min-width: 50px"
              aria-labelledby="dropdownMenuButton"
              v-if="user.role.id === 1"
            >
              <button
                v-for="(c, index) in client"
                :key="index"
                class="dropdown-item"
                @click="changeIconClient(props.row.id, c)"
              >
                <feather-icon
                  :icon="c.icon"
                  size="20"
                  class="align-middle mr-25"
                />
              </button>
            </div>
          </div>
        </span>
        <!-- Column: tags -->
        <span v-else-if="props.column.field === 'tag'">
          <b-badge pill variant="success" class="badge-glow db__tc">{{
            props.row.tag
          }}</b-badge>
        </span>
        <!-- Column: commentManager -->
        <span
          class="comment-manager"
          style="word-break: break-all"
          v-else-if="props.column.field === 'commentManager'"
        >
          <span>{{ props.row.commentManager }}</span>
        </span>
        <!-- Column: commentClient -->
        <span
          style="word-break: break-all"
          v-else-if="props.column.field === 'commentClient'"
        >
          <span>{{ props.row.commentClient }}</span>
        </span>
        <!-- Column: Status -->
        <span v-else-if="props.column.field === 'status'">
          <b-badge :variant="statusVariant(props.row.status)">
            {{ props.row.status }}
          </b-badge>
        </span>

        <!-- Column: Действие -->
        <span v-else-if="props.column.field === 'Действие'">
          <span>
            <b-dropdown
              variant="link"
              toggle-class="text-decoration-none"
              no-caret
            >
              <template v-slot:button-content>
                <feather-icon
                  icon="MoreVerticalIcon"
                  size="16"
                  class="text-body align-middle mr-25"
                />
              </template>
              <b-dropdown-item v-b-modal.modal__seeProject>
                <eye-icon size="1x" class="mr-50"></eye-icon>
                <span>Посмотреть</span>
              </b-dropdown-item>
              <b-dropdown-item v-if="user.role.id === 1" @click="deleteModal">
                <feather-icon icon="TrashIcon" class="mr-50" />
                <span>Удалить</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>
        <!-- Column: Common -->
      </template>

      <!-- pagination -->
      <template slot="pagination-bottom" slot-scope="props">
        <div class="d-flex justify-content-between flex-wrap">
          <div class="d-flex align-items-center mb-0 mt-1">
            <span class="text-nowrap"> Showing 1 to </span>
            <b-form-select
              v-model="pageLength"
              :options="['3', '5', '10', '50']"
              class="mx-1"
              @input="
                (value) => props.perPageChanged({ currentPerPage: value })
              "
            />
            <span class="text-nowrap"> of {{ props.total }} entries </span>
          </div>
          <div>
            <b-pagination
              :value="1"
              :total-rows="props.total"
              :per-page="pageLength"
              first-number
              last-number
              align="right"
              prev-class="prev-item"
              next-class="next-item"
              class="mt-1 mb-0"
              @input="(value) => props.pageChanged({ currentPage: value })"
            >
              <template #prev-text>
                <feather-icon icon="ChevronLeftIcon" size="18" />
              </template>
              <template #next-text>
                <feather-icon icon="ChevronRightIcon" size="18" />
              </template>
            </b-pagination>
          </div>
        </div>
      </template>
    </vue-good-table>
    <!-- modal see project -->
    <b-modal
      id="modal__seeProject"
      centered
      title="Просмотр обращения"
      cancel-title="Отмена"
      size="lg"
      ref="modal__window"
      hide-footer
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
                <div v-if="user.role.id === 1" class="row__date-lables">
                  <label class="row__lables-label">Дата и время</label>
                  <flat-pickr
                    placeholder="Выберите дату и время"
                    v-model="data.date"
                    class="form-control row__date-pickr"
                    :config="{
                      enableTime: true,
                      dateFormat: 'd.m.Y H:i:s',
                      enableSeconds: true,
                    }"
                  />
                </div>
                <div v-if="user.role.id === 1" class="row__call-lables">
                  <label class="row__lables-label"
                    >Продолжительность звонка</label
                  >
                  <div class="modal__input">
                    <b-button
                      @click="quantity_minus(data.call)"
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
                      v-model="data.call"
                      readonly="readonly"
                    />
                    <b-button
                      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                      variant="primary"
                      type="button"
                      size="sm"
                      @click="quantity_plus(data.call)"
                    >
                      <plus-icon
                        size="1x"
                        class="plus-icon align-middle mr-25"
                      ></plus-icon>
                    </b-button>
                  </div>
                </div>
                <div v-if="user.role.id === 1" class="row__source-lables">
                  <label class="row__lables-label">Источник</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.source"
                    :options="options_source"
                  >
                  </b-form-select>
                </div>
                <div v-if="user.role.id === 1" class="row__user-lables">
                  <label class="row__lables-label">Пользователь</label>
                  <b-form-input
                    class="row__user-input"
                    type="text"
                    placeholder="+7 (999) 999-99-99"
                    v-model="data.user"
                    v-mask="'+7 (###) ###-##-##'"
                    maxlength="18"
                  />
                </div>
                <div
                  v-if="user.role.id === 1 || user.role.id === 2"
                  class="row__tag-lables"
                >
                  <label class="row__lables-label">Тэги</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.tag"
                    :options="options_tags"
                  >
                  </b-form-select>
                </div>
                <div
                  v-if="user.role.id === 1 || user.role.id === 2"
                  class="row__tag-lables"
                >
                  <label class="row__lables-label">Проверка менеджера</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.manager"
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
                    v-model="data.commentManager"
                    :value="data.commentManager"
                  />
                </div>
                <div v-if="user.role.id === 1" class="row__tag-lables">
                  <label class="row__lables-label">Проверка клиента</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.client"
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
                    v-model="data.commentClient"
                    :value="data.commentClient"
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

import Search from "./Search.vue";
import Filters from "./Filters.vue";
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import "swiper/css/swiper.css";
import { VueGoodTable } from "vue-good-table";
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
    Filters,
    Search,
    Swiper,
    SwiperSlide,
    VueGoodTable,
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
  directives: {
    Ripple,
    "b-tooltip": VBTooltip,
    "b-modal": VBModal,
  },
  data() {
    return {
      perfectScrollbarSettings: {
        maxScrollbarLength: 150,
      },
      manager: [
        { value: "целевой", icon: "CheckIcon" },
        { value: "не целевой", icon: "XCircleIcon" },
        { value: "не установленный", icon: "XSquareIcon" },
      ],
      managerObject: {},
      clientObject: {},
      client: [
        { value: "целевой", icon: "CheckIcon" },
        { value: "не целевой", icon: "XCircleIcon" },
        { value: "не проверенный", icon: "XSquareIcon" },
      ],
      pageLength: 10,
      dir: false,
      columns: [
        {
          label: "Дата и время",
          field: "Date",
          width: "100px",
          thClass: "columnCenter",
        },
        {
          label: "Продол. звонка",
          field: "call",
          width: "80px",
          thClass: "columnCenter",
        },
        {
          label: "Источник",
          field: "source",
          width: "230px",
          thClass: "columnCenter",
        },
        {
          label: "Пользователь",
          field: "user",
          thClass: "columnCenter",
          width: "180px",
        },
        {
          label: "Менеджер",
          field: "Managment",
          width: "130px",
          thClass: "columnCenter",
        },
        {
          label: "Клиент",
          field: "Client",
          width: "130px",
          thClass: "columnCenter",
          // tdClass: 'text-center', пользовательский класс для ячеек таблицы
        },
        {
          label: "Тэги обращения",
          field: "tag",
          width: "130px",
          thClass: "columnCenter",
        },
        {
          label: "Комментарий менеджера",
          field: "commentManager",
          thClass: "columnCenter",
        },
        {
          label: "Комментарий клиента",
          field: "commentClient",
          thClass: "columnCenter",
        },
        {
          label: "Действие",
          field: "Действие",
          width: "120px",
          thClass: "columnCenter",
        },
      ],
      selected: {},
      options_tags: [
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
      rows: [],
      user: "",
      searchTerm: "",
      sortedFilter: [],
      arr: [],
      rowSelection: [],
      arraySearch: [],
      managerArray: [],
      clientArray: [],
      modalArray: [],
      modalArrayNext: [],
      modalArrayPrev: [],
      modalCounter: 0,
      historyArray: [],
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
      checkboxUser: [],
      arrayChat: [],
      getDT: false,
      project: "",
    };
  },
  methods: {
    quantity_minus(call) {
      let temp = call.toString().replace(/[^0-9]/g, "");
      call = Number(temp);
      if (call >= 10) {
        call -= 10;
        this.modalArray[this.modalCounter].call = "";
        this.modalArray[this.modalCounter].call += call + " секунд";
      }
    },
    quantity_plus(call) {
      call = call.toString().replace(/[^0-9]/g, "");
      call = Number(call);
      call += 10;

      this.modalArray[this.modalCounter].call = "";
      this.modalArray[this.modalCounter].call += call + " секунд";
    },
    changeSlideNext() {
      this.modalCounter++;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    changeSlidePrev() {
      this.modalCounter--;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    handleHide(bvEvent) {
      if (!this.isCloseable) {
        bvEvent.preventDefault();
      } else {
        this.$refs.dropdown.hide();
      }
    },
    closeMe() {
      this.isCloseable = true;
      this.$refs.dropdown.hide();
    },
    stopPropagation() {
      console.log("stopPropagation");
    },
    pushChangeCheckBox(user) {
      this.checkboxUser = user;
    },
    pushSortedFilter(SortedFilter) {
      this.sortedFilter = SortedFilter;
    },
    pushArraySearch(search) {
      this.arraySearch = search;
    },
    pushSelected(select) {
      this.selected = select;
    },
    resetFilters() {
      this.selected.tag = null;
      this.selected.source = null;
      this.selected.date = null;
      this.selected.manager = null;
      this.selected.client = null;
      this.selected.deleted = null;
      this.selected.value_range = 0;
      this.phone = "";
      this.sortedFilter = [];
    },
    async getDataUser() {
      await axios.get("/sanctum/csrf-cookie").then((response) => {
        axios
          .get("api/user ")
          .then((response) => {
            this.user = response.data;
          })
          .catch((error) => {
            if (error.response.status === 401) {
              axios.get("/logout").then((resp) => {
                localStorage.removeItem(
                  "x_xsrf_token",
                  resp.config.headers["X-XSRF-TOKEN"]
                );
                this.$router.push("/");
                this.$store.commit("SET_ENTERED", false);
              });
            } else {
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
          });
      });
    },
    async getDataTable() {
      try {
        axios
          .get("/api/data")
          .then((response) => {
            this.rows = response.data;
            this.getDT = true;
            this.rows.forEach((row) => {
              const activeManager =
                this.manager.find((m) => m.value == row.manager) || null;
              this.$set(this.managerObject, row.id, activeManager);
              const activeClient =
                this.client.find((c) => c.value == row.client) || null;
              this.$set(this.clientObject, row.id, activeClient);
            });
            this.historyArray = [];
            this.rows.filter((row, index, k) => {
              k = 0;
              for (let i = 0; i < index; i++) {
                if (row.user === this.rows[i].user) {
                  k++;
                  if (k < 2) {
                    this.historyArray.push(this.rows[i].id);
                  }
                }
              }
            });
            this.historyArray = [...new Set(this.historyArray)];
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
    selectionChanged(params) {
      this.rowSelection = params.selectedRows;
      if (this.rowSelection.length) {
        this.check = true;
      } else {
        this.check = false;
      }
    },
    historyUser(row) {
      this.searchTerm = row.user;
      this.arraySearch = [];
      this.rows.filter((item) => {
        if (item.user === this.searchTerm) {
          this.arraySearch.push(item);
        }
      });
    },
    onCellClick(row) {
      if (row.column.label === "Действие") {
        this.ActionOnProject(row.event.path[0].innerText, row.row);
      }
    },
    async ActionOnProject(item, row) {
      if (item === "Посмотреть") {
        let temp = row;
        this.modalArray = [];
        let i = 0;
        this.rows.filter((item) => {
          if (temp.id === item.id) {
            i++;
          }
          if (i === 1) {
            this.modalArray.push(item);
          }
        });
        this.arrayChat = await this.modalArray[this.modalCounter].dialog;
      }
      if (item === "Удалить") {
        this.modalArray = row;
      }
    },
    async deleteModal() {
      try {
        this.$swal({
          title: "Вы согласны удалить обращение?",
          text: "Это действие необратимо!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Да, удалить!",
          cancelButtonText: "Отменить",
          customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-outline-danger ml-1",
          },
          buttonsStyling: false,
        }).then((result) => {
          if (this.rows.length) {
            this.rows.filter((index, i) => {
              if (index.id === this.modalArray.id) {
                axios
                  .delete("/api/data/" + this.modalArray.id)
                  .then(() => {
                    this.rows.splice(i, 1);
                    if (result.value) {
                      this.$swal({
                        icon: "success",
                        title: "Удалено!",
                        text: "Ваше обращение было удалено.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                    }
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
              }
            });
          } else if (result.dismiss === "cancel") {
            this.$swal({
              title: "Отмена",
              text: "Удаление обращения было отменено",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
          }
        });
      } catch (error) {}
    },
    hideModal() {
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        await axios
          .put("/api/data/" + this.modalArray[this.modalCounter].id, {
            id: this.modalArray[this.modalCounter].id,
            date: this.modalArray[this.modalCounter].date,
            call: this.modalArray[this.modalCounter].call,
            commentManager: this.modalArray[this.modalCounter].commentManager,
            source: this.modalArray[this.modalCounter].source,
            user: this.modalArray[this.modalCounter].user,
            tag: this.modalArray[this.modalCounter].tag,
            commentClient: this.modalArray[this.modalCounter].commentClient,
            manager: this.modalArray[this.modalCounter].manager,
            client: this.modalArray[this.modalCounter].client,
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

        await this.getDataTable();
      } catch (error) {}
    },
    async changeIconManager(id, manager) {
      this.rows.map((row) => {
        if (row.id === id) {
          row.manager = manager.value;
          axios
            .put("/api/data/" + id, {
              id: id,
              date: row.date,
              call: row.call,
              commentManager: row.commentManager,
              source: row.source,
              user: row.user,
              tag: row.tag,
              commentClient: row.commentClient,
              manager: manager.value,
              client: row.client,
            })
            .then(() => {
              this.$set(this.managerObject, id, manager);
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
        }
      });
    },
    changeIconClient(id, client) {
      this.rows.map((row) => {
        if (row.id === id) {
          row.client = client.value;
          axios
            .put("/api/data/" + id, {
              id: id,
              date: row.date,
              call: row.call,
              commentManager: row.commentManager,
              source: row.source,
              user: row.user,
              tag: row.tag,
              commentClient: row.commentClient,
              manager: row.manager,
              client: client.value,
            })
            .then(() => {
              this.$set(this.clientObject, id, client);
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
        }
      });
    },
    // nextAppeal() {
    //   try {
    //     let j = 0;
    //     this.modalPrevCounter = 0;
    //     for (let i in this.rows) {
    //       if (this.rows[i].id === this.modalArrayNext[0].id) {
    //         this.modalCounter = i;
    //         this.modalArrayNext = [];
    //         this.modalArrayNext.push(this.rows[j + 1]);
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
    //   for (let i in this.rows) {
    //     if (this.rows[j - this.modalPrevCounter].id === undefined) {
    //     }
    //     this.modalArrayNext = [];
    //     this.modalArrayNext.push(this.rows[j - this.modalPrevCounter]);
    //     this.modalArray = this.modalArrayNext[0];
    //   }
    // },
    inputSpinButton(call) {
      this.modalArray[this.modalCounter].call = call + " секунд";
    },
  },
  computed: {
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      }
      if (this.checkboxUser.length) {
        return this.checkboxUser;
      }
      if (this.sortedFilter.length) {
        const filteredRows = this.sortedFilter.filter((row) => {
          let flag = true;
          for (const [key, value] of Object.entries(this.selected)) {
            if (!!value && !row[key].includes(value)) {
              // если value фильтра истинно (то есть не равно ни null, ни 0) и если то же поле в строке (с тем же ключом что и в фильтре) не содержит значение фильтра...
              flag = false; // ... то флаг = false...
              break; // ... и цикл прерывается
            }
          }
          return flag; // возвращаем флаг, если флаг == false - строка не проходит фильтрацию
        });
        return filteredRows;
      } else {
        return this.rows;
      }
    },
  },
  created() {
    this.getDataTable();
    this.getDataUser();
  },
};
</script>
<style lang="scss">
@import "~@core/scss/base/pages/app-chat.scss";
@import "~@core/scss/base/pages/app-chat-list.scss";
</style>
// :dateFormatOptions="{
//                   year: 'numeric',
//                   month: 'numeric',
//                   day: 'numeric',
//                 }"
// @hide="handleHide($event)"
// @hidden="isCloseable = false"
