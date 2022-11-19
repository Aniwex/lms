<template>
  <div>
    <div class="text-center" v-if="!getDataTags || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      :rows="getDataTags"
      :searchTerm="searchTerm"
      v-if="getDataTags && user"
      :role_id="user.role.id"
      @arraySearch="pushArraySearch"
    />
    <div
      v-if="rowSelection.length && user.role.id === 1"
      class="d-flex justify-content-end"
    >
      <b-dropdown class="drop__down-delete" variant="primary" right no-caret>
        <template #button-content>
          <trash-2-icon size="1x" class="custom-class"></trash-2-icon
        ></template>
        <b-dropdown-form
          ><div class="form__group-delete">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              @click="deleteSelected()"
            >
              Удалить выбранное ({{ rowSelection.length }})
            </b-button>
          </div>
        </b-dropdown-form>
      </b-dropdown>
    </div>
    <!-- table -->
    <vue-good-table
      :columns="columns"
      :rows="sorted"
      v-if="getDataTags && user"
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
      @on-cell-click="onCellClick"
      @on-selected-rows-change="selectionChanged"
    >
      <template slot="table-row" slot-scope="props">
        <!-- Column: id -->
        <span v-if="props.column.field === 'ID'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.id }}</span>
        </span>
        <!-- Column: Name -->
        <span v-if="props.column.field === 'name'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.name }}</span>
        </span>
        <!-- Column: objective -->
        <span
          v-else-if="props.column.field === 'objective'"
          class="text-nowrap db__tc"
        >
          <div v-if="props.row.objective">
            <check-icon
              style="color: green"
              size="1.5x"
              class="custom-class"
            ></check-icon>
          </div>
          <div v-else>
            <x-circle-icon
              style="color: red"
              size="1.5x"
              class="custom-class"
            ></x-circle-icon>
          </div>
        </span>
        <!-- Column: client_plus_words -->
        <span
          v-else-if="props.column.field === 'client_plus_words'"
          class="text-nowrap db__tc"
        >
          <span
            v-for="(words, index) in props.row.client_plus_words"
            :key="index"
            class="text-nowrap"
            >{{ words }}</span
          >
        </span>
        <!-- Column: client_minus_words -->
        <span
          v-else-if="props.column.field === 'client_minus_words'"
          class="text-nowrap db__tc"
        >
          <span
            v-for="(words, index) in props.row.client_minus_words"
            :key="index"
            class="text-nowrap"
            >{{ words }}</span
          >
        </span>
        <!-- Column: operator_plus_words -->
        <span
          v-else-if="props.column.field === 'operator_plus_words'"
          class="text-nowrap db__tc"
        >
          <span
            v-for="(words, index) in props.row.operator_plus_words"
            :key="index"
            class="text-nowrap"
            >{{ words }}</span
          >
        </span>
        <!-- Column: operator_minus_words -->
        <span
          v-else-if="props.column.field === 'operator_minus_words'"
          class="text-nowrap db__tc"
        >
          <span
            v-for="(words, index) in props.row.operator_minus_words"
            :key="index"
            class="text-nowrap"
            >{{ words }}</span
          >
        </span>
        <span v-else-if="props.column.field === 'action'">
          <span class="db__tc">
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
              <b-dropdown-item v-b-modal.modal__seeIntegration>
                <span>Посмотреть</span>
              </b-dropdown-item>
              <b-dropdown-item v-if="user.role.id === 1" @click="deleteModal">
                <span>Удалить</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>
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
      id="modal__seeIntegration"
      centered
      title="Просмотр обращения"
      cancel-title="Отмена"
      size="lg"
      ref="modal__window"
      hide-footer
      @hidden="hideModal"
    >
      <div class="see-project__form">
        <h3 class="see-project__read">Данные для редактирования</h3>
        <div class="container__see-project">
          <div class="row__lables">
            <div class="row__date-lables">
              <label class="row__lables-label">Название</label>
              <b-form-input
                class="row__user-input"
                v-model="modalObject.name"
                objective="text"
                placeholder="Название"
              />
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Целевой(да/нет)</label>
              <b-form-checkbox
                v-model="modalObject.objective"
                class="form__checkbox"
              >
                Целевой (да/нет)
              </b-form-checkbox>
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Плюс слова клиента</label>
              <div>
                <b-form-textarea
                  style="text-align: left; width: 300px"
                  :placeholder="
                    errors['client_plus_words.' + 0]
                      ? errors['client_plus_words.' + 0][0]
                      : 'Плюс слова клиента'
                  "
                  class="row__user-input"
                  rows="5"
                  no-resize
                  v-model="modalObject.client_plus_words"
                  :state="modalObject.client_plus_words !== ''"
                />
                <span v-if="modalObject.client_plus_words === ''"
                  ><span
                    style="color: red"
                    class="db__tc"
                    v-if="errors['client_plus_words.' + 0]"
                    >{{ errors["client_plus_words." + 0][0] }}</span
                  ></span
                >
              </div>
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Минус слова клиента</label>
              <div>
                <b-form-textarea
                  class="row__user-input"
                  style="text-align: left; width: 300px"
                  :placeholder="
                    errors['client_minus_words.' + 0]
                      ? errors['client_minus_words.' + 0][0]
                      : 'Плюс слова клиента'
                  "
                  rows="5"
                  no-resize
                  v-model="modalObject.client_minus_words"
                  :state="modalObject.client_minus_words !== ''"
                />
                <span v-if="modalObject.client_minus_words === ''"
                  ><span
                    style="color: red"
                    class="db__tc"
                    v-if="errors['client_minus_words.' + 0]"
                    >{{ errors["client_minus_words." + 0][0] }}</span
                  ></span
                >
              </div>
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Плюс слова оператора</label>
              <div>
                <b-form-textarea
                  class="row__user-input"
                  style="text-align: left; width: 300px"
                  :placeholder="
                    errors['operator_plus_words.' + 0]
                      ? errors['operator_plus_words.' + 0][0]
                      : 'Плюс слова клиента'
                  "
                  rows="5"
                  no-resize
                  v-model="modalObject.operator_plus_words"
                  :state="modalObject.operator_plus_words !== ''"
                />
                <span v-if="modalObject.operator_plus_words === ''"
                  ><span
                    style="color: red"
                    class="db__tc"
                    v-if="errors['operator_plus_words.' + 0]"
                    >{{ errors["operator_plus_words." + 0][0] }}</span
                  ></span
                >
              </div>
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Минус слова оператора</label>
              <div>
                <b-form-textarea
                  class="row__user-input"
                  style="text-align: left; width: 300px"
                  :placeholder="
                    errors['operator_minus_words.' + 0]
                      ? errors['operator_minus_words.' + 0][0]
                      : 'Плюс слова клиента'
                  "
                  rows="5"
                  no-resize
                  v-model="modalObject.operator_minus_words"
                  :state="modalObject.operator_minus_words !== ''"
                />
                <span v-if="modalObject.operator_minus_words === ''"
                  ><span
                    style="color: red"
                    class="db__tc"
                    v-if="errors['operator_minus_words.' + 0]"
                    >{{ errors["operator_minus_words." + 0][0] }}</span
                  ></span
                >
              </div>
            </div>
            <div class="modal__form-buttons">
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
            </div>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { VueGoodTable } from "vue-good-table";
