<template>
  <div>
    <div class="text-center" v-if="!getProjects || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      :rows="rowsProjects"
      :searchTerm="searchTerm"
      v-if="getProjects && user"
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
      v-if="getProjects && user"
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
        <!-- Column: name -->
        <span v-if="props.column.field === 'name'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.name }}</span>
        </span>
        <!-- Column: domain -->
        <span
          v-else-if="props.column.field === 'domain'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.domain }}</span>
        </span>
        <!-- Column: mirrows -->
        <span
          v-else-if="props.column.field === 'mirrows'"
          class="text-nowrap db__tc"
        >
          <span
            v-for="(mirrow, index) in props.row.mirrows"
            :key="index"
            class="text-nowrap"
            >{{ mirrow }} <br
          /></span>
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
    <!-- modal see integration -->
    <b-modal
      id="modal__seeIntegration"
      centered
      cancel-role="Отмена"
      size="lg"
      title="Просмотр проекта"
      ref="modal__window"
      hide-footer
      @hidden="hideModal"
    >
      <div class="see-project__form">
        <h3 class="see-project__read">Данные для редактирования</h3>
        <div class="container__see-project">
          <div class="row__lables">
            <div class="row__source-lables">
              <label class="row__lables-label">Название</label>

              <b-form-input
                class="row__user-input"
                v-model="modalObject.name"
                type="text"
                placeholder="Название"
              />
            </div>
            <div class="row__source-lables">
              <label class="row__lables-label">Домен</label>
              <b-form-input
                class="row__user-input"
                v-model="modalObject.domain"
                type="text"
                placeholder="Название"
              />
            </div>
            <div class="form__group-options">
              <div>
                <label class="row__lables-label">Зеркала</label>

                <b-form
                  ref="form"
                  v-if="modalObject.mirrows"
                  class="repeater__form-project"
                  @submit.prevent="repeateAgain"
                >
                  <!-- Row Loop -->
                  <div
                    v-for="(item, index) in modalObject.mirrows"
                    :id="index"
                    :key="index"
                    ref="row"
                    :style="{ margin: trMargin + 'px' }"
                  >
                    <!-- Значение -->
                    <b-form-group label="Значение">
                      <b-form-input
                        type="text"
                        placeholder="Значение"
                        v-model="modalObject.mirrows[index]"
                        class="row__user-input"
                        :state="
                          modalObject.mirrows[index] === '' ? false : true
                        "
                      />
                      <span class="db__tc" v-if="errors['mirrows.' + index]">{{
                        errors["mirrows." + index][0]
                      }}</span>
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
                      class="btn__delete-project"
                    >
                      <feather-icon icon="XIcon" class="mr-25" />
                      <span>Удалить</span>
                    </b-button>
                  </div>
                  <div
                    class="db__tc"
                    v-if="modalObject.mirrows.length === 0"
                    style="margin-bottom: 20px"
                  >
                    <b-button
                      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                      variant="primary"
                      @click="repeateAgain"
                    >
                      <feather-icon icon="PlusIcon" class="mr-25" />
                      <span>Добавить ещё</span>
                    </b-button>
                  </div>
                </b-form>
              </div>
            </div>

            <div class="row__date-lables">
              <label class="row__lables-label">Пользователи</label>
              <multiselect
                onclick="this.querySelector('input').focus();"
                class="multiselect-input"
                v-model="users"
                :options="getUsers"
                :multiple="true"
                label="login"
                track-by="login"
                placeholder="Выберите пользователя"
                selectLabel="Нажмите enter для выбора"
                deselectLabel="Нажмите enter для удаления"
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
import { ValidationProvider, ValidationObserver } from "vee-validate";
import {
  BInputGroup,
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
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
export default {
  components: {
    ValidationProvider,
    ValidationObserver,
    BInputGroup,
    vSelect,
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
      rowsProjects: [],
      password: "",
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
          field: "name",
          thClass: "columnCenter",
        },
        {
          label: "Домен",
          field: "domain",
          thClass: "columnCenter",
        },
        {
          label: "Зеркала",
          field: "mirrows",
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
      trHeight: 550,
      trMargin: 20,
      tempHeightPlus: null,
      tempHeightMinus: null,
      mirrows: [
        {
          value: "",
        },
      ],
      users: [],
    };
  },
  methods: {
    repeateAgain() {
      this.modalObject.mirrows.push("");
      this.trHeight += 250;
    },
    removeItem(index) {
      this.modalObject.mirrows.splice(index, 1);
      this.trHeight -= 250;
    },
    pushArraySearch(search) {
      this.arraySearch = search;
    },

    async getDataUser() {
      await this.$store.dispatch("getDataUsers");
      await this.$store.dispatch("SET_PROJECTS");
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
          });
      });
    },
    onCellClick(row) {
      if (row.column.label === "Действие") {
        this.ActionOnProject(row.event.path[0].innerText, row.row);
      }
    },
    async ActionOnProject(item, row) {
      this.users = [];
      this.modalObject = row;
      await axios
        .get("api/projects/" + this.modalObject.id)
        .then((response) => {
          this.users = response.data.project.users;
        });
    },
    hideModal() {
      this.$store.dispatch("SET_PROJECTS");
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        let tempUserId = [];
        this.users.filter((item) => {
          tempUserId.push(item.id);
        });
        await axios
          .put("/api/projects/" + this.modalObject.id, {
            name: this.modalObject.name,
            mirrows:
              this.modalObject.mirrows.length === 0
                ? [""]
                : this.modalObject.mirrows,
            domain: this.modalObject.domain,
            users: tempUserId,
          })
          .then(() => {
            this.$store.dispatch("SET_PROJECTS");
            this.$refs["modal__window"].hide();
            this.errors = {};
          });

        await this.$store.dispatch("SET_USER");
      } catch (error) {
        this.errors = {};
        this.errors = error.response.data.errors;
      }
    },
    async deleteModal() {
      try {
        this.$swal({
          role: "Вы согласны удалить проект?",
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
          if (this.rowsProjects.length) {
            this.rowsProjects.filter((index, i) => {
              if (index.id === this.modalObject.id) {
                axios
                  .delete("/api/projects/" + this.modalObject.id)
                  .then(() => {
                    this.$store.dispatch("SET_PROJECTS");
                    this.$store.dispatch("SET_USER");
                  })
                  .then(() => {
                    if (result.value) {
                      this.$swal({
                        icon: "success",
                        role: "Удалено!",
                        text: "Проект был удален.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                    } else if (result.dismiss === "cancel") {
                      this.$swal({
                        role: "Отмена",
                        text: "Удаление проекта было отменено",
                        icon: "error",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                    }
                  })
                  .catch(() => {
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
        if (this.getProjects.length) {
          this.rowSelection.filter((item) => {
            this.getProjects.map((index, i) => {
              if (item.id === index.id) {
                axios
                  .delete("/api/projects/" + item.id)
                  .then(() => {
                    this.$store.dispatch("SET_PROJECTS");
                  })
                  .catch((error) => {
                    
                  });
              }
            });
          });
        }
      } catch (error) {
        
      }
    },
  },
  computed: {
    getProject() {
      return this.$store.getters.project;
    },
    getUsers() {
      return this.$store.getters.getUsers;
    },
    sorted() {
      if (this.arraySearch.length) {
        return this.arraySearch;
      } else {
        return this.$store.getters.projects;
      }
    },
    getProjects() {
      return this.$store.getters.projects;
    },
  },
  created() {
    this.getDataUser();
  },
};
</script>
<style scoped>
.btn__plus-projects {
  display: block;
  margin: 0 auto;
}
.btn__delete-project {
  float: right;
}
.repeater__form-project {
  width: 400px;
  display: block;
  margin: 0 auto;
}
.row__users-input {
  width: 300px;
  padding: 20px 0px;
}
</style>
