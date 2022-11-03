<template>
  <div v-if="getProject !== ''">
    <div class="text-center" v-if="!getDataTable || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      @arraySearch="pushArraySearch"
      :rows="rows"
      v-if="getDataTable && user"
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
      :options_source_id="options_source_id"
      v-if="getDataTable && user"
    />

    <!-- table -->
    <vue-good-table
      v-if="getDataTable"
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
        <!-- Column: datetime -->
        <span
          v-if="props.column.field === 'datetime'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.datetime }}</span>
        </span>
        <!-- Column: duration -->
        <span
          v-else-if="props.column.field === 'duration'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.duration }}</span>
        </span>
        <!-- Column: source_id  -->
        <span v-else-if="props.column.field === 'source_id '">
          <span>{{ props.row.source_id }}</span>
        </span>
        <!-- Column: phone  -->
        <span class="db__tc" v-else-if="props.column.field === 'phone '">
          <span class="text-nowrap">{{ props.row.phone }}</span>
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
        <!-- Column: manager_check -->
        <span v-else-if="props.column.field === 'manager_check'">
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
                v-for="(m, index) in manager_check"
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
        <!-- Column: client_check -->
        <span v-else-if="props.column.field === 'client_check'">
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
                v-for="(c, index) in client_check"
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
        <span v-else-if="props.column.field === 'tags'">
          <b-badge pill variant="success" class="badge-glow db__tc">{{
            props.row.tags
          }}</b-badge>
        </span>
        <!-- Column: manager_comment -->
        <span
          class="comment-manager"
          style="word-break: break-all"
          v-else-if="props.column.field === 'manager_comment'"
        >
          <span>{{ props.row.manager_comment }}</span>
        </span>
        <!-- Column: client_comment -->
        <span
          style="word-break: break-all"
          v-else-if="props.column.field === 'client_comment'"
        >
          <span>{{ props.row.client_comment }}</span>
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
              <b-dropdown-item>
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
                    <b-form-input
                      class="input__number form-control"
                      v-model="data.duration"
                      readonly="readonly"
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
                <div v-if="user.role.id === 1" class="row__source -lables">
                  <label class="row__lables-label">Источник</label>
                  <b-form-select
                    class="form__select"
                    v-model="data.source_id"
                    :options="options_source_id"
                  >
                  </b-form-select>
                </div>
                <div v-if="user.role.id === 1" class="row__user-lables">
                  <label class="row__lables-label">Пользователь</label>
                  <b-form-input
                    class="row__user-input"
                    type="text"
                    placeholder="+7 (999) 999-99-99"
                    v-model="data.phone"
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
                    v-model="data.tags"
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
  <div v-else>
    <h3>Список доступных проектов</h3>
    <multiselect
      v-model="project"
      :options="option_project"
      selectLabel="Нажмите enter для выбора"
      deselectLabel="Нажмите enter для удаления"
      selectedLabel="Выбрано"
      class="multiselect-input"
      label="name"
      track-by="name"
      placeholder="Выберите проект"
      @select="selectProject"
    >
    </multiselect>
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
      manager_check: [
        { value: "целевой", icon: "CheckIcon" },
        { value: "не целевой", icon: "XCircleIcon" },
        { value: "не установленный", icon: "XSquareIcon" },
      ],
      managerObject: {},
      clientObject: {},
      client_check: [
        { value: "целевой", icon: "CheckIcon" },
        { value: "не целевой", icon: "XCircleIcon" },
        { value: "не проверенный", icon: "XSquareIcon" },
      ],
      pageLength: 10,
      dir: false,
      columns: [
        {
          label: "Дата и время",
          field: "datetime",
          width: "100px",
          thClass: "columnCenter",
        },
        {
          label: "Продол. звонка",
          field: "duration",
          width: "120px",
          thClass: "columnCenter",
        },
        {
          label: "Источник",
          field: "source_id ",
          width: "200px",
          thClass: "columnCenter",
        },
        {
          label: "Пользователь",
          field: "phone",
          thClass: "columnCenter",
          width: "180px",
        },
        {
          label: "Менеджер",
          field: "manager_check",
          width: "130px",
          thClass: "columnCenter",
        },
        {
          label: "Клиент",
          field: "client_check",
          width: "130px",
          thClass: "columnCenter",
          // tdClass: 'text-center', пользовательский класс для ячеек таблицы
        },
        {
          label: "Тэги обращения",
          field: "tags",
          width: "130px",
          thClass: "columnCenter",
        },
        {
          label: "Комментарий менеджера",
          field: "manager_comment",
          thClass: "columnCenter",
        },
        {
          label: "Комментарий клиента",
          field: "client_comment",
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
      options_source_id: [
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
      phone: "",
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
      project: [],
      option_project: [],
      user: "",
    };
  },
  methods: {
    async selectProject(project) {
      await this.$store.commit("SET_PROJECT", project);
      await this.$store.dispatch("getDataTable");
      await this.$store.dispatch("getSourceTable");
      await this.$store.dispatch("getTagsTable");
    },
    showProjectModal() {
      this.$refs["project__modal"].show();
    },
    async saveProjectModal() {
      await this.$store.commit("SET_PROJECT", this.project);
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
    async getDataUser() {
      await axios.get("/sanctum/csrf-cookie").then((response) => {
        axios
          .get("api/user ")
          .then((response) => {
            this.user = response.data;
            this.option_project = this.user.projects;
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
    selectionChanged(params) {
      this.rowSelection = params.selectedRows;
      if (this.rowSelection.length) {
        this.check = true;
      } else {
        this.check = false;
      }
    },
    historyUser(row) {
      this.searchTerm = row.phone;
      this.arraySearch = [];
      this.getDataTable.filter((item) => {
        if (item.phone === this.searchTerm) {
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
        this.getDataTable.filter((item) => {
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
          if (this.getDataTable.length) {
            this.getDataTable.filter((index, i) => {
              if (index.id === this.modalArray.id) {
                axios
                  .delete("/api/data/" + this.modalArray.id)
                  .then(() => {
                    this.getDataTable.splice(i, 1);
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
    hideProjectModal() {
      this.$refs["project__modal"].hide();
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

        await this.getDataTable();
      } catch (error) {}
    },
    async changeIconManager(id, manager) {
      this.getDataTable.map((row) => {
        if (row.id === id) {
          row.manager_check = manager_check.value;
          axios
            .put("/api/data/" + id, {
              id: id,
              datetime: row.datetime,
              duration: row.duration,
              manager_comment: row.manager_comment,
              source_id: row.source_id,
              phone: row.phone,
              tags: row.tags,
              client_comment: row.client_comment,
              manager_check: manager_check.value,
              client_check: row.client_check,
            })
            .then(() => {
              let manager_object = {};
              this.$set(manager_object, id, manager_check);
              this.$store.commit("SET_MANAGER_OBJECT", manager_object);
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
      this.getDataTable.map((row) => {
        if (row.id === id) {
          row.client_check = client_check.value;
          axios
            .put("/api/data/" + id, {
              id: id,
              datetime: row.datetime,
              duration: row.duration,
              manager_comment: row.manager_comment,
              source_id: row.source_id,
              phone: row.phone,
              tags: row.tags,
              client_comment: row.client_comment,
              manager_check: row.manager_check,
              client_check: client_check.value,
            })
            .then(() => {
              let client_object = {};
              this.$set(client_object, id, client_check);
              this.$store.commit("SET_CLIENT_OBJECT", client_object);
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
  computed: {
    getDataTable() {
      return this.$store.getters.getClaims;
    },
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
        return this.getDataTable;
      }
    },
    getProject() {
      return this.$store.getters.project;
    },
  },
  created() {
    this.getDataUser();
  },
};
</script>
<style lang="scss">
@import "~@core/scss/base/pages/app-chat.scss";
@import "~@core/scss/base/pages/app-chat-list.scss";
</style>
// :datetimeFormatOptions="{
//                   year: 'numeric',
//                   month: 'numeric',
//                   day: 'numeric',
//                 }"
// @hide="handleHide($event)"
// @hidden="isCloseable = false"
