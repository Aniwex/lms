<template>
  <div>
    <div class="text-center" v-if="!getUsers || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      :rows="rowsUsers"
      :searchTerm="searchTerm"
      v-if="getUsers && user"
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
      v-if="getUsers && user"
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
        <!-- Column: login -->
        <span v-if="props.column.field === 'login'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.login }}</span>
        </span>
        <!-- Column: role -->
        <span
          v-else-if="props.column.field === 'role'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.role.title }}</span>
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
      title="Просмотр пользователя"
      size="lg"
      ref="modal__window"
      hide-footer
      @hidden="hideModal"
    >
      <div class="see-project__form">
        <h3 class="see-project__read">Данные для редактирования</h3>
        <div class="container__see-project">
          <div class="row__lables">
            <div class="row__source-lables">
              <label class="row__lables-label">Роль</label>
              <multiselect
                v-model="role"
                :options="options_roles"
                selectedLabel="Выбрано"
                class="multiselect-input"
                label="title"
                track-by="title"
                onclick="this.querySelector('input').focus();"
                placeholder="Выберите роль"
                :showLabels="false"
              >
              </multiselect>
            </div>
            <div class="row__date-lables">
              <label class="row__lables-label">Логин</label>
              <b-form-input
                class="row__user-input"
                v-model="modalObject.login"
                type="text"
                placeholder="Логин"
              />
            </div>
            <div class="row__date-lables">
              <label class="row__lables-label">Пароль</label>
              <validation-provider
                #default="{ errors }"
                name="Password"
                rules="required"
              >
                <b-input-group
                  class="input-group-merge"
                  :class="errors.length > 0 ? 'is-invalid' : null"
                  style="width: 300px !important"
                >
                  <b-form-input
                    v-model="password"
                    :state="errors.length > 0 ? false : null"
                    class="row__user-input"
                    :type="passwordFieldType"
                    placeholder="············"
                  />
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </div>
            <div class="row__date-lables">
              <label class="row__lables-label">Проекты</label>
              <multiselect
                v-model="project"
                :options="getProjects"
                ref="multiselect"
                selectedLabel="Выбрано"
                label="name"
                :multiple="true"
                track-by="name"
                placeholder="Выберите проект"
                :showLabels="false"
              >
              </multiselect>
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
  BInputGroup,
  BInputGroupAppend,
} from "bootstrap-vue";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required } from "@validations";
import { togglePasswordVisibility } from "@core/mixins/ui/forms";
import Search from "../layouts/components/Search.vue";
import Filters from "../layouts/components/Filters.vue";
import { Swiper, SwiperSlide } from "vue-awesome-swiper";
import { MoreVerticalIcon } from "vue-feather-icons";
import Ripple from "vue-ripple-directive";
import "swiper/css/swiper.css";
export default {
  components: {
    BInputGroupAppend,
    BInputGroup,
    ValidationProvider,
    ValidationObserver,
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
  mixins: [togglePasswordVisibility],
  data() {
    return {
      rowsUsers: [],
      password: "",
      errors: {},
      rowSelection: [],
      modalObject: [],
      arraySearch: "",
      user: "",
      modalCounter: 0,
      searchTerm: "",
      required,
      pageLength: 5,
      columns: [
        {
          label: "ID",
          field: "ID",
          thClass: "columnCenter",
        },
        {
          label: "Логин",
          field: "login",
          thClass: "columnCenter",
        },
        {
          label: "Роль",
          field: "role",
          thClass: "columnCenter",
        },
      ],
      getUsers: false,
      trHeight: 550,
      trMargin: 20,
      tempHeightPlus: null,
      tempHeightMinus: null,
      options_roles: [],
      project: [],
      options_projects: [],
      role: null,
    };
  },
  methods: {
    repeateAgain() {
      this.modalObject.config.push({
        key: null,
        value: null,
      });
      this.trHeight += 250;
    },
    removeItem(index) {
      this.modalObject.config.splice(index, 1);
      this.trHeight -= 250;
    },
    pushArraySearch(search) {
      this.arraySearch = search;
    },
    async getTableUsers() {
      await this.$store.dispatch("getDataUsers");
      await this.$store.dispatch("SET_PROJECTS");
      await axios
        .get("api/roles")
        .then((response) => {
          this.options_roles = response.data.roles;
          this.getUsers = true;
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
    },
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
    onCellClick(row) {
      if (row.column.label === "Действие") {
        this.ActionOnProject(row.event.path[0].innerText, row.row);
      }
    },
    async ActionOnProject(item, row) {
      if (item === "Посмотреть") {
        this.modalObject = row;
        this.options_projects = [];

        this.role = this.modalObject.role;
        await axios.get("api/users/" + this.modalObject.id).then((response) => {
          this.project = response.data.user.projects;
        });
      }
      if (item === "Удалить") {
        this.modalObject = row;
      }
    },
    hideModal() {
      this.$store.dispatch("getDataUsers");
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        let tempProjects = [];
        this.project.filter((item) => {
          tempProjects.push(item.id);
        });
        await axios
          .put("/api/users/" + this.modalObject.id, {
            login: this.modalObject.login,
            password: this.password,
            role_id: this.role.id,
            projects: tempProjects,
          })
          .then(() => {
            if (tempProjects.length === 0) {
              this.$store.dispatch("SET_USER");
              this.$store.dispatch("getDataUsers");
              this.$store.commit("SET_PROJECT", "");
              localStorage.removeItem("project");
              this.$refs["modal__window"].hide();
            } else {
              localStorage.setItem("project", JSON.stringify(this.project[0]));
              this.$store.dispatch("SET_USER");
              this.$store.commit("SET_PROJECT", this.project[0]);
              this.$refs["modal__window"].hide();
            }
          })
          .catch((error) => {
            this.errors = error.response.data;
          });
      } catch (error) {}
    },
    async deleteModal() {
      try {
        this.$swal({
          title: "Вы согласны удалить пользователя?",
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
              role: "Отмена",
              text: "Удаление пользователя было отменено",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
          } else {
            if (this.$store.getters.getUsers.length) {
              this.$store.getters.getUsers.filter((index, i) => {
                if (index.id === this.modalObject.id) {
                  axios
                    .delete("/api/users/" + this.modalObject.id)
                    .then(() => this.$store.dispatch("getDataUsers"))
                    .then(() => {
                      if (result.value) {
                        this.$swal({
                          icon: "success",
                          role: "Удалено!",
                          text: "Пользователь был удален.",
                          customClass: {
                            confirmButton: "btn btn-success",
                          },
                        });
                      }
                    })
                    .catch(() => {
                      this.$bvToast.toast("Удалять супер админа запрещено", {
                        title: `Ошибка`,
                        variant: "danger",
                        solid: true,
                        appendToast: true,
                        toaster: "b-toaster-top-center",
                        autoHideDelay: 2000,
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
        if (this.$store.getters.getUsers.length) {
          this.rowSelection.filter((item) => {
            this.$store.getters.getUsers.map((index, i) => {
              if (item.id === index.id) {
                axios
                  .delete("/api/users/" + item.id)
                  .then(() => {
                    this.$store.dispatch("getDataUsers");
                  })
                  .catch((error) => {
                    const vNodesMsg = [
                      `${Object.values(error.response.data.error)}`,
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
  },
  computed: {
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      } else {
        return this.$store.getters.getUsers;
      }
    },
    getProjects() {
      return this.$store.getters.projects;
    },
  },
  created() {
    this.getDataUser();
    this.getTableUsers();
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
  width: 600px;
  display: block;
  margin: 0 auto;
}
</style>
