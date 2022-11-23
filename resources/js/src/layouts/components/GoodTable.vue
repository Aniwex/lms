<template>
  <div v-if="LocalStorageProject || getProject">
    <div class="text-center" v-if="!getDataTable || !user">
      <b-button variant="primary" disabled class="mr-1">
        <b-spinner small />
        Загрузка...
      </b-button>
    </div>
    <!-- search input -->
    <div class="custom-search" v-if="getDataTable && user">
      <div class="container__search-from">
        <div class="search__form align-items-center">
          <b-form-input
            v-model="searchTerm"
            placeholder="Поиск"
            type="text"
            class="search__input"
            @input="handleSearch"
          />
        </div>
        <div class="create__appeal" v-if="user.role.id === 1">
          <b-button
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            variant="primary"
            to="NewAppeal"
            v-b-tooltip.hover.top="'Добавить обращение'"
            v-if="this.$route.path === '/Home'"
          >
            Добавить обращение
          </b-button>
        </div>
      </div>
    </div>
    <!-- filers -->
    <filters
      @arrayCheckboxUser="pushChangeCheckBox"
      @selected="pushSelected"
      @sortedFilter="pushSortedFilter"
      :getDataTable="getDataTable"
      :role_id="user.role.id"
      :project="getProject"
      :options_source="getSourceTable"
      :options_tags="getTagsTable"
      :options_manager="options_manager_check"
      :options_client="options_client_check"
      v-if="getDataTable && getTagsTable && getSourceTable && user"
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
      v-if="getDataTable && user.role"
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
        <span v-if="props.column.field === 'ID'" class="text-nowrap">
          <span class="text-nowrap">{{ props.row.id }}</span>
        </span>
        <span
          v-else-if="props.column.field === 'datetime'"
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
        <span class="db__tc" v-else-if="props.column.field === 'source '">
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
                v-if="managerObject[props.row.id]"
                :icon="managerObject[props.row.id].icon"
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
                v-for="(m, index) in managerIcons"
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
                v-if="clientObject[props.row.id]"
                :icon="clientObject[props.row.id].icon"
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
                v-for="(c, index) in clientIcons"
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
          <span>{{ props.row.manager.comment }}</span>
        </span>
        <!-- Column: client_comment -->
        <span
          style="word-break: break-all"
          v-else-if="props.column.field === 'client_comment'"
        >
          <span>{{ props.row.client.comment }}</span>
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
      v-if="openModalProject"
      id="modal__seeProject"
      centered
      title="Просмотр обращения"
      cancel-title="Отмена"
      size="lg"
      ref="modal__window"
      hide-footer
      @hidden="hideModal"
    >
      <div class="see-project__form">
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
            <div class="row__tag-lables">
              <label class="row__lables-label">Дата и время</label>
              <flat-pickr
                placeholder="Выберите дату и время"
                v-model="modalObject.datetime"
                class="form-control row__datetime-pickr"
                :config="{
                  enableTime: true,
                  datetimeFormat: 'Y-m-d H:i:s',
                  enableSeconds: true,
                }"
                :disabled="
                  user.role.id === 3 || user.role.id === 2 ? true : false
                "
              />
            </div>
            <div class="row__tag-lables">
              <label class="row__lables-label">Продолжительность звонка</label>
              <div class="mutiselect-margin">
                <div class="modal__input">
                  <b-button
                    @click="quantity_minus()"
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
                    v-if="modalObject.duration"
                    class="input__number form-control"
                    v-model="modalObject.duration.original"
                    type="number"
                    :state="modalObject.duration.original !== 0"
                    :disabled="
                      user.role.id === 3 || user.role.id === 2 ? true : false
                    "
                  />
                  <b-button
                    v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                    variant="primary"
                    type="button"
                    size="sm"
                    @click="quantity_plus()"
                  >
                    <plus-icon
                      size="1x"
                      class="plus-icon align-middle mr-25"
                    ></plus-icon>
                  </b-button>
                </div>
                <div v-if="modalObject.duration.original === 0">
                  <errorValidation
                    v-if="errors"
                    :errors="errors"
                    :error="errors.duration"
                  ></errorValidation>
                </div>
              </div>
            </div>
            <div class="row__tag-lables">
              <label class="row__lables-label">Источник</label>
              <div
                class="multiselect-input mutiselect-margin"
                :class="{ mb__50: errors.source_id }"
              >
                <multiselect
                  v-model="modalObject.source"
                  onclick="this.querySelector('input').focus();"
                  :options="getSourceTable"
                  selectedLabel="Выбрано"
                  label="name"
                  track-by="name"
                  placeholder="Выберите источник"
                  :showLabels="false"
                  :disabled="user.role.id === 3 ? true : false"
                >
                </multiselect>
                <errorValidation
                  v-if="errors"
                  :errors="errors"
                  :error="errors.source_id"
                ></errorValidation>
              </div>
            </div>
            <div class="row__user-lables">
              <label class="row__lables-label">Пользователь</label>
              <b-form-input
                class="row__user-input mutiselect-margin"
                type="text"
                placeholder="+7 (999) 999-99-99"
                v-model="phone"
                v-mask="'+7 (###) ###-##-##'"
                maxlength="18"
                :disabled="
                  user.role.id === 3 || user.role.id === 2 ? true : false
                "
              />
            </div>
            <div class="row__tag-lables">
              <label class="row__lables-label">Тэги</label>
              <multiselect
                v-model="modalObject.tags"
                onclick="this.querySelector('input').focus();"
                :options="getTagsTable"
                selectedLabel="Выбрано"
                :multiple="true"
                class="multiselect-input mutiselect-margin"
                label="name"
                track-by="name"
                placeholder="Выберите тэг"
                :showLabels="false"
                :disabled="user.role.id === 3 ? true : false"
              >
              </multiselect>
            </div>
            <div class="row__tag-lables">
              <label class="row__lables-label">Проверка менеджера</label>
              <multiselect
                v-model="manager__check"
                onclick="this.querySelector('input').focus();"
                :options="options_manager_check"
                selectedLabel="Выбрано"
                class="multiselect-input mutiselect-margin"
                label="text"
                track-by="text"
                placeholder="Проверка менеджера"
                :showLabels="false"
                :disabled="user.role.id === 3 ? true : false"
              >
              </multiselect>
            </div>
            <div class="row__comment-manager-lables">
              <label class="row__lables-label">Комментарий менеджера</label>
              <b-form-textarea
                v-if="modalObject.manager"
                class="form__textarea-manager"
                placeholder="Комментарий менеджера"
                rows="5"
                no-resize
                v-model="modalObject.manager.comment"
                :disabled="user.role.id === 3 ? true : false"
              />
            </div>
            <div class="row__tag-lables">
              <label class="row__lables-label">Проверка клиента</label>
              <multiselect
                v-model="client__check"
                onclick="this.querySelector('input').focus();"
                :options="options_client_check"
                selectedLabel="Выбрано"
                class="multiselect-input mutiselect-margin"
                label="text"
                track-by="text"
                placeholder="Проверка клиента"
                :showLabels="false"
                :disabled="user.role.id === 2 ? true : false"
              >
              </multiselect>
            </div>
            <div class="row__comment-client-lables">
              <label class="row__lables-label">Комментарий клиента</label>
              <b-form-textarea
                v-if="modalObject.client"
                class="form__textarea-client"
                placeholder="Комментарий клиента"
                rows="5"
                no-resize
                v-model="modalObject.client.comment"
                :disabled="user.role.id === 2 ? true : false"
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
    </b-modal>
  </div>
  <div v-else>
    <b-button
      style="display: none"
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
      title="Проекты"
      ref="b_modal"
      modal-close="false"
      no-close-on-esc
      no-close-on-backdrop
      hide-footer
    >
      <div v-if="option_project.length !== 0">
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
      </div>
      <div v-else>Доступных проектов нет</div>
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
import Search from "./Search.vue";
import Filters from "./Filters.vue";
import { VueGoodTable } from "vue-good-table";
import flatPickr from "vue-flatpickr-component";
import "@core/scss/vue/libs/vue-flatpicker.scss";
import "vue-good-table/dist/vue-good-table.css";
import Ripple from "vue-ripple-directive";
import "@core/scss/vue/libs/vue-select.scss";
import errorValidation from "../../views/error/errorValidation";
import axios from "axios";
import {
  Trash2Icon,
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
    errorValidation,
    Trash2Icon,
    BSpinner,
    Filters,
    Search,
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
      sortedFilter: [],
      clientObject: {},
      pageLength: 10,
      dir: false,
      columns: [
        {
          label: "ID",
          field: "id",
          thClass: "columnCenter",
        },
        {
          label: "Дата и время",
          field: "datetime",
          thClass: "columnCenter",
        },
        {
          label: "Продол. звонка",
          field: "duration",
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
          thClass: "columnCenter",
        },
        {
          label: "Клиент",
          field: "client",
          thClass: "columnCenter",
          // tdClass: 'text-center', пользовательский класс для ячеек таблицы
        },
        {
          label: "Тэги",
          field: "tags",
          thClass: "columnCenter",
        },
        // {
        //   label: "Комментарий менеджера",
        //   field: "manager_comment",
        //   thClass: "columnCenter",
        // },
        // {
        //   label: "Комментарий клиента",
        //   field: "client_comment",
        //   thClass: "columnCenter",
        // },
        {
          label: "Действие",
          field: "Действие",
          thClass: "columnCenter",
          width: "130px",
        },
      ],
      errors: {},
      selected: {},
      options_tags: [],
      options_source: [],
      managerIcons: [
        { value: "целевой", icon: "CheckCircleIcon" },
        { value: "нецелевой", icon: "XCircleIcon" },
        { value: "не установленный", icon: "XSquareIcon" },
      ],
      clientIcons: [
        { value: "целевой", icon: "CheckCircleIcon" },
        { value: "нецелевой", icon: "XCircleIcon" },
        { value: "не проверенный", icon: "XSquareIcon" },
      ],
      rows: [],
      searchTerm: "",
      arr: [],
      rowSelection: [],
      arraySearch: [],
      managerArray: [],
      clientArray: [],
      modalObject: [],
      modalCounter: 0,
      client__check: [],
      manager__check: [],
      checkboxUser: [],
      arrayChat: [],
      project: [],
      phone: "",
      option_project: [],
      user: "",
      tempProject: null,
      historyArray: [],
      openModalProject: false,
    };
  },
  methods: {
    async selectProject(project) {
      await this.$store.commit("SET_PROJECT", project);
      await localStorage.setItem("project", JSON.stringify(project));
      await this.$store.dispatch("getDataTable");
      await this.$store.dispatch("getSourceTable");
      await this.$store.dispatch("getTagsTable");
    },
    //modalSeeProject
    quantity_minus() {
      if (this.user.role.id === 1) {
        if (this.modalObject.duration.original >= 1) {
          this.modalObject.duration.original--;
        }
      }
    },
    quantity_plus() {
      if (this.user.role.id === 1) {
        this.modalObject.duration.original++;
      }
    },
    hideModal() {
      this.$store.dispatch("getDataTable");
      this.$refs["modal__window"].hide();
    },
    async saveModal() {
      if (this.manager__check === null) {
        this.manager__check = {
          value: "unidentified",
        };
      }
      if (this.phone === "") {
        this.phone = this.modalObject.phone.original;
      }
      if (this.client__check === null) {
        this.client__check = {
          value: "unchecked",
        };
      }
      if (this.modalObject.source === null) {
        this.modalObject.source = {
          id: 0,
        };
      }
      try {
        let tempTagsId = [];
        this.modalObject.tags.filter((item) => {
          tempTagsId.push(item.id);
        });
        await axios
          .put(
            " api/projects/" +
              this.getProject.id +
              "/claims/" +
              this.modalObject.id,
            {
              id: this.modalObject.id,
              datetime: this.modalObject.datetime,
              duration: this.modalObject.duration.original,
              manager_comment: this.modalObject.manager_comment,
              source_id: this.modalObject.source.id,
              phone: this.phone,
              tags: tempTagsId,
              client_comment: this.modalObject.client_comment,
              manager_check: this.manager__check.value,
              client_check: this.client__check.value,
              dialog: this.modalObject.dialog,
              manager_comment: this.modalObject.manager.comment,
              client_comment: this.modalObject.client.comment,
            }
          )
          .then(() => {
            this.$store.dispatch("getDataTable");
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
    //modalSeeProject end
    deleteSelected() {
      try {
        if (this.getDataTable.length) {
          this.rowSelection.filter((item) => {
            this.getDataTable.map((index, i) => {
              if (item.id === index.id) {
                axios
                  .delete(
                    "api/projects/" + this.getProject.id + "/claims/" + item.id
                  )
                  .then(() => {
                    this.$store.dispatch("getDataTable");
                  })
                  .catch((error) => {});
              }
            });
          });
        }
      } catch (error) {}
    },
    handleSearch() {
      this.arraySearch = [];
      //let key = Object.keys(this.getDataTable[0]);
      if (this.getDataTable.length) {
        this.getDataTable.filter((item) => {
          // for (let k in key) {
          //   console.log(item.phone.formatted);
          //   if (
          //     item[key[k]] !== null &&
          //     item[key[k]].toString().includes(this.searchTerm)
          //   ) {
          //     this.arraySearch.push(item);
          //   }
          // }
          if (
            item.phone.formatted !== null &&
            item.phone.formatted.toString().includes(this.searchTerm)
          ) {
            this.arraySearch.push(item);
          }
        });
      }
      this.arraySearch = [...new Set(this.arraySearch)];
    },
    async saveProjectModal() {
      await this.$store.commit("SET_PROJECT", this.project);
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
    pushChangeCheckBox(user) {
      this.checkboxUser = user;
    },
    pushSelected(select) {
      this.selected = select;
    },
    pushSortedFilter(sorted) {
      this.sortedFilter = sorted;
      //console.log(this.sortedFilter);
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
    selectionChanged(params) {
      this.rowSelection = params.selectedRows;
      if (this.rowSelection.length) {
        this.check = true;
      } else {
        this.check = false;
      }
    },
    historyUser(row) {
      this.searchTerm = row.phone.formatted;
      this.arraySearch = [];
      this.getDataTable.filter((item) => {
        if (item.phone.formatted === this.searchTerm) {
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
        this.phone = "";
        await axios
          .get("api/projects/" + this.getProject.id + "/claims/" + row.id)
          .then((response) => {
            this.modalObject = response.data.claim;
            this.openModalProject = true;
          });
        this.errors = {};
        this.client__check = this.modalObject.client.check;
        this.manager__check = this.modalObject.manager.check;
        this.phone = this.modalObject.phone.formatted;
        this.arrayChat = this.modalObject.dialog;
        this.$bvModal.show("modal__seeProject");
      }
      if (item === "Удалить") {
        this.modalObject = row;
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
          if (result.dismiss === "cancel") {
            this.$swal({
              title: "Отмена",
              text: "Удаление обращения было отменено",
              icon: "error",
              customClass: {
                confirmButton: "btn btn-success",
              },
            });
          } else {
            if (this.getDataTable.length) {
              this.getDataTable.filter((index, i) => {
                if (index.id === this.modalObject.id) {
                  axios
                    .delete(
                      "api/projects/" +
                        this.getProject.id +
                        "/claims/" +
                        this.modalObject.id
                    )
                    .then(() => {
                      this.$swal({
                        icon: "success",
                        title: "Удалено!",
                        text: "Обращение было удалено.",
                        customClass: {
                          confirmButton: "btn btn-success",
                        },
                      });
                      this.$store.dispatch("getDataTable");
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
    hideModal() {
      this.$refs["modal__window"].hide();
    },
    hideProjectModal() {
      this.$refs["project__modal"].hide();
    },

    async changeIconManager(id, manager) {
      this.getDataTable.map((row) => {
        if (row.id === id) {
          this.options_manager_check.filter((item) => {
            if (item.text === manager.value) {
              axios
                .put(" api/projects/" + this.getProject.id + "/claims/" + id, {
                  manager_check: item.value,
                })
                .then(() => {
                  this.$set(this.managerObject, id, manager);
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
        if (id) {
        }
      });
    },
    changeIconClient(id, client) {
      this.getDataTable.map((row) => {
        if (row.id === id) {
          this.options_client_check.filter((item) => {
            if (item.text === client.value) {
              axios
                .put("api/projects/" + this.getProject.id + "/claims/" + id, {
                  client_check: item.value,
                })
                .then(() => {
                  this.$set(this.clientObject, id, client);
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
      });
    },
    async verifyLocalStorage() {
      if (this.LocalStorageProject) {
        await this.$store.commit("SET_PROJECT", this.LocalStorageProject);
        await this.$store.dispatch("getDataTable");
        await this.$store.dispatch("getSourceTable");
        await this.$store.dispatch("getTagsTable");
      }
    },
  },
  computed: {
    getDataTable() {
      this.historyArray = [];
      this.$store.getters.getClaims.forEach((row, index, k) => {
        k = 0;
        const activeManager =
          this.managerIcons.find((m) => m.value === row.manager.check.text) ||
          null;
        this.$set(this.managerObject, row.id, activeManager);
        const activeClient =
          this.clientIcons.find((c) => c.value == row.client.check.text) ||
          null;
        this.$set(this.clientObject, row.id, activeClient);
        for (let i = 0; i < index; i++) {
          if (
            row.phone.original ===
            this.$store.getters.getClaims[i].phone.original
          ) {
            k++;
            if (k < 2) {
              this.historyArray.push(this.$store.getters.getClaims[i].id);
            }
          }
        }
      });
      this.historyArray = [...new Set(this.historyArray)];
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
            if (!!value && !row[key] === value) {
              // если value фильтра истинно (то есть не равно ни null, ни 0) и если то же поле в строке (с тем же ключом что и в фильтре) не содержит значение фильтра...
              flag = false; // ... то флаг = false...
              break; // ... и цикл прерывается
            }
          }
          return flag; // возвращаем флаг, если флаг == false - строка не проходит фильтрацию
        });
        return filteredRows;
      }

      return this.getDataTable;
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
    options_manager_check() {
      return this.$store.getters.options_manager_check;
    },
    options_client_check() {
      return this.$store.getters.options_client_check;
    },
    create_window() {
      return this.$store.getters.get_create_modal_window;
    },
    LocalStorageProject() {
      return JSON.parse(localStorage.getItem("project"));
    },
  },
  created() {
    this.getDataUser();
  },
  mounted() {
    this.verifyLocalStorage();
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
