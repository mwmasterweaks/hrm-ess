<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">Add Schedule</h6>
      </div>

      <div class="elClr panel-body">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Client:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="clients"
              v-model="clientSelected"
              option-value="id"
              :custom-text="getClientDesc"
              placeholder="Select/Search a client (name - location - branch)"
              name="client_id"
              v-validate="'required'"
              @input="onChangeClientId"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('client_id')"
            >Please select / search a client name or location.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Sales In-Charge:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="sales"
              v-model="client_details.sales_id"
              name="sales_id"
              option-value="id"
              option-text="name"
              placeholder="Select/Search a sales in-charge..."
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('sales_id')"
            >Please select / search sellers name.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Cable Category:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="cableCategoryOption"
              v-model="client_details.cable_category"
              option-value="id"
              option-text="name"
              placeholder="Select Cable Category (optional)"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">FOC Length(meter):</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              class="form-control"
              v-b-tooltip.hover
              title="FOC Length in meter"
              placeholder="Length in meter(optional)"
              v-model.trim="client_details.foc_length"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">FOC Layout:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="focLayoutOption"
              v-model="client_details.foc_layout"
              option-value="id"
              option-text="name"
              placeholder="Select FOC Layout status (optional)"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">OTC:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="otcOption"
              v-model="client_details.otc"
              option-value="id"
              option-text="name"
              placeholder="Select OTC (optional)"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Mapping Status:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_details.mapping_status"
            >
              <i class="fas fa-check" v-show="client_details.mapping_status" />
              <i class="fas fa-times" v-show="!client_details.mapping_status" />
            </p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Modem Status:</p>
          </div>
          <div class="col-lg-9">
            <p-check
              class="textLabel p-switch p-fill"
              color="success"
              v-model="client_details.modem_status"
            >
              <i class="fas fa-check" v-show="client_details.modem_status" />
              <i class="fas fa-times" v-show="!client_details.modem_status" />
            </p-check>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Applied date:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              
              <date-picker
                v-model="client_details.applied_date"
                :config="AppliedDateoptions"
                autocomplete="off"
              ></date-picker>

            </div>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="heading-elements">
          <button
            type="button"
            class="btn btn-success btn-labeled pull-right"
            v-on:click="addClientDetails"
          >ADD</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck
  },
  data() {
    return {
      client_details: {
        client_id: "",
        branch_id: "",
        sales_id: "",
        otc: "",
        mapping_status: false,
        cable_category: "",
        foc_length: "",
        foc_layout: "",
        modem_status: false,
        applied_date: "",
        aging: null
      },
      clients: [],
      clientSelected: {
        id: "",
        branch_id: ""
      },
      sales: [],
      focLayoutOption: [
        { id: "Done", name: "Done" },
        { id: "Done outside only", name: "Done outside only" },
        { id: "Done inside only", name: "Done inside only" },
        { id: "Pending", name: "Pending" }
      ],
      otcOption: [
        { id: "Paid", name: "Paid" },
        { id: "Promo", name: "Promo" },
        { id: "Billing", name: "Billing" },
        { id: "Waived", name: "Waived" },
        { id: "Waiting for C&C advisory", name: "Waiting for C&C advisory" },
        { id: "NTP", name: "NTP" }
      ],
      cableCategoryOption: [
        { id: "Drop Fiber", name: "Drop Fiber" },
        { id: "Hard Fiber", name: "Hard Fiber" },
        { id: "UTP", name: "Unshielded twisted pair (UTP)" }
      ],
      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      user: []
    };
  },
  beforeCreate() {},
  created() {
    this.clients = this.$global.getClients();
    this.sales = this.$global.getSales();
    this.user = this.$global.getUser();
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";
        }, 100);
      });
    },
    getClientDesc(client) {
      return `${client.name} - ${client.location} - ${client.branch1}`;
    },
    addClientDetails() {
      this.$validator.validateAll().then(result => {
        if (result) {
          if (
            this.client_details.otc == "Paid" ||
            this.client_details.otc == "Promo" ||
            this.client_details.otc == "Billing" ||
            this.client_details.otc == "Waived" ||
            this.client_details.otc == "NTP"
          ) {
            this.client_details.aging = this.formatDate(new Date());
          }
          //this.client_details.branch_id = this.user.branch_id;
          this.$http
            .post("api/ClientDetail", this.client_details)
            .then(response => {
              swal("Schedule", "Added successfully", "success");
              this.client_details.client_id = "";
              this.client_details.sales_id = "";
              this.client_details.otc = "";
              this.client_details.mapping_status = false;
              this.client_details.cable_category = "";
              this.client_details.foc_length = "";
              this.client_details.foc_layout = "";
              this.client_details.modem_status = false;
              this.client_details.applied_date = "";
              this.client_details.aging = null;
              this.$global.setSchedule(Object.values(response.body)[1]);
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                }
              });
            });
        }
      });
    },
    onChangeClientId() {
      this.client_details.client_id = this.clientSelected.id;
      this.client_details.branch_id = this.user.branch_id;
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
    formatDateDefualt(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("/");
    }
  }
};
</script>
<style scoped>
.textLabel {
  margin-top: 9px;
  font-size: 15px;
}
.rowFields {
  margin-top: 15px;
}
</style>
