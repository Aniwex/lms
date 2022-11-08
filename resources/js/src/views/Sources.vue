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
        <!-- Column: Integration -->
        <span
          v-if="props.column.field === 'integration'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.integration.title }}</span>
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
        <span
          v-else-if="props.column.field === 'source_data'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.source_data }}</span>
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
      no-close-on-esc
      no-close-on-backdrop
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
            <h3 class="see-project__read">Данные для редактирования</h3>
            <div class="container__see-project">
              <div class="row__lables">
                <div class="row__date-lables" v-if="data.integration">
                  <label class="row__lables-label">Интеграция</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.integration.title"
                    type="text"
                    placeholder="Интеграция"
                  />
                </div>
                <div class="row__date-lables">
                  <label class="row__lables-label">Название</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.name"
                    type="text"
                    placeholder="Название"
                  />
                </div>
                <div class="row__source-lables">
                  <label class="row__lables-label">Код</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.code"
                    type="text"
                    placeholder="Код"
                  />
                </div>
                <div class="row__source-lables">
                  <label class="row__lables-label">Настройки</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.source_data"
                    type="text"
                    placeholder="Данные по источнику"
                  />
                  <label for=""></label>
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
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import { MoreVerticalIcon } from "vue-feather-icons";
import Ripple from "vue-ripple-directive";
import "swiper/css/swiper.css";
export default {
  components: {
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
      rowSelection: [],
      modalArray: [],
      modalCounter: 0,
      searchTerm: "",
      pageLength: 5,
      columns: [
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
    };
  },
  methods: {
    pushArraySearch(search) {
      this.arraySearch = search;
    },
    changeSlideNext() {
      this.modalCounter++;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    changeSlidePrev() {
      this.modalCounter--;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    async getDataUser() {
      if (!this.$store.getters.project) {
        this.$router.push("/Home");
      } else {
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
        let temp = row;
        this.modalArray = [];
        let i = 0;
        this.getDataSources.filter((item) => {
          if (temp.id === item.id) {
            i++;
          }
          if (i === 1) {
            this.modalArray.push(item);
          }
        });
        await axios
          .get()
          .then((response) => {
            console.log(response);
          })
          .catch((error) => {
            console.log(error.response.data.error);
          });
      }
      if (item === "Удалить") {
        this.modalArray = row;
      }
    },
    hideModal() {
      this.modalArray = this.getDataSources;
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        await axios
          .put(
            " api/projects/" +
              this.project.id +
              "/sources/" +
              this.modalArray[this.modalCounter].id,
            {
              integration_id: this.modalArray[this.modalCounter].integration.id,
              name: this.modalArray[this.modalCounter].name,
              code: this.modalArray[this.modalCounter].code,
              data: this.modalArray[this.modalCounter].data,
            }
          )
          .then(() => {
            this.$refs["modal__window"].hide();
          });
      } catch (error) {
        const vNodesMsg = [`${Object.values(error.response.data.errors)}`];
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
          if (this.getDataSources.length) {
            this.getDataSources.filter((index, i) => {
              if (index.id === this.modalArray.id) {
                axios
                  .delete(
                    "api/projects/" +
                      this.project.id +
                      "/sources/" +
                      this.modalArray.id
                  )
                  .then(() => {
                    this.getDataSources.splice(i, 1);
                    this.$swal({
                      icon: "success",
                      title: "Удалено!",
                      text: "Обращение было удалено.",
                      customClass: {
                        confirmButton: "btn btn-success",
                      },
                    });
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
    selectionChanged(params) {
      this.rowSelection = params.selectedRows;
      if (this.rowSelection.length) {
        this.check = true;
      } else {
        this.check = false;
      }
    },
    deleteSelected() {
      if (this.getDataSources.length) {
        this.rowSelection.filter((item) => {
          this.getDataSources.map((index, i) => {
            if (item.id === index.id) {
              axios
                .delete(
                  "api/projects/" + this.project.id + "/sources/" + +item.id
                )
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
              this.getDataSources.splice(i, 1);
            }
          });
        });
      }
    },
  },
  computed: {
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      } else {
        return this.getDataSources;
      }
    },
    getDataSources() {
      return this.$store.getters.getSources;
    },
    getProject() {
      this.project = this.$store.getters.project;
      return this.$store.getters.project;
    },
  },
  created() {
    this.getDataUser();
  },
};
</script>

<style>
</style>