import axios from "axios";
import "vue-good-table/dist/vue-good-table.css";
import { Trash2Icon, CheckIcon, XCircleIcon } from "vue-feather-icons";
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
  BModal,
  VBModal,
  BButton,
  BFormTextarea,
  BSpinner,
  BFormCheckbox,
} from "bootstrap-vue";
import Search from "../layouts/components/Search.vue";
import Filters from "../layouts/components/Filters.vue";
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import { MoreVerticalIcon } from "vue-feather-icons";
import Ripple from "vue-ripple-directive";
import "swiper/css/swiper.css";
export default {
  components: {
    BFormCheckbox,
    XCircleIcon,
    CheckIcon,
    Trash2Icon,
    MoreVerticalIcon,
    Swiper,
    SwiperSlide,
    BModal,
    VBModal,
    Search,
    Filters,
    BSpinner,
    VueGoodTable,
    BFormTextarea,
    BBadge,
    BPagination,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BDropdown,
    BDropdownItem,
    BDropdownGroup,
    BDropdownForm,
    BDropdownDivider,
    BButton,
  },
  directives: {
    Ripple,
    "b-modal": VBModal,
  },
  data() {
    return {
      rowsTags: [],
      rowSelection: [],
      modalObject: [],
      errors: {},
      modalCounter: 0,
      searchTerm: "",
      user: "",
      pageLength: 5,
      columns: [
        {
          label: "ID",
          field: "ID",
          thClass: "columnCenter",
        },
        {
          label: "Название",
          field: "name",
          thClass: "columnCenter",
        },
        {
          label: "Целевой(да/нет)",
          field: "objective",
          thClass: "columnCenter",
        },
        {
          label: "Плюс слова клиента",
          field: "client_plus_words",
          thClass: "columnCenter",
        },
        {
          label: "Минус слова клиента",
          field: "client_minus_words",
          thClass: "columnCenter",
        },
        {
          label: "Плюс слова оператора",
          field: "operator_plus_words",
          thClass: "columnCenter",
        },
        {
          label: "Минус слова оператора",
          field: "operator_minus_words",
          thClass: "columnCenter",
        },
      ],
      project: [],
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
      arraySearch: "",
    };
  },
  methods: {
    async getDataUser() {
      if (!this.getProject) {
        this.$router.push("/Home");
      } else {
        await axios.get("/sanctum/csrf-cookie").then((response) => {
          axios
            .get("api/user ")
            .then((response) => {
              this.user = response.data;
              if (this.user.role.id === 1) {
                const obj = {
                  label: "Действие",
                  field: "action",
                  thClass: "columnCenter",
                  width: "200px",
                };
                this.columns.push(obj);
              }
            })
            .catch((error) => {
              const vNodesMsg = [
                `${Object.values(error.response.data.errors)}`,
              ];
              this.$bvToast.toast([vNodesMsg], {
                title: `Ошибка`,
                variant: "danger",
                solid: true,
                appendToast: true,
                toaster: "b-toaster-top-center",
                autoHideDelay: 3000,
              });
            });
        });
      }
    },
    onCellClick(row) {
      if (row.column.label === "Действие") {
        this.ActionOnProject(row.event.path[0].innerText, row.row);
      }
    },
    async ActionOnProject(item, row) {
      if (item === "Посмотреть") {
        this.modalObject = row;
      }
      if (item === "Удалить") {
        this.modalObject = row;
      }
    },
    hideModal() {
      this.$store.dispatch("getTagsTable");
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      let temp_client_plus_words = [];
      let temp_client_minus_words = [];
      let temp_operator_plus_words = [];
      let temp_operator_minus_words = [];
      temp_client_plus_words.push(
        this.modalObject.client_plus_words.split(/(?=\/)|\s/)
      );
      temp_client_minus_words.push(
        this.modalObject.client_minus_words.split(/(?=\/)|\s/)
      );
      temp_operator_plus_words.push(
        this.modalObject.operator_plus_words.split(/(?=\/)|\s/)
      );
      temp_operator_minus_words.push(
        this.modalObject.operator_minus_words.split(/(?=\/)|\s/)
      );
      try {
        await axios
          .put(
            "api/projects/" +
              this.getProject.id +
              "/tags/" +
              this.modalObject.id,
            {
              name: this.modalObject.name,
              objective: this.modalObject.objective,
              client_plus_words: temp_client_plus_words[0],
              client_minus_words: temp_client_minus_words[0],
              operator_plus_words: temp_operator_plus_words[0],
              operator_minus_words: temp_operator_minus_words[0],
            }
          )
          .then(() => {
            this.$refs["modal__window"].hide();
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
      } catch (error) {}
    },
    async deleteModal() {
      try {
        this.$swal({
          title: "Вы согласны удалить тэг?",
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
          if (result.dismiss === "cancel") {
            this.$swal({
              title: "Отмена",
              text: "Удаление тэга было отменено",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
          } else {
            if (this.getDataTags.length) {
              this.getDataTags.filter((index, i) => {
                if (index.id === this.modalObject.id) {
                  axios
                    .delete(
                      "api/projects/" +
                        this.getProject.id +
                        "/tags/" +
                        this.modalObject.id
                    )
                    .then(() => {
                      this.$store.dispatch("getTagsTable");
                      this.$swal({
                        icon: "success",
                        title: "Удалено!",
                        text: "Тэг был удалён.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                    })
                    .catch((error) => {
                      const vNodesMsg = [
                        `${Object.values(error.response.data.errors)}`,
                      ];
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
            }
          }
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
    deleteSelected() {
      try {
        if (this.getDataTags.length) {
          this.rowSelection.filter((item) => {
            this.getDataTags.map((index, i) => {
              if (item.id === index.id) {
                axios
                  .delete(
                    "api/projects/" + this.getProject.id + "/tags/" + item.id
                  )
                  .then(() => {
                    this.$store.dispatch("getTagsTable");
                  })
                  .catch((error) => {
                    const vNodesMsg = [
                      `${Object.values(error.response.data.errors)}`,
                    ];
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
          });
        }
      } catch (error) {}
    },
    pushArraySearch(search) {
      this.arraySearch = search;
    },
  },
  computed: {
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      } else {
        return this.getDataTags;
      }
    },
    getDataTags() {
      return this.$store.getters.getTags;
    },
    getProject() {
      return this.$store.getters.project;
    },
  },
  created() {
    this.getDataUser();
    this.getProject;
  },
};
</script>

<style>
</style>
