<template>
  <div>
    <b-modal
      id="modalEmployeeFilter"
      size="lg"
      title="Multiple Filter"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <!-- branch -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-fill" color="success" v-model="cbFilter.branch"></p-check>Branch
        <div class="rowFields mx-auto row" v-if="cbFilter.branch">
          <div class="col-lg-3">
            <p class="textLabel">Select Branch:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="cbFilter.data.branch_id"
              option-value="id"
              option-text="name"
            ></model-list-select>
          </div>
        </div>
      </div>

      <!-- group -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-fill" color="success" v-model="cbFilter.group"></p-check>Group
        <div class="rowFields mx-auto row" v-if="cbFilter.group">
          <div class="col-lg-3">
            <p class="textLabel">Select Group:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="groups"
              v-model="cbFilter.data.group_id"
              option-value="id"
              option-text="name"
            ></model-list-select>
          </div>
        </div>
      </div>

      <!-- position -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-fill" color="success" v-model="cbFilter.position"></p-check>Position
        <div class="rowFields mx-auto row" v-if="cbFilter.position">
          <div class="col-lg-3">
            <p class="textLabel">Select Position:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="positions"
              v-model="cbFilter.data.position_id"
              option-value="id"
              option-text="name"
            ></model-list-select>
          </div>
        </div>
      </div>

      <!-- department -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-fill"
          color="success"
          v-model="cbFilter.department"
        ></p-check>Department
        <div class="rowFields mx-auto row" v-if="cbFilter.department">
          <div class="col-lg-3">
            <p class="textLabel">Select Department:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="departments"
              v-model="cbFilter.data.department_id"
              option-value="id"
              option-text="name"
            ></model-list-select>
          </div>
        </div>
      </div>

      <!-- rate -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-fill" color="success" v-model="cbFilter.rate"></p-check>Rate
        <div class="rowFields mx-auto row" v-if="cbFilter.rate">
          <div class="col-lg-3">
            <p class="textLabel">Select Rate:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="rates"
              v-model="cbFilter.data.rate_id"
              option-value="id"
              option-text="name"
            ></model-list-select>
          </div>
        </div>
      </div>

      <!-- employment_status -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-fill"
          color="success"
          v-model="cbFilter.employment_status"
        ></p-check>Employment Status
        <div class="rowFields mx-auto row" v-if="cbFilter.employment_status">
          <div class="col-lg-3">
            <p class="textLabel">Type Employment Status:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" v-model.trim="cbFilter.data.employment_status" />
          </div>
        </div>
      </div>

      <!-- first_name -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-fill"
          color="success"
          v-model="cbFilter.first_name"
        ></p-check>First Name
        <div class="rowFields mx-auto row" v-if="cbFilter.first_name">
          <div class="col-lg-3">
            <p class="textLabel">Type First Name:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" v-model.trim="cbFilter.data.first_name" />
          </div>
        </div>
      </div>

      <!-- middle_name -->
      <div class="cont-wrap">
        <p-check
          class="checkboxStyle p-switch p-fill"
          color="success"
          v-model="cbFilter.middle_name"
        ></p-check>Last Name
        <div class="rowFields mx-auto row" v-if="cbFilter.middle_name">
          <div class="col-lg-3">
            <p class="textLabel">Type Middle Name:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" v-model.trim="cbFilter.data.middle_name" />
          </div>
        </div>
      </div>

      <!-- last_name -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-fill" color="success" v-model="cbFilter.last_name"></p-check>Middle Name
        <div class="rowFields mx-auto row" v-if="cbFilter.last_name">
          <div class="col-lg-3">
            <p class="textLabel">Type Last Name:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" v-model.trim="cbFilter.data.last_name" />
          </div>
        </div>
      </div>

      <!-- gender -->
      <div class="cont-wrap">
        <p-check class="checkboxStyle p-switch p-fill" color="success" v-model="cbFilter.gender"></p-check>Gender
        <div class="rowFields mx-auto row" v-if="cbFilter.gender">
          <div class="col-lg-3">
            <p class="textLabel">Type Gender:</p>
          </div>
          <div class="col-lg-9">
            <input type="text" class="form-control" v-model.trim="cbFilter.data.gender" />
          </div>
        </div>
      </div>
      <!-- //
      //-->
      <div slot="modal-footer" slot-scope="{  }">
        <button class="btn btn-success btn-labeled" @click="filter_ok">ok</button>
      </div>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";

import VueRangedatePicker from "vue-rangedate-picker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker,
    "p-check": PrettyCheck
  },
  data() {
    return {
      cbFilter: {
        group: false,
        rate: false,
        position: false,
        branch: false,
        department: false,
        employment_status: false,
        first_name: false,
        last_name: false,
        middle_name: false,
        gender: false,
        data: {
          group_id: 0,
          rate_id: 0,
          position_id: 0,
          branch_id: 0,
          department_id: 0,
          employment_status: "",
          first_name: "",
          last_name: "",
          middle_name: "",
          gender: ""
        }
      },
      employment_statuses: [
        {
          name: "Trainee"
        },
        {
          name: "Provisionary"
        },
        {
          name: "Regular"
        },
        {
          name: "Resigned"
        },
        {
          name: "Terminated"
        }
      ],
      groups: [],
      positions: [],
      departments: [],
      rates: [],
      branches: [],
      user: [],
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.groups = this.$global.getGroup();
    this.positions = this.$global.getPosition();
    this.branches = this.$global.getBranch();
    this.departments = this.$global.getDepartment();
    this.load_rates();
  },
  methods: {
    load() {},
    load_rates() {
      this.$http.get("api/Rate").then(function(response) {
        this.rates = response.body;
      });
    },
    filter_ok() {
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/user/multipleFilter", this.cbFilter)
        .then(response => {
          this.$root.$emit("pageLoaded");
          console.log(response.body);
          this.$root.$emit("update_item", response.body);
          this.$bvModal.hide("modalEmployeeFilter");
        })
        .catch(response => {
          swal({
            title: "Error",
            text: response.body.error,
            icon: "error",
            dangerMode: true
          });
          this.$root.$emit("pageLoaded");
        });
    },
    select_date_change(txt, event) {
      if (txt == "from") {
        this.cbFilter.data.installation_date.from = this.formatDateMDY(event);
      } else if (txt == "to") {
        this.cbFilter.data.installation_date.to = this.formatDateMDY(event);
      } else if (txt == "foc_schedule") {
        this.cbFilter.data.foc_schedule_date = this.formatDateMDY(event);
      } else if (txt == "agingfrom") {
        this.cbFilter.data.aging.from = this.formatDateMDY(event);
      } else if (txt == "agingto") {
        this.cbFilter.data.aging.to = this.formatDateMDY(event);
      } else if (txt == "date_activated") {
        this.cbFilter.data.date_activated = this.formatDateMDY(event);
      } else if (txt == "created_at") {
        this.cbFilter.data.created_at = this.formatDateMDY(event);
      }
    },
    formatDateMDY(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;
      var mstring = [
        "Jan.",
        "Feb.",
        "Mar.",
        "Apr.",
        "May",
        "Jun.",
        "Jul.",
        "Aug.",
        "Sept.",
        "Oct.",
        "Nov.",
        "Dec."
      ];
      return [mstring[month - 1], day, year].join(" ");
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}
.cont-wrap {
  margin-bottom: 10px;
}
</style>


