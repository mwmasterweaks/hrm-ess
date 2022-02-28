<template>
  <div>
    <!-- <button

      type="button"

      class="btn btn-success btn-labeled pull-right margin-right-10"
    >Summary</button>-->
    <b-button
      v-b-modal="'modalhrreport'"
      type="button"
      style="margin-top:-17px;"
      v-b-tooltip.hover
      title="Reports"
      class="btn btn-success btn-labeled pull-right margin-right-10"
      v-if="roles.hr"
    >
      <i class="fas fa-paste"></i>
    </b-button>
    <b-modal
      id="modalhrreport"
      size="xl"
      title="Summary Report"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
    >
      <div class="rowFields mx-auto row">
        <div class="col-lg-1">
          <p class="elClr" style="margin-top:9px;">Period:</p>
        </div>
        <div class="col-lg-5">
          <date-picker
            v-if="selectedSummary == 'toplates'"
            v-model="late_period_select"
            placeholder="Select month and year"
            :config="AppliedDateoptions"
            autocomplete="off"
            style="height: 100%"
            @input="on_summary_change"
          ></date-picker>
          <model-list-select
            v-else
            :list="pay_period_list"
            v-model="pay_period_select"
            option-value="id"
            option-text="period"
            placeholder="Select pay period"
            name="pay_period_list"
            v-validate="'required'"
            @input="on_summary_change"
          ></model-list-select>
        </div>
        <div class="col-lg-5">
          <b-form-select
            v-model="selectedSummary"
            :options="summaryOptions"
          ></b-form-select>
        </div>
        <div class="col-lg-1">
          <b-button @click="pay_period_onchange" class="sumReport-btn">
            FILTER
          </b-button>
        </div>
      </div>
      <br />
      <div v-if="activeDiv != 'totals'">
        <b-table
          id="HRsummaryTable"
          class="elClr centerText"
          show-empty
          striped
          hover
          outlined
          :fields="rsFields"
          :items="items"
          :busy="tblisBusy"
          :tbody-tr-class="tblRowClass"
          head-variant="elClr"
          thead-class="cursorPointer-th"
          style="text-align: left"
          @row-clicked="rowDetails"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template slot="table-caption"></template>

          <template #cell(details)="row">
            <b-button
              size="sm"
              @click="row.toggleDetails"
              class="toggle-btn"
              style="padding-top: 0"
            >
              {{ row.detailsShowing ? "Hide" : "Show" }}
            </b-button>
          </template>

          <template #row-details="row">
            <b-card
              v-if="selectedSummary == 'late' || selectedSummary == 'toplates'"
              class="perRow-bcard"
            >
              <b-row class="perRow-brow">
                <b-col><b>Shift Schedule (In)</b></b-col>
                <b-col><b>Login Time</b></b-col>
              </b-row>
              <b-row
                v-for="item in row.item.summary.lates"
                :key="item.sched_in"
                class="perRow-brow"
              >
                <b-col>{{ item.sched_in }}</b-col>
                <b-col>{{ item.time_in }}</b-col>
              </b-row>
            </b-card>
            <b-card v-if="selectedSummary == 'absent'" class="perRow-bcard">
              <b-row
                class="perRow-brow"
                v-if="row.item.summary.absents.length > 0"
              >
                <b-col><b>Work Date</b></b-col>
                <b-col><b>Shift Schedule (In)</b></b-col>
                <b-col><b>Shift Schedule (Out)</b></b-col>
              </b-row>
              <b-row
                v-for="item in row.item.summary.absents"
                :key="item.sched_in"
                class="perRow-brow"
              >
                <b-col>{{ item.work_date }}</b-col>
                <b-col>{{ item.sched_in }}</b-col>
                <b-col>{{ item.sched_out }}</b-col>
              </b-row>
              <b-row
                class="perRow-brow"
                v-if="row.item.summary.missinglogs.length > 0"
              >
                <b-col><b>Work Date</b></b-col>
                <b-col><b>Login Time</b></b-col>
                <b-col><b>Logout Time</b></b-col>
              </b-row>
              <b-row
                v-for="item in row.item.summary.missinglogs"
                :key="item.sched_in"
                class="perRow-brow"
              >
                <b-col>{{ item.work_date }}</b-col>
                <b-col>{{ item.time_in }}</b-col>
                <b-col>{{ item.time_out }}</b-col>
              </b-row>
              <b-row
                class="perRow-brow"
                v-if="row.item.summary.leaves.length > 0"
              >
                <b-col><b>Work Date</b></b-col>
                <b-col><b>Reason</b></b-col>
                <b-col><b>No. of Days</b></b-col>
              </b-row>
              <b-row
                v-for="item in row.item.summary.leaves"
                :key="item.sched_in"
                class="perRow-brow"
              >
                <b-col>{{ item.date_from + " - " + item.date_to }}</b-col>
                <b-col>{{ item.reason }}</b-col>
                <b-col>{{ item.total_days }}</b-col>
              </b-row>
            </b-card>
            <b-card v-if="selectedSummary == 'undertime'" class="perRow-bcard">
              <b-row class="perRow-brow">
                <b-col><b>Shift Schedule (out)</b></b-col>
                <b-col><b>Logout Time</b></b-col>
              </b-row>
              <b-row
                v-for="item in row.item.summary.undertimes"
                :key="item.sched_in"
                class="perRow-brow"
              >
                <b-col>{{ item.sched_out }}</b-col>
                <b-col>{{ item.time_out }}</b-col>
              </b-row>
            </b-card>
            <b-card v-if="selectedSummary == 'overtime'" class="perRow-bcard">
              <b-row class="perRow-brow">
                <b-col><b>Reference No.</b></b-col>
                <b-col><b>Work Date</b></b-col>
                <b-col><b>Shift Schedule</b></b-col>
                <b-col><b>From</b></b-col>
                <b-col><b>To</b></b-col>
                <b-col><b>Break Hours</b></b-col>
                <b-col><b>Total Hours</b></b-col>
                <b-col><b>Reason</b></b-col>
                <b-col><b>Date Filed</b></b-col>
                <b-col><b>Status</b></b-col>
              </b-row>
              <b-row
                v-for="item in row.item.summary.ots"
                :key="item.sched_in"
                class="perRow-brow"
              >
                <b-col>{{ item.reference_no }}</b-col>
                <b-col>{{ item.work_date }}</b-col>
                <b-col>{{ item.shift }}</b-col>
                <b-col>{{ item.time_in }}</b-col>
                <b-col>{{ item.time_out }}</b-col>
                <b-col>{{ item.break_hours }}</b-col>
                <b-col>{{ item.total_hours }}</b-col>
                <b-col>{{ item.reason }}</b-col>
                <b-col>{{ item.date_filed }}</b-col>
                <b-col>{{ item.status }}</b-col>
              </b-row>
            </b-card>
          </template>
        </b-table>
      </div>
      <div v-if="activeDiv == 'totals'">
        <b-table
          id="HRsummaryTable"
          class="elClr centerText"
          show-empty
          striped
          hover
          outlined
          :fields="fields"
          :items="items"
          :busy="tblisBusy"
          :tbody-tr-class="tblRowClass"
          head-variant=" elClr"
          thead-class="cursorPointer-th"
          style="text-align: left"
          @row-clicked="rowDetails"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template slot="table-caption"></template>
        </b-table>
      </div>
      <template v-slot:modal-footer>
        <b-button
          size="sm"
          variant="success"
          @click="fnExcelReport('HRsummaryTable')"
          >Export</b-button
        >
      </template>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import VueRangedatePicker from "vue-rangedate-picker";
