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
              <b-form-select
                class="db__tc"
                v-model="selected.tag"
                :options="options_tags"
                @change="sortTag(selected.tag)"
                :state="selected.tag === null ? false : true"
              >
              </b-form-select>
              <b-dropdown-divider />
              <p>Источник</p>
              <b-form-select
                class="db__tc"
                v-model="selected.source"
                :options="options_source"
                :state="selected.source === null ? false : true"
                @change="sortSource(selected.source)"
              >
              </b-form-select>
              <b-dropdown-divider />
              <p>Дата и время</p>
              <flat-pickr
                v-model="selected.date"
                class="form-control row__date-pickr db__tc"
                placeholder="Выберите дату и время"
                :config="{ dateFormat: 'd.m.Y' }"
                @input="datePicker(selected.date)"
                :class="{
                  nonActiveDatePicr: !selected.date,
                  activeDatePicr: selected.date,
                }"
              />
              <b-dropdown-divider />
              <p>Продолжительность Звонка</p>
              <b-form-spinbutton
                class="form__spinbutton"
                v-model="selected.value_range"
                min="0"
                max="1000"
                step="10"
                :state="selected.value_range === 0 ? false : true"
                @change="valueRange(selected.value_range)"
              />
              <div class="mt-2">Значение: {{ selected.value_range }}</div>
              <b-dropdown-divider />
              <p>Пользователь</p>
              <b-form-input
                type="text"
                placeholder="+7 (999) 999-99-99"
                v-model="selected.user"
                v-mask="'+7 (###) ###-##-##'"
                @input="sortedUser(selected.user)"
                :state="
                  selected.user === '' || selected.user === null ? false : true
                "
              />
              <b-dropdown-divider />
              <p>Целевой по мнению менеджера</p>
              <b-form-select
                class="db__tc"
                v-model="selected.manager"
                :options="options_manager"
                @change="sortManager(selected.manager)"
                :state="selected.manager === null ? false : true"
              >
              </b-form-select>
              <b-dropdown-divider />
              <p>Целевой по мнению клиента</p>
              <b-form-select
                class="db__tc"
                v-model="selected.client"
                :options="options_client"
                @change="sortClient(selected.client)"
                :state="selected.client === null ? false : true"
              >
              </b-form-select>
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

    <div
      v-if="rowSelection.length && role_id === 1"
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
import { FilterIcon, Trash2Icon } from "vue-feather-icons";
import flatPickr from "vue-flatpickr-component";
import Ripple from "vue-ripple-directive";
import axios from "axios";
export default {
  components: {
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
  props: ["options_source", "rows", "rowSelection", "role_id"],
  data() {
    return {
      selected: {
        tag: null,
        source: null,
        date: null,
        user: null,
        manager: null,
        client: null,
        value_range: 0,
      },
      options_tags: [
        { value: null, text: "—" },
        { value: "балкон", text: "балкон" },
        { value: "окна", text: "окна" },
      ],
      options_manager: [
        { value: null, text: "—" },
        { value: "целевой", text: "целевой" },
        { value: "не целевой", text: "не целевой" },
        { value: "не установленный", text: "не установленный" },
      ],
      options_client: [
        { value: null, text: "—" },
        { value: "целевой", text: "целевой" },
        { value: "не целевой", text: "не целевой" },
        { value: "не проверенный", text: "не проверенный" },
      ],
      sortedFilter: [],
      checkboxUser: false,
      arrayCheckboxUser: [],
    };
  },
  methods: {
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
      this.$emit("arrayCheckboxUser", this.arrayCheckboxUser);
      this.$emit("sortedFilter", this.sortedFilter);
      this.$emit("selected", this.selected);
    },
    sortTag(tag) {
      this.sortedFilter = [];
      this.rows.filter((item) => {
        if (item.tag === tag) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    sortSource(source) {
      this.sortedFilter = [];
      this.rows.map((item) => {
        if (item.source === source) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    datePicker(date) {
      let d;
      this.sortedFilter = [];
      this.rows.map((item) => {
        d = item.date.substr(0, 10);
        if (d === date) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    valueRange(value) {
      this.sortedFilter = [];
      this.rows.map((item, call) => {
        call = item.call.toString().replace(/[^0-9]/g, "");
        call = Number(call);

        if (call === value) {
          this.sortedFilter.push(item);
        }
      });

      this.$emit("sortedFilter", this.sortedFilter);
    },
    sortManager(manager) {
      this.sortedFilter = [];
      this.rows.filter((item) => {
        if (item.manager === manager) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    sortClient(client) {
      this.sortedFilter = [];
      this.rows.filter((item) => {
        if (item.client === client) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    sortedUser(user) {
      this.sortedFilter = [];
      this.rows.filter((item) => {
        if (item.user.includes(user)) {
          this.sortedFilter.push(item);
        }
      });
      this.$emit("sortedFilter", this.sortedFilter);
    },
    deleteSelected() {
      if (this.rows.length) {
        this.rowSelection.filter((item) => {
          return this.rows.map((index, i) => {
            if (item.id === index.id) {
              axios.delete("/api/data/" + item.id);
              this.rows.splice(i, 1);
            }
          });
        });
      }
    },
    changeCheckBox(chek) {
      if (chek) {
        let arr = [];
        this.arrayCheckboxUser = [];
        this.rows.filter((item) => {
          arr.push(item.user);
        });
        arr = [...new Set(arr)];
        let j = 0;
        for (let i in this.rows) {
          if (this.rows[i].user.includes(arr[j])) {
            this.arrayCheckboxUser.push(this.rows[i]);
            j++;
          }
        }
        this.$emit("arrayCheckboxUser", this.arrayCheckboxUser);
      } else {
        this.$emit("arrayCheckboxUser", this.rows);
      }
    },
  },
  computed: {},
  updated() {
    this.$emit("selected", this.selected);
  },
};
</script>

<style>
</style>