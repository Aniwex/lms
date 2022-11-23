<template>
  <div>
    <div class="text-center" v-if="!getDataSources || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      :rows="getDataSources"
      :searchTerm="searchTerm"
      v-if="getDataSources && user"
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
      v-if="getDataSources && user"
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
        <!-- Column: Integration -->
        <span
          v-if="props.column.field === 'integration'"
          class="text-nowrap db__tc"
        >
          <span v-if="props.row.integration" class="text-nowrap">{{
            props.row.integration.title
          }}</span>
        </span>
        <!-- Column: Name -->
        <span
          v-else-if="props.column.field === 'name'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.name }}</span>
        </span>
        <!-- Column: code -->
        <span
          v-else-if="props.column.field === 'code'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.code }}</span>
        </span>

        <!-- Column: Source_data -->
        <span v-else-if="props.column.field === 'source_data'" class="db__tc">
          <div v-for="(data, index) in props.row.data" :key="index" class="">
            <span>{{ index }}: {{ data }}</span>
          </div>
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
      title="Просмотр источника"
      cancel-title="Отмена"
      size="lg"
      ref="modal__window"
      hide-footer
      @hidden="cancelModal"
    >
      <div class="see-project__form">
        <h3 class="see-project__read">Данные для редактирования</h3>
        <div class="container__see-project">
          <div class="row__lables">
            <div class="row__date-lables" v-if="getIntegrationTable">
              <label class="row__lables-label">Интеграция</label>
              <div class="multiselect-input multiselect-margin">
                <multiselect
                  v-model="modalObject.integration"
                  :options="getIntegrationTable"
                  label="title"
                  track-by="title"
                  placeholder="Выберите интеграцию"
                  selectLabel="Нажмите enter для выбора"
                  deselectLabel="Нажмите enter для удаления"
                  @input="inputIntegration"
                >
                </multiselect>
                <span v-if="!modalObject.integration">
                  <span class="db__tc" style="color: red">
                    Выберите интеграцию
                  </span>
                </span>
              </div>
            </div>
            <div class="row__date-lables">
              <label class="row__lables-label">Название</label>
              <div class="container__row">
                <b-form-input
                  class="row__user-input"
                  v-model="modalObject.name"
                  type="text"
                  placeholder="Название"
                />
              </div>
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Код</label>
              <div class="container__row">
                <b-form-input
                  class="row__user-input"
                  v-model="modalObject.code"
                  type="text"
                  placeholder="Код"
                />
                <errorValidation
                  :errors="errors"
                  :error="errors.code"
                ></errorValidation>
              </div>
            </div>
            <div class="data__source">
              <label class="db__tc data__source-label"
                >Данные по источнику</label
              >
              <div v-for="(fields, index) in getTempFields" :key="index">
                <div v-if="fields.type === 'text'">
                  <b-form-group
                    label-cols="4"
                    label-cols-lg="2"
                    :label="fields.value"
                    label-for="input-lg"
                    style="width: 400px"
                  >
                    <b-form-input
                      class="row__user-input"
                      style="margin-left: 10px; margin-top: 0"
                      v-model="value__input[fields.key]"
                      type="text"
                      :placeholder="fields.value"
                    />
                  </b-form-group>
                  <span class="db__tc" v-if="fields.description">{{
                    fields.description
                  }}</span>
                </div>
                <div v-else-if="fields.type === 'select'">
                  <b-form-group
                    label-cols="4"
                    label-cols-lg="2"
                    :label="fields.value"
                    label-for="input-lg"
                    style="width: 400px"
                  >
                    <multiselect
                      v-model="phone_zadarma"
                      onclick="this.querySelector('input').focus();"
                      :options="fields.options"
                      selectedLabel="Выбрано"
                      style="margin-top: 10px; margin-left: 10px"
                      label="label"
                      track-by="value"
                      placeholder="Выберите источник"
                      :showLabels="false"
                    >
                    </multiselect>
                  </b-form-group>
                </div>
                <div v-else-if="fields.type === 'hint'">
                  <p class="fields__hint db__tc">
                    {{ fields.message }}
                  </p>
                </div>
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
import { Trash2Icon } from "vue-feather-icons";
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
} from "bootstrap-vue";
import Search from "../layouts/components/Search.vue";
import Filters from "../layouts/components/Filters.vue";
import errorValidation from "../views/error/errorValidation";
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import { MoreVerticalIcon } from "vue-feather-icons";
import Ripple from "vue-ripple-directive";
import "swiper/css/swiper.css";
export default {
  components: {
    errorValidation,
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
      rowsSource: [],
      user: "",
      errors: {},
      phone_zadarma: "",
      rowSelection: [],
      modalObject: [],
      modalCounter: 0,
      searchTerm: "",
      value__input: "",
      pageLength: 5,
      columns: [
        {
          label: "ID",
          field: "ID",
          thClass: "columnCenter",
        },
        {
          label: "Интеграция",
          field: "integration",
          thClass: "columnCenter",
        },
        {
          label: "Название",
          field: "name",
          thClass: "columnCenter",
        },
        {
          label: "Код",
          field: "code",
          thClass: "columnCenter",
        },
        {
          label: "Данные по источнику",
          field: "source_data",
          thClass: "columnCenter",
          width: "700px",
        },
      ],
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
      getSources: false,
      arraySearch: "",
      project: [],
      tempCancel: {},
    };
  },
  methods: {
    async inputIntegration() {
      if (this.modalObject.integration) {
        await axios
          .put(
            " api/projects/" +
              this.project.id +
              "/sources/" +
              this.modalObject.id,
            {
              integration_id: this.modalObject.integration.id,
            }
          )
          .then(() => {
            this.$store.dispatch("getTempFields");
          });
      }
    },
    pushArraySearch(search) {
      this.arraySearch = search;
    },
    async getDataUser() {
      if (!this.$store.getters.project) {
        this.$router.push("/Home");
      } else {
        await this.$store.dispatch("getIntegrationTable");
        await axios.get("/sanctum/csrf-cookie").then((response) => {
          axios.get("api/user ").then((response) => {
            this.user = response.data;
            this.getProject;
            if (this.user.role.id === 1) {
              const obj = {
                label: "Действие",
                field: "action",
                thClass: "columnCenter",
                width: "200px",
              };
              this.columns.push(obj);
            }
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
        for (let key in row) {
          this.tempCancel[key] = row[key];
        }
        this.value__input = "";
        this.errors = {};
        this.$store.commit("SET_OBJECTIDSOURCES", this.modalObject.id);
        this.$store.dispatch("getTempFields");
        if (this.modalObject.data) {
          this.value__input = this.modalObject.data;
          this.phone_zadarma = this.modalObject.data.phone;
          if (this.getTempFields.length === 4) {
            this.getTempFields[3].options.filter((item) => {
              if (item.value == this.phone_zadarma) {
                this.phone_zadarma = item;
              }
            });
          }
        }
      }
      if (item === "Удалить") {
        this.modalObject = row;
      }
    },
    async hideModal() {
      await axios
        .put(
          " api/projects/" + this.project.id + "/sources/" + this.tempCancel.id,
          {
            integration_id: this.tempCancel.integration.id,
          }
        )
        .then(() => {
          this.$store.dispatch("getSourceTable");
          this.$refs["modal__window"].hide();
        });
    },
    cancelModal() {
      this.$store.dispatch("getSourceTable");
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        await axios
          .put(
            " api/projects/" +
              this.project.id +
              "/sources/" +
              this.modalObject.id,
            {
              integration_id: this.modalObject.integration.id,
              name: this.modalObject.name,
              code: this.modalObject.code,
              data:
                this.getTempFields.length !== 4
                  ? {
                      [this.getTempFields[0].key]:
                        this.value__input[this.getTempFields[0].key],
                    }
                  : {
                      [this.getTempFields[0].key]:
                        this.value__input[this.getTempFields[0].key],
                      [this.getTempFields[1].key]:
                        this.value__input[this.getTempFields[1].key],
                      phone: this.phone_zadarma ? this.phone_zadarma.value : [],
                    },
            }
          )
          .then(() => {
            this.$store.dispatch("getSourceTable");
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
    async deleteModal() {
      try {
        this.$swal({
          title: "Вы согласны удалить источник?",
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
              text: "Удаление источника было отменено",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
          } else {
            if (this.getDataSources.length) {
              this.getDataSources.filter((index, i) => {
                if (index.id === this.modalObject.id) {
                  axios
                    .delete(
                      "api/projects/" +
                        this.project.id +
                        "/sources/" +
                        this.modalObject.id
                    )
                    .then(() => {
                      this.$store.dispatch("getSourceTable");
                      this.$swal({
                        icon: "success",
                        title: "Удалено!",
                        text: "Источник был удалён.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
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
        if (this.getDataSources.length) {
          this.rowSelection.filter((item) => {
            this.getDataSources.map((index, i) => {
              if (item.id === index.id) {
                axios
                  .delete(
                    "api/projects/" + this.project.id + "/sources/" + +item.id
                  )
                  .then(() => {
                    this.$store.dispatch("getSourceTable");
                  })
                  .catch((error) => {
                    const vNodesMsg = [`${Object.values(error.response.data)}`];
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
  },
  computed: {
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      } else {
        return this.$store.getters.getSources;
      }
    },
    getDataSources() {
      return this.$store.getters.getSources;
    },
    getProject() {
      this.project = this.$store.getters.project;
      return this.$store.getters.project;
    },
    getIntegrationTable() {
      return this.$store.getters.getIntegrations;
    },
    getTempFields() {
      return this.$store.getters.tempFields;
    },
  },
  created() {
    this.getDataUser();
  },
};
</script>

<style>
.data__source {
  width: 400px;
  display: block;
  margin: 0 auto;
}
</style>
