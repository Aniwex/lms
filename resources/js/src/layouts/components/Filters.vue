<template>
  <div class="d-flex justify-content-end filter__content">
    <b-form-group>
      <b-dropdown class="drop__down" variant="primary" right no-caret>
        <template #button-content>
          <filter-icon size="1x" class="custom-class"></filter-icon
        ></template>
        <div v-if="this.$route.path === '/Home'">
          <b-dropdown-form>
            <div class="form-group">
              <b-button
                class="reset__filters"
                v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                variant="flat-secondary"
                @click="resetFilters"
              >
                Сбросить фильтры
              </b-button>
              <p>Тэги</p>
              <!-- <b-dropdown-item class="dropitem__close" @click="closeMe()"
                >X</b-dropdown-item
              > -->
              <multiselect
                v-model="selected.tag"
                :options="options_tags"
                selectedLabel="Выбрано"
                :multiple="true"
                class="multiselect-input multiselect-width"
                label="name"
                track-by="name"
                placeholder="Выберите тэг"
                :showLabels="false"
                @input="selectTag"
              >
              </multiselect>
              <b-dropdown-divider />
              <p>Источник</p>
              <multiselect
                v-model="selected.source"
                :options="options_source"
                selectedLabel="Выбрано"
                class="multiselect-input multiselect-width"
                label="name"
                track-by="name"
                placeholder="Выберите источник"
                :showLabels="false"
                @input="selectSource"
              >
              </multiselect>

              <b-dropdown-divider />
              <p>Дата и время</p>
              <flat-pickr
                v-model="selected.date"
                class="form-control row__date-pickr db__tc"
                placeholder="Выберите дату и время"
                :config="{ datetimeFormat: 'Y-m-d H:i:s' }"
                @input="selectedDate"
              />
              <b-dropdown-divider />
              <p>Продолжительность Звонка</p>
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
                  class="input__number form-control multiselect-width"
                  v-model="selected.duration"
                  type="number"
                  @input="selectedDuration"
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
              <div class="mt-2">Значение: {{ selected.value_range }}</div>
              <b-dropdown-divider />
              <p>Пользователь</p>
              <b-form-input
                type="text"
                placeholder="+7 (999) 999-99-99"
                v-model="selected.user"
                v-mask="'+7 (###) ###-##-##'"
                @input="selectedUser"
              />
              <b-dropdown-divider />
              <p>Целевой по мнению менеджера</p>
              <multiselect
                v-model="selected.manager"
                :options="options_manager"
                selectedLabel="Выбрано"
                class="multiselect-input multiselect-width"
                label="text"
                track-by="text"
                placeholder="Выберите источник"
                :showLabels="false"
                @input="selectManager"
              >
              </multiselect>
              <b-dropdown-divider />
              <p>Целевой по мнению клиента</p>
              <multiselect
                v-model="selected.client"
                :options="options_client"
                selectedLabel="Выбрано"
                class="multiselect-input multiselect-width"
                label="text"
                track-by="text"
                placeholder="Выберите источник"
                :showLabels="false"
                @input="selectClient"
              >
              </multiselect>
              <b-dropdown-divider />
              <p>Группировка по пользователю</p>
              <b-form-checkbox
                @change="changeCheckBox(checkboxUser)"
                v-model="checkboxUser"
                plain
              >
                Применить
              </b-form-checkbox>
              <b-dropdown-divider />
            </div>
          </b-dropdown-form>
        </div>
      </b-dropdown>
    </b-form-group>
  </div>
</template>