import datePicker from "vue-bootstrap-datetimepicker";

export default {
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "rangedate-picker": VueRangedatePicker
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "user.email", label: "ID", sortable: true },
        {
          key: "Name",
          label: "Full Name",
          formatter: (value, key, item) => {
            if (item.middleName == null) item.middleName = "";
            return (
              // item.last_name + ", " + item.first_name + " " + item.middle_name
              item.lastName + ", " + item.firstName + " " + item.middleName
            );
          },
          sortable: true
        },
        { key: "summary.late", label: "Total Late", sortable: true },
        { key: "summary.undertime", label: "Total Undertime", sortable: true },
        {
          key: "summary.no_in_or_out",
          label: "Total No IN or OUT",
          sortable: true
        },
        { key: "summary.no_in_and_out", label: "Total Absent", sortable: true }
      ],
      rsFields: [
        {
          key: "Name",
          label: "Full Name",
          formatter: (value, key, item) => {
            if (item.middleName == null) item.middleName = "";
            return (
              // item.last_name + ", " + item.first_name + " " + item.middle_name
              item.lastName + ", " + item.firstName + " " + item.middleName
            );
          },
          sortable: true
        },
        {
          key: "Class",
          label: "Class",
          formatter: (value, key, item) => {
            return item.dept_name + " / " + item.branch_name;
            // return item.department.name + " / " + item.branch.name;
          },
          sortable: true
        },
        {
          key: "Qty",
          label: "Qty",
          formatter: (value, key, item) => {
            if (
              this.selectedSummary == "late" ||
              this.selectedSummary == "toplates"
            )
              return item.summary.late;
            else if (this.selectedSummary == "absent")
              return item.summary.no_in_and_out;
            else if (this.selectedSummary == "undertime")
              return item.summary.undertime;
            else if (this.selectedSummary == "overtime")
              return item.summary.overtime;
          },
          sortable: true
        },
        {
          key: "Amount",
          label: "Amount",
          formatter: (value, key, item) => {
            if (
              this.selectedSummary == "late" ||
              this.selectedSummary == "toplates"
            )
              return item.summary.late * 0.88;
            else if (this.selectedSummary == "absent")
              return item.summary.no_in_and_out;
            else if (this.selectedSummary == "undertime")
              return item.summary.undertime * 0.66;
            else if (this.selectedSummary == "overtime") return "-";
          },
          sortable: true
        },
        {
          key: "Frequency",
          label: "Frequency",
          formatter: (value, key, item) => {
            if (
              this.selectedSummary == "late" ||
              this.selectedSummary == "toplates"
            )
              return item.summary.lateCount;
            else if (this.selectedSummary == "absent") return "-";
            else if (this.selectedSummary == "undertime")
              return item.summary.utCount;
            else if (this.selectedSummary == "overtime") return "-";
          },
          sortable: true
        },
        { key: "details", label: "Details" }
      ],
      summaryOptions: [
        { value: "totals", text: "All Records" },
        { value: "late", text: "Late" },
        { value: "absent", text: "Absent" },
        { value: "undertime", text: "Undertime" },
        { value: "overtime", text: "Overtime" },
        { value: "toplates", text: "Top Lates" }
      ],
      AppliedDateoptions: {
        format: "YYYY-MM",
        useCurrent: false
      },
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 20,
      pageOptions: [20, 50, 100, 200, 500],
      pay_period_list: [],
      pay_period_select: "",
      late_period_select: "",
      roles: [],
      selectedSummary: "totals",
      activeDiv: "totals"
    };
  },
  created() {
    this.roles = this.$global.getRoles();
    this.load_pay_period();
  },
  methods: {
    load_pay_period() {
      this.$http.get("api/PayPeriod").then(function(response) {
        this.pay_period_list = response.body;
      });
    },
    pay_period_onchange() {
      console.log(this.pay_period_select);
      console.log(this.selectedSummary);
      let selectedPeriod =
        this.selectedSummary == "toplates"
          ? this.late_period_select
          : this.pay_period_select;

      this.tblisBusy = true;
      this.$http
        .put(
          "api/HRSummaryReport/" + selectedPeriod + "/" + this.selectedSummary
        )
        .then(function(response) {
          console.log(response.body);
          if (
            this.selectedSummary == "toplates" ||
            this.selectedSummary == "late"
          ) {
            this.items = response.body.sort((a, b) =>
              a.summary.lateCount > b.summary.lateCount ? -1 : 1
            );
          } else this.items = response.body;

          this.activeDiv = this.selectedSummary;
          this.tblisBusy = false;
          // this.totalRows = this.sched_items.length;
          // this.currentPage = 1;
        });
    },
    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    },
    fnExcelReport(tbl) {
      this.$nextTick(function() {
        setTimeout(
          function() {
            var tab_text = "<table border='2px'><tr>";
            var textRange;
            var j = 0;
            var tab = document.getElementById(tbl); // id of table
            console.log(tab.rows.length);

            for (j = 0; j < tab.rows.length; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // removes input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
              // If Internet Explorer
              txtArea1.document.open("txt/html", "replace");
              txtArea1.document.write(tab_text);
              txtArea1.document.close();
              txtArea1.focus();
              var sa = txtArea1.document.execCommand(
                "SaveAs",
                true,
                "Say Thanks to Sumit.xls"
              );
            } //other browser not tested on IE 11
            else
              var sa = window.open(
                "data:application/vnd.ms-excel," + encodeURIComponent(tab_text)
              );
            return sa;
          }.bind(this),
          1000
        );
      });
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
    },
    rowDetails(item, index, event) {
      console.log(item);
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    on_summary_change() {
      let selectedPeriod =
        this.selectedSummary == "toplates"
          ? this.late_period_select
          : this.pay_period_select;
      console.log(selectedPeriod);
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

.sumReport-btn {
  float: right;
  padding-left: 7px;
  padding-right: 7px;
  margin-left: 5px;
  border-radius: 2px;
  height: 100%;
}
.perRow-bcard {
  font-size: 80%;
}
.perRow-bcard > div {
  width: 100%;
  margin: auto;
}
.perRow-bcard2 > div {
  width: 80%;
}
.perRow-bcard > div > .row {
  padding-left: 15px;
}
.perRow-bcard > .card-body {
  padding: 5px;
}
.perRow-brow {
  padding: 3px;
  margin: 1px;
}
.perRow-brow:hover {
  background: #eaffec;
}
</style>
