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
        <!-- Column: login -->
        <span v-if="props.column.field === 'login'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.login }}</span>
        </span>
        <!-- Column: role -->
        <span
          v-else-if="props.column.field === 'role'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap"
            >ID: {{ props.row.role.id }} <br />
            Роль: {{ props.row.role.title }}</span
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
      role="Просмотр пользователя"
      cancel-role="Отмена"
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
            <h3 class="see-project__read">Данные для редактирования</h3>
            <div class="container__see-project">
              <div class="row__lables">
                <div class="row__source-lables">
                  <label class="row__lables-label">Логин</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.login"
                    type="text"
                    placeholder="Логин"
                  />
                </div>
                <div class="row__date-lables">
                  <label class="row__lables-label">Роль</label>
                  <b-form-select
                    style="display: block; text-align: center"
                    class="form__appeal-input"
                    v-model="role"
                    :options="options_roles"
                  >
                  </b-form-select>
                </div>
                <div class="row__date-lables">
                  <label class="row__lables-label">Пароль</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="password"
                    type="text"
                    placeholder="Пароль"
                    :state="password !== ''"
                  />
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
      rowsUsers: [],
      password: "",
      rowSelection: [],
      modalArray: [],
      arraySearch: "",
      user: "",
      modalCounter: 0,
      searchTerm: "",
      pageLength: 5,
      columns: [
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
      getUsers: false,
      trHeight: 550,
      trMargin: 20,
      tempHeightPlus: null,
      tempHeightMinus: null,
      options_roles: [],
      role: null,
    };
  },
  methods: {
    repeateAgain() {
      this.modalArray[this.modalCounter].config.push({
        key: null,
        value: null,
      });
      this.trHeight += 250;
    },
    removeItem(index) {
      this.modalArray[this.modalCounter].config.splice(index, 1);
      this.trHeight -= 250;
    },
    changeSlideNext() {
      this.modalCounter++;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    pushArraySearch(search) {
      this.arraySearch = search;
    },
    changeSlidePrev() {
      this.modalCounter--;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    async getTableUsers() {
      await this.$store.dispatch("getDataUsers");
      await axios
        .get("api/roles")
        .then((response) => {
          this.options_roles = response.data.roles;
          this.options_roles.filter((item, i) => {
            item["value"] = item["text"] = i + 1;
          });
          this.options_roles.unshift({ value: null, text: "—" });
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
        let temp = row;
        this.modalArray = [];
        let i = 0;
        this.$store.getters.getUsers.filter((item) => {
          if (temp.id === item.id) {
            i++;
          }
          if (i === 1) {
            this.modalArray.push(item);
          }
        });
      }
      if (item === "Удалить") {
        this.modalArray = row;
      }
    },
    hideModal() {
      this.getTableUsers();
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        await axios.put("/api/users/" + this.modalArray[this.modalCounter].id, {
          login: this.modalArray[this.modalCounter].login,
          password: this.password,
          role_id: this.role,
        });
        this.$refs["modal__window"].hide();
        await this.getTableUsers();
      } catch (error) {
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
    },
    async deleteModal() {
      try {
        this.$swal({
          role: "Вы согласны удалить пользователя?",
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
          if (this.$store.getters.getUsers.length) {
            this.$store.getters.getUsers.filter((index, i) => {
              if (index.id === this.modalArray.id) {
                axios
                  .delete("/api/users/" + this.modalArray.id)
                  .then(() => this.$store.getters.getUsers.splice(i, 1))
                  .then(() => {
                    if (result.value) {
                      this.$swal({
                        icon: "success",
                        role: "Удалено!",
                        text: "Ваш пользователь был удален.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                    } else if (result.dismiss === "cancel") {
                      this.$swal({
                        role: "Отмена",
                        text: "Удаление пользователя было отменено",
                        icon: "error",
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
      if (this.$store.getters.getUsers.length) {
        this.rowSelection.filter((item) => {
          this.$store.getters.getUsers.map((index, i) => {
            if (item.id === index.id) {
              axios.delete("/api/users/" + item.id);
              this.$store.getters.getUsers.splice(i, 1);
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
        return this.$store.getters.getUsers;
      }
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
