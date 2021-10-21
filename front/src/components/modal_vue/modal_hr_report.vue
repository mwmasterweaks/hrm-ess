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
          <model-list-select
            :list="pay_period_list"
            v-model="pay_period_select"
            option-value="id"
            option-text="period"
            placeholder="Select pay period"
            name="pay_period_list"
            v-validate="'required'"
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
          tbody-tr-class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="rsFields"
          :items="items"
          :busy="tblisBusy"
          head-variant=" elClr"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
          <template slot="table-caption"></template>
        </b-table>
      </div>
      <div v-if="activeDiv == 'totals'">
        <b-table
          id="HRsummaryTable"
          class="elClr centerText"
          tbody-tr-class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="fields"
          :items="items"
          :busy="tblisBusy"
          head-variant=" elClr"
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

export default {
  components: {
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
            if (item.middle_name == null) item.middle_name = "";
            return (
              item.first_name + " " + item.middle_name + " " + item.last_name
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
            if (item.middle_name == null) item.middle_name = "";
            return (
              item.last_name + ", " + item.first_name + " " + item.middle_name
            );
          },
          sortable: true
        },
        {
          key: "Class",
          label: "Class",
          formatter: (value, key, item) => {
            return item.department.name + " / " + item.branch.name;
          },
          sortable: true
        },
        {
          key: "Qty",
          label: "Qty",
          formatter: (value, key, item) => {
            if (this.selectedSummary == "late") return item.summary.late;
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
            if (this.selectedSummary == "late") return item.summary.late * 0.88;
            else if (this.selectedSummary == "absent")
              return item.summary.no_in_and_out;
            else if (this.selectedSummary == "undertime")
              return item.summary.undertime * 0.66;
            else if (this.selectedSummary == "overtime") return "-";
          },
          sortable: true
        }
      ],
      summaryOptions: [
        { value: "totals", text: "All Records" },
        { value: "late", text: "Late" },
        { value: "absent", text: "Absent" },
        { value: "undertime", text: "Undertime" },
        { value: "overtime", text: "Overtime" }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 1,
      perPage: 20,
      pageOptions: [20, 50, 100, 200, 500],
      pay_period_list: [],
      pay_period_select: "",
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

      this.tblisBusy = true;
      this.$http
        .put(
          "api/HRSummaryReport/" +
            this.pay_period_select +
            "/" +
            this.selectedSummary
        )
        .then(function(response) {
          console.log(response.body);
          this.items = response.body;
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
</style>
