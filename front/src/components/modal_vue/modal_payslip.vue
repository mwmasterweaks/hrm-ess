<template>
  <div>
    <b-modal
      id="ModalManagePayslip"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title
      ok-only
    >
      <!--Form-------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">
            Manage Payslip for employee ID: {{ data.user.email }}
            <b-button
              type="button"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              v-if="roles.create_payslip"
              v-b-modal="'ModalCreatePayslip'"
            >
              <i class="fas fa-plus-square"></i>
            </b-button>
          </p>
        </div>
        <div class="elClr panel-body">
          <div>
            <b-table
              class="elClr"
              striped
              show-empty
              hover
              outlined
              :fields="fields"
              :items="data.payslip"
              :busy="tblisBusy"
              :tbody-tr-class="tblRowClass"
              head-variant=" elClr"
              thead-class="cursorPointer-th"
              @row-clicked="tblRowClicked"
            >
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>
            </b-table>
          </div>
        </div>
      </div>
      <!--Form-------->
      <template slot="modal-footer" slot-scope="{}"></template>
    </b-modal>

    <b-modal
      id="ModalMyPayslip"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="My Payslip"
    >
      <payslip_table v-bind:payslip="payslip"></payslip_table>
    </b-modal>

    <create_payslip v-bind:data="data"></create_payslip>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import datePicker from "vue-bootstrap-datetimepicker";
import create_payslip from "./modal_create_payslip.vue";
import payslip_table from "../others/payslip_table.vue";

export default {
  props: ["data"],
  components: {
    payslip_table,
    create_payslip,
    "model-list-select": ModelListSelect,
    "date-picker": datePicker
  },
  data() {
    return {
      fields: [
        { key: "pay_period.period", label: "Pay Period", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      item: {},
      payslip: {},
      tblisBusy: false,
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      user: {},
      roles: []
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
  },
  mounted() {
    this.$root.$on("update_payslip_list", item => {
      this.data.payslip = item;
    });
  },
  methods: {
    load() {},
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblRowClicked(item, index, event) {
      console.log(item);
      this.payslip = item;
      this.$bvModal.show("ModalMyPayslip");
    },
    btnAdd() {},
    btnUpdate() {},
    btnDelete() {},
    clearData() {}
  }
};
</script>
<style scoped>
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}
</style>


