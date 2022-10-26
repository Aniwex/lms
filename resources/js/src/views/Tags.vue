<template>
  <div>
    <div class="text-center" v-if="!getTag || !email">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <search
      :rows="rowsTags"
      :searchTerm="searchTerm"
      v-if="getTag && email"
      :email="email"
      @arraySearch="pushArraySearch"
    />
    <div
      v-if="rowSelection.length && this.email === 'admin@mail.ru'"
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
      v-if="getTag && email"
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
        <!-- Column: Name -->
        <span v-if="props.column.field === 'name'" class="text-nowrap db__tc">
          <span class="text-nowrap">{{ props.row.name }}</span>
        </span>
        <!-- Column: type -->
        <span
          v-else-if="props.column.field === 'type'"
          class="text-nowrap db__tc"
        >
          <div v-if="props.row.type">
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
        <!-- Column: plus_words_client -->
        <span
          v-else-if="props.column.field === 'plus_words_client'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.plus_words_client }}</span>
        </span>
        <!-- Column: minus_words_client -->
        <span
          v-else-if="props.column.field === 'minus_words_client'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.minus_words_client }}</span>
        </span>
        <!-- Column: plus_words_operator -->
        <span
          v-else-if="props.column.field === 'plus_words_operator'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.plus_words_operator }}</span>
        </span>
        <!-- Column: minus_words_operator -->
        <span
          v-else-if="props.column.field === 'minus_words_operator'"
          class="text-nowrap db__tc"
        >
          <span class="text-nowrap">{{ props.row.minus_words_operator }}</span>
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
              <b-dropdown-item
                v-if="email === 'admin@mail.ru'"
                @click="deleteModal"
              >
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
                  <label class="row__lables-label">Тип</label>
                  <b-form-checkbox
                    v-model="data.type"
                    value="true"
                    class="form__checkbox"
                  >
                    Целевой (да/нет)
                  </b-form-checkbox>
                </div>
                <div class="row__source-lables">
                  <label class="row__lables-label">Плюс слова клиента</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.plus_words_client"
                    type="text"
                    placeholder="Тип"
                  />
                </div>
                <div class="row__source-lables">
                  <label class="row__lables-label">Минус слова клиента</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.minus_words_client"
                    type="text"
                    placeholder="Тип"
                  />
                </div>
                <div class="row__source-lables">
                  <label class="row__lables-label">Плюс слова оператора</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.plus_words_operator"
                    type="text"
                    placeholder="Тип"
                  />
                </div>
                <div class="row__source-lables">
                  <label class="row__lables-label">Минус слова оператора</label>
                  <b-form-input
                    class="row__user-input"
                    v-model="data.minus_words_operator"
                    type="text"
                    placeholder="Тип"
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
      modalArray: [],
      modalCounter: 0,
      searchTerm: "",
      email: "",
      pageLength: 5,
      columns: [
        {
          label: "Название",
          field: "name",
          thClass: "columnCenter",
        },
        {
          label: "Тип",
          field: "type",
          thClass: "columnCenter",
        },
        {
          label: "Плюс слова клиента",
          field: "plus_words_client",
          thClass: "columnCenter",
        },
        {
          label: "Минус слова клиента",
          field: "minus_words_client",
          thClass: "columnCenter",
        },
        {
          label: "Плюс слова оператора",
          field: "plus_words_operator",
          thClass: "columnCenter",
        },
        {
          label: "Минус слова оператора",
          field: "minus_words_operator",
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
      getTag: false,
      arraySearch:'',
    };
  },
  methods: {
    changeSlideNext() {
      this.modalCounter++;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    changeSlidePrev() {
      this.modalCounter--;
      this.arrayChat = this.modalArray[this.modalCounter].dialog;
    },
    async getDataUser() {
      await axios.get("/sanctum/csrf-cookie").then((response) => {
        axios.get("api/user ").then((response) => {
          this.email = response.data.email;
          if (this.email === "admin@mail.ru") {
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
    },
    async getTags() {
      await axios.get("/api/tags").then((response) => {
        this.rowsTags = response.data;
        this.getTag = true;
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
        this.rowsTags.filter((item) => {
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
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      try {
        await axios.put("/api/tags/" + this.modalArray[this.modalCounter].id, {
          id: this.modalArray[this.modalCounter].id,
          name: this.modalArray[this.modalCounter].name,
          type: this.modalArray[this.modalCounter].type,
          plus_words_client:
            this.modalArray[this.modalCounter].plus_words_client,
          minus_words_client:
            this.modalArray[this.modalCounter].minus_words_client,
          plus_words_operator:
            this.modalArray[this.modalCounter].plus_words_operator,
          minus_words_operator:
            this.modalArray[this.modalCounter].minus_words_operator,
        });
        this.$refs["modal__window"].hide();
        await this.getTags();
      } catch (error) {
        alert("Error: " + error);
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
          if (result.value) {
            this.$swal({
              icon: "success",
              title: "Удалено!",
              text: "Ваше обращение было удалено.",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
            if (this.rowsTags.length) {
              this.rowsTags.filter((index, i) => {
                if (index.id === this.modalArray.id) {
                  axios.delete("/api/tags/" + this.modalArray.id);
                  this.rowsTags.splice(i, 1);
                }
              });
            }
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
      if (this.rowsTags.length) {
        this.rowSelection.filter((item) => {
          this.rowsTags.map((index, i) => {
            if (item.id === index.id) {
              axios.delete("/api/tags/" + item.id);
              this.rowsTags.splice(i, 1);
            }
          });
        });
      }
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
        return this.rowsTags;
      }
    },
  },
  created() {
    this.getDataUser();
    this.getTags();
  },
};
</script>

<style>
</style>
