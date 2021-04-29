<template>
  <div>
    <b-modal
      id="ModalCreatePayslip"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="Manage Payslip"
    >
      <!-- Period -->
      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Period:</p>
        </div>
        <div class="col-lg-3">
          <model-list-select
            :list="pay_period_list.year"
            v-model="pay_period_select.year"
            option-value="year"
            option-text="year"
            placeholder="Select year"
            @input="pay_period_onchange('year')"
          ></model-list-select>
        </div>
        <div class="col-lg-3" v-if="pay_period_select.year != null">
          <model-list-select
            :list="pay_period_list.month"
            v-model="pay_period_select.month"
            option-value="month"
            option-text="month"
            placeholder="Select month"
            @input="pay_period_onchange('month')"
          ></model-list-select>
        </div>
        <div class="col-lg-3" v-if="pay_period_select.month != null">
          <model-list-select
            :list="pay_period_list.day"
            v-model="pay_period_select.day"
            option-value="id"
            option-text="day"
            placeholder="Select day"
            @input="pay_period_onchange('day')"
          ></model-list-select>
        </div>
      </div>
      <br />
      <br />
      <payslip_table v-bind:payslip="payslip"></payslip_table>
      <br />

      <template slot="modal-footer" slot-scope="{}">
        <b-button size="sm" variant="success" @click="btnSave()">Save</b-button>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";
import payslip_table from "../others/payslip_table.vue";

export default {
  props: ["data"],
  components: {
    payslip_table,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
  },
  data() {
    return {
      fields: ["Gross Pay", "Amount", "Deduction", "AMOUNT"],
      items: [],
      pay_period_list: {
        year: [],
        month: [],
        day: []
      },
      pay_period_select: {
        year: null,
        month: null,
        day: {}
      },
      pay_period_select_add: {},
      payslip: {
        dtr: {}
      },
      roles: []
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      this.$http.post("api/PayPeriod/getYear").then(function(response) {
        this.pay_period_list.year = response.body;
      });
    },
    pay_period_onchange(val) {
      if (val == "year") {
        this.$http
          .get("api/PayPeriod/getMonth/" + this.pay_period_select.year)
          .then(function(response) {
            this.pay_period_list.month = response.body;
            this.pay_period_select.month = null;
            this.pay_period_select.day = null;
          });
      }
      if (val == "month") {
        if (this.pay_period_select.month.toString().length == 1)
          this.pay_period_select.month =
            "0" + this.pay_period_select.month.toString();

        this.$http
          .get(
            "api/PayPeriod/getDay/" +
              this.pay_period_select.year +
              "-" +
              this.pay_period_select.month
          )
          .then(function(response) {
            this.pay_period_list.day = response.body;
            this.pay_period_select.day = {};
          });
      }

      if (val == "day") {
        //this.pay_period_select.day  <--payperiod
        var data = {
          employee: this.data,
          payperiod: this.pay_period_select.day
        };
        console.log(data);
        this.$http
          .post("api/Payslip/generatePayslip", data)
          .then(function(response) {
            this.payslip = response.body;
            console.log(response.body);
          });
      }
    },
    btnSave() {
      this.payslip.employee_id = this.data.id;
      this.payslip.pay_period_id = this.pay_period_select.day.id;
      this.$root.$emit("pageLoading");
      this.$http
        .post("api/Payslip", this.payslip)
        .then(response => {
          this.$root.$emit("pageLoaded");
          this.$root.$emit("update_payslip_list", response.body);
          console.log(response.body);
          swal("Saved!");
          this.$bvModal.hide("ModalCreatePayslip");
        })
        .catch(response => {
          this.$root.$emit("pageLoaded");
          console.log(response.body);
          swal({
            title: "Error",
            text: response.body.message,
            icon: "error",
            dangerMode: true
          });
        });
    }
  }
};
</script>
<style scoped>
.centerText {
  text-align: center;
}

.margin-right-10 {
  margin-right: 10px;
}
table,
th,
td {
  text-align: left;
  padding: 8px;
}
tr:nth-child(even) {
  background-color: #f2f2f2;
}

th {
  background-color: #4caf50;
  color: white;
}

table {
  width: 100%;
  border-collapse: collapse;
}

.row {
  margin-left: -5px;
  margin-right: -5px;
}

.column {
  float: left;
  width: 50%;
  padding: 5px;
}

/* Clearfix (clear floats) */
.row::after {
  content: "";
  clear: both;
  display: table;
}
</style>
