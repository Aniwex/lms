<template>
  <div>
    <div class="text-center" v-if="!getIntegrations || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      :rows="getIntegrations"
      :searchTerm="searchTerm"
      v-if="getIntegrations && user"
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
      compactMode
      :rows="sorted"
      v-if="getIntegrations && user"
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
      >
      <template slot="table-row" slot-scope="props">
        <!-- Column: id -->
        <span v-if="props.column.field === 'ID'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.id }}</span>
        </span>
        <!-- Column: title -->
        <span v-if="props.column.field === 'title'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.title }}</span>
        </span>
        <!-- Column: slug -->
        <span
          v-else-if="props.column.field === 'slug'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.slug }}</span>
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
              <b-dropdown-item
                v-if="user.role.id === 1"
                v-b-modal.modal__seeIntegration
              >
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
      title="Просмотр интеграции"
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
                type="text"
                placeholder="Ключ"
                v-model="integration.title"
                class="row__user-input"
              />
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Код</label>
              <div class="container__row">
                <b-form-input
                  class="row__user-input"
                  v-model="modalObject.slug"
                  type="text"
                  placeholder="Код"
                />
                <span style="color: red" class="db__tc" v-if="errors.slug">
                  <span v-for="(err, index) in errors.slug" :key="index"
                    >{{ err }}<br
                  /></span>
                </span>
              </div>
            </div>
            <div class="form__group-options">
              <div>
                <label class="row__lables-label">Настройки </label>
                <b-form
                  ref="form"
                  class="repeater__form"
                  @submit.prevent="repeateAgain"
                  style="margin-bottom: 20px"
                >
                  <!-- Row Loop -->
                  <div
                    v-for="(item, index) in modalObject.config"
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
                        class="row__user-input"
                      />
                      <span
                        style="color: red"
                        class="db__tc"
                        v-if="errors['config.' + index + '.key']"
                        >{{ errors["config." + index + ".key"][0] }}</span
                      >
                    </b-form-group>
                    <!-- Значение -->
                    <b-form-group label="Значение">
                      <b-form-input
                        type="text"
                        placeholder="Значение"
                        v-model="item.value"
                        class="row__user-input"
                      />
                      <span
                        style="color: red"
                        class="db__tc"
                        v-if="errors['config.' + index + '.value']"
                        >{{ errors["config." + index + ".value"][0] }}</span
                      >
                    </b-form-group>
                    <hr />
                    <!-- Добавить Button -->
                    <b-button
                      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                      variant="primary"
                      @click="repeateAgain"
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
            <div
              v-if="!modalObject.config || this.modalObject.config.length === 0"
            >
              <label class="row__lables-label db__tc">Настроек нет </label>
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
  BForm,
  BFormGroup,
  BBadge,
  BPagination,
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
    BForm,
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
      errors: {},
      rowSelection: [],
      modalObject: [],
      arraySearch: "",
      user: "",
      modalCounter: 0,
      searchTerm: "",
      pageLength: 5,
      columns: [
        {
          label: "ID",
          field: "ID",
          thClass: "columnCenter",
        },
        {
          label: "Название",
          field: "title",
          thClass: "columnCenter",
          width: "600px",
        },
        {
          label: "Код",
          field: "slug",
          thClass: "columnCenter",
          width: "600px",
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
      trHeight: 400,
      trMargin: 20,
      tempHeightPlus: null,
      tempHeightMinus: null,
      options_integrations: [],
      integration: [],
    };
  },
  methods: {
    //Метод добавления репитера
    repeateAgain() {
      this.modalObject.config.push({
        key: null,
        value: null,
      });
    },
    //Метод удаления репитера
    removeItem(index) {
      this.modalObject.config.splice(index, 1);
    },
    //Метод добавления в массив поиска данных
    pushArraySearch(search) {
      this.arraySearch = search;
    },
    //Метод получения авторизованного пользователя
    async getDataUser() {
      await axios.get("/sanctum/csrf-cookie").then((response) => {
        axios
          .get("api/user")
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
            if (error.response.status === 401) {
              axios.get("/logout").then((resp) => {
                localStorage.removeItem(
                  "x_xsrf_token",
                  resp.config.headers["X-XSRF-TOKEN"]
                );
                localStorage.removeItem("project");
                this.$router.push("/");
                this.$store.commit("SET_ENTERED", false);
              });
            } else {
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
            }
          });
      });
    },
    onCellClick(row) {
      if (row.column.label === "Действие") {
        this.ActionOnProject(row.event.path[0].innerText, row.row);
      }
    },
    //Метод открытия модалки
    async ActionOnProject(item, row) {
      if (item === "Посмотреть") {
        this.modalObject = row;
        this.errors = {};
        this.integration = this.modalObject;
        if (this.modalObject.config === null) {
          this.modalObject.config = [];
        } else {
        }
      }
      if (item === "Удалить") {
        this.modalObject = row;
      }
    },
    //Метод закрытия модалки
    hideModal() {
      this.$store.dispatch("getIntegrationTable");
      this.$refs["modal__window"].hide();
    },
    //Метод сохранения интеграции
    async saveModal() {
      try {
        await axios
          .put("/api/integrations/" + this.modalObject.id, {
            id: this.modalObject.id,
            title: this.integration.title,
            slug: this.modalObject.slug,
            config: this.modalObject.config,
          })
          .then(() => {
            this.$store.dispatch("getIntegrationTable");
            this.errors = {};
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
    //Метод удаления интеграции
    async deleteModal() {
      try {
        this.$swal({
          title: "Вы согласны удалить интеграцию?",
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
              text: "Удаление интеграции было отменено",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
          } else {
            if (this.getIntegrations.length) {
              this.getIntegrations.filter((index, i) => {
                if (index.id === this.modalObject.id) {
                  axios
                    .delete("/api/integrations/" + this.modalObject.id)
                    .then(() => {
                      this.$store.dispatch("getIntegrationTable");
                      this.$swal({
                        icon: "success",
                        title: "Удалено!",
                        text: "Интеграция была удалена.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                    })
                    .catch((error) => {
                      this.errors = error.response.data;
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
    //Метод выбора определённых интеграций
    selectionChanged(params) {
      this.rowSelection = params.selectedRows;
      if (this.rowSelection.length) {
        this.check = true;
      } else {
        this.check = false;
      }
    },
    //Метод удаления определённых интеграций
    deleteSelected() {
      try {
        if (this.getIntegrations.length) {
          this.rowSelection.filter((item) => {
            this.getIntegrations.filter((index, i) => {
              if (item.id === index.id) {
                axios
                  .delete("/api/integrations/" + item.id)
                  .then(() => {
                    this.$store.dispatch("getIntegrationTable");
                  })
                  .catch((error) => {
                    this.errors = error.response.data;
                  });
              }
            });
          });
        }
      } catch (error) {}
    },
  },
  computed: {
    //Получение проекта из store
    getProject() {
      return this.$store.getters.project;
    },
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      } else {
        return this.getIntegrations;
      }
    },
    //Получение интеграций из store для таблицы
    getIntegrations() {
      return this.$store.getters.getIntegrations;
    },
  },
  created() {
    this.getDataUser();
    this.$store.dispatch("getIntegrationTable");
  },
};
</script>

<style scoped>
.btn__plus-integration {
  display: block;
  margin: 0 auto;
}
.btn__delete-integration {
  float: right;
}
.repeater__form {
  width: 400px;
  display: block;
  margin: 0 auto;
}
</style>
