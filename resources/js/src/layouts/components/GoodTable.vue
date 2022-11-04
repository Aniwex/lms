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
      :rows="getDataTable"
      v-if="getDataTable && user"
      :role_id="user.role.id"
    />
    <!-- filers -->
    <filters
      @sortedFilter="pushSortedFilter"
      @arrayCheckboxUser="pushChangeCheckBox"
      @selected="pushSelected"
      :rowSelection="rowSelection"
      :rows="getDataTable"
      :role_id="user.role.id"
      :project="getProject"
      :options_source="options_source"
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
          <span class="text-nowrap">{{ props.row.duration.formatted }}</span>
        </span>
        <!-- Column: source  -->
        <span v-else-if="props.column.field === 'source '">
          <span>{{ props.row.source.name }}</span>
        </span>
        <!-- Column: phone  -->
        <span class="db__tc" v-else-if="props.column.field === 'phone'">
          <span class="text-nowrap">{{ props.row.phone.formatted }}</span>
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
        <!-- Column: manager -->
        <span v-else-if="props.column.field === 'manager'">
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
                v-if="getManagerObject[props.row.id]"
                :icon="getManagerObject[props.row.id].icon"
                size="16"
                class="align-middle mr-25"
              />
              <div v-else>
                <feather-icon
                  icon="SlashIcon"
                  size="16"
                  class="align-middle mr-25"
                />
              </div>
            </b-button>
            <div
              class="dropdown-menu"
              style="min-width: 50px"
              aria-labelledby="dropdownMenuButton"
              v-if="user.role.id === 1"
            >
              <button
                v-for="(m, index) in getManager"
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
        <!-- Column: client -->
        <span v-else-if="props.column.field === 'client'">
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
                v-if="getClientObject[props.row.id]"
                :icon="getClientObject[props.row.id].icon"
                size="16"
                class="align-middle mr-25"
              />
              <div v-else>
                <feather-icon
                  icon="SlashIcon"
                  size="16"
                  class="align-middle mr-25"
                />
              </div>
            </b-button>
            <div
              class="dropdown-menu"
              style="min-width: 50px"
              aria-labelledby="dropdownMenuButton"
              v-if="user.role.id === 1"
            >
              <button
                v-for="(c, index) in getClient"
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
          <b-badge
            v-for="(tag, index) in props.row.tags"
            :key="index"
            pill
            variant="success"
            style="margin-top: 10px"
            class="badge-glow db__tc"
            >{{ tag.name }}</b-badge
          >
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
    <ModalSeeProject
      id="modal__seeProject"
      :user="user"
      :modalArray="modalArray"
      :options_source="getSourceTable"
      :options_tags="getTagsTable"
      :options_manager="options_manager"
      :options_client="options_client"
    ></ModalSeeProject>
  </div>
  <div v-else>
    <b-button
      style="display: none"
      v-b-modal.Project__modal
      class="form__button-margin"
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="primary"
    >
      Сохранить
    </b-button>
    <b-modal
      id="Project__modal"
      cancel-variant="secondary"
      centered
      title="Выберите проект"
      ref="b_modal"
      modal-close="false"
      no-close-on-esc
      no-close-on-backdrop
      hide-header-close
      hide-footer
    >
      <h3 class="list__projects">Список доступных проектов</h3>
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
        style="margin: 20px"
      >
      </multiselect>
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
import ModalSeeProject from "./ModalSeeProject.vue";
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
  SlashIcon,
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
    ModalSeeProject,
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
      managerObject: {},
      clientObject: {},
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
          field: "source ",
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
          field: "manager",
          width: "130px",
          thClass: "columnCenter",
        },
        {
          label: "Клиент",
          field: "client",
          width: "130px",
          thClass: "columnCenter",
          // tdClass: 'text-center', пользовательский класс для ячеек таблицы
        },
        {
          label: "Тэги",
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
      options_tags: [],
      options_source: [],
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
      this.selected.source = null;
      this.selected.datetime = null;
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
                  .delete(
                    "api/projects/" +
                      this.getProject.id +
                      "/claims/" +
                      this.modalArray.id
                  )
                  .then(() => {
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
                this.getDataTable.splice(i, 1);
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

    async changeIconManager(id, manager) {
      this.getDataTable.map((row) => {
        if (row.id === id) {
          row.manager.check.text = manager.value;
          let tempTagsId = [];
          row.tags.filter((item) => {
            tempTagsId.push(item.id);
          });
          console.log("client_check = " + row.client.check.value);
          axios
            .put(" api/projects/" + this.getProject.id + "/claims/" + id, {
              datetime: row.datetime,
              duration: row.duration.original,
              manager_comment: row.manager_comment,
              client_comment: row.client_comment,
              source_id: row.source.id,
              phone: row.phone.original,
              tags: tempTagsId,
              manager_check: row.manager.check.value,
              client_check: row.client.check.value,
            })
            .then(() => {
              let manager_object = {};
              this.$set(manager_object, id, manager);
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
          row.client = client.value;
          axios
            .put("/api/data/" + id, {
              id: id,
              datetime: row.datetime,
              duration: row.duration,
              manager_comment: row.manager_comment,
              source: row.source,
              phone: row.phone,
              tags: row.tags,
              client_comment: row.client_comment,
              manager: row.manager,
              client: client.value,
            })
            .then(() => {
              let client_object = {};
              this.$set(client_object, id, client);
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
    inputSpinButton(duration) {
      this.modalArray[this.modalCounter].duration = duration + " секунд";
    },
  },
  computed: {
    getDataTable() {
      let managerObject = {};
      let clientObject = {};
      this.$store.getters.getClaims.forEach((row) => {
        const activeManager =
          this.$store.getters.manager.find(
            (m) => m.value == row.manager.check.text
          ) || null;
        this.$set(managerObject, row.id, activeManager);
        this.$store.commit("SET_MANAGER_OBJECT", managerObject);
        const activeClient =
          this.$store.getters.client.find(
            (c) => c.value == row.client.check.text
          ) || null;
        this.$set(clientObject, row.id, activeClient);
        this.$store.commit("SET_CLIENT_OBJECT", clientObject);
      });
      return this.$store.getters.getClaims;
    },
    getTagsTable() {
      return this.$store.getters.getTags;
    },
    getSourceTable() {
      return this.$store.getters.getSources;
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
    getManager() {
      return this.$store.getters.manager;
    },
    getClient() {
      return this.$store.getters.client;
    },
    getManagerObject() {
      return this.$store.getters.getManagerObject;
    },
    getClientObject() {
      return this.$store.getters.getClientObject;
    },
  },
  created() {
    this.getDataUser();
  },
  mounted() {
    this.$bvModal.show("Project__modal");
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