<script>
import {
  BFormGroup,
  BFormInput,
  BFormSelect,
  BDropdown,
  BDropdownItem,
  BDropdownDivider,
  BDropdownGroup,
  BDropdownForm,
  BFormCheckbox,
  BButton,
  BFormSpinbutton,
} from "bootstrap-vue";
import { FilterIcon, Trash2Icon, PlusIcon, MinusIcon } from "vue-feather-icons";
import flatPickr from "vue-flatpickr-component";
import Ripple from "vue-ripple-directive";
import axios from "axios";
export default {
  components: {
    PlusIcon,
    MinusIcon,
    Trash2Icon,
    BFormSpinbutton,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BDropdown,
    BDropdownItem,
    BDropdownGroup,
    BDropdownForm,
    BFormCheckbox,
    BDropdownDivider,
    BButton,
    flatPickr,
    FilterIcon,
  },
  directives: {
    Ripple,
    // "b-tooltip": VBTooltip,
  },
  props: [
    "options_source",
    "options_tags",
    "options_manager",
    "options_client",
    "getDataTable",
    "rowSelection",
    "role_id",
    "project",
  ],
  data() {
    return {
      selected: {
        tag: [],
        source: null,
        date: null,
        user: null,
        manager: null,
        client: null,
        duration: 0,
      },
      sortedFilter: [],
      checkboxUser: false,
      arrayCheckboxUser: [],
    };
  },
  methods: {
    quantity_minus() {
      if (this.selected.duration >= 1) {
        this.selected.duration--;
      }
      this.selectedDuration();
    },
    quantity_plus() {
      this.selected.duration++;
      this.selectedDuration();
    },
    selectTag() {
      this.sortedFilter = [];
      let tempTags = [];
      this.selected.tag.filter((item) => {
        tempTags.push(item.name);
      });
      this.getDataTable.filter((row) => {
        row.tags.filter((item, i) => {
          tempTags.filter((tag) => {
            if (item.name === tag) {
              this.sortedFilter.push(row);
            }
          });
        });
      });
      this.sortedFilter = [...new Set(this.sortedFilter)];
      this.$emit("sortedFilter", this.sortedFilter);
    },
    selectSource() {
      this.sortedFilter = [];
      let tempSource = this.selected.source;
      if (tempSource) {
        this.getDataTable.filter((row) => {
          if (row.source.name === tempSource.name) {
            this.sortedFilter.push(row);
          }
        });
      }
      this.$emit("sortedFilter", this.sortedFilter);
    },
    selectedDate() {
      let tempDate;
      this.sortedFilter = [];
      this.getDataTable.filter((item) => {
        tempDate = item.datetime.substr(0, 10);
        if (tempDate === this.selected.date) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    selectedDuration() {
      this.sortedFilter = [];
      let tempDuration = Number(this.selected.duration);
      this.getDataTable.map((item, call) => {
        call = item.duration.original;
        call = Number(call);
        console.log(typeof call);
        if (call === tempDuration) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    selectedUser() {
      this.sortedFilter = [];
      this.getDataTable.filter((item) => {
        if (item.phone.formatted.includes(this.selected.user)) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    selectManager() {
      this.sortedFilter = [];
      if (this.selected.manager) {
        this.getDataTable.filter((item) => {
          if (item.manager.check.value === this.selected.manager.value) {
            this.sortedFilter.push(item);
          }
        });
      }
      this.$emit("sortedFilter", this.sortedFilter);
    },
    selectClient() {
      this.sortedFilter = [];
      if (this.selected.client) {
        this.getDataTable.filter((item) => {
          if (item.client.check.value === this.selected.client.value) {
            this.sortedFilter.push(item);
          }
        });
      }
      this.$emit("sortedFilter", this.sortedFilter);
    },
    resetFilters() {
      this.selected.tag = null;
      this.selected.source = null;
      this.selected.date = null;
      this.selected.manager = null;
      this.selected.client = null;
      this.selected.value_range = 0;
      this.selected.user = null;
      this.sortedFilter = [];
      this.arrayCheckboxUser = [];
      this.checkboxUser = false;
      this.$emit("sortedFilter", this.sortedFilter);
      this.$emit("arrayCheckboxUser", this.arrayCheckboxUser);
      this.$emit("selected", this.selected);
    },

    changeCheckBox(chek) {
      if (chek) {
        let arr = [];
        this.arrayCheckboxUser = [];
        this.getDataTable.filter((item) => {
          arr.push(item.phone.formatted);
        });
        arr = [...new Set(arr)];
        let j = 0;
        for (let i in this.getDataTable) {
          if (this.getDataTable[i].phone.formatted.includes(arr[j])) {
            this.arrayCheckboxUser.push(this.getDataTable[i]);
            j++;
          }
        }
        this.$emit("arrayCheckboxUser", this.arrayCheckboxUser);
      } else {
        this.$emit("arrayCheckboxUser", this.getDataTable);
      }
    },
  },
  computed: {},
  updated() {
    this.$emit("selected", this.selected);
  },
  created() {},
};
</script>

<style>
</style>