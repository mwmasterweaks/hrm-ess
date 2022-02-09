<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Leave Application
          <b-button
            v-b-modal="'ModelApply'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            >Apply</b-button
          >
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input
                    v-model="tblFilter"
                    placeholder="Filter"
                  ></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''"
                      >Clear</b-button
                    >
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select
                  v-model="perPage"
                  :options="pageOptions"
                ></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
          <b-table
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :fields="fields"
            :items="items"
            :filter="tblFilter"
            :busy="tblisBusy"
            :current-page="currentPage"
            :per-page="perPage"
            head-variant=" elClr"
            @filtered="onFiltered"
            :tbody-tr-class="tblRowClass"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>

            <template v-slot:cell(status)="row">
              <button
                class="btn btn-warning"
                v-if="row.item.status == 'Pending'"
                @click="openModalApprovers(row.item)"
              >
                Pending
              </button>
              <button
                class="btn btn-success"
                v-if="row.item.status == 'Approved'"
                @click="openModalApprovers(row.item)"
              >
                Approved
              </button>
              <button
                class="btn btn-danger"
                v-if="row.item.status == 'Disapproved'"
                @click="openModalApprovers(row.item)"
              >
                Disapproved
              </button>
              <span v-if="row.item.status == 'Canceled'">Canceled</span>
            </template>
          </b-table>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;">
            <span class="elClr">{{ totalRows }} item/s found.</span>
          </div>

          <div class="col-md-4" style="background-color:;">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              class="my-0 pull-right"
            ></b-pagination>
          </div>
        </div>
      </div>

      <!-- ModelApply ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModelApply"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Apply Leave"
        @ok="handleOk"
      >
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Leave type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="leave_types"
              v-model="leave_apply.leave_type_id"
              option-value="id"
              option-text="name"
              placeholder="Select Leave type"
              name="leave_types"
              @input="leave_types_onchange"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('leave_types')"
              >Leave type is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Available Balance:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel" v-if="available_balance > 0">
              {{ available_balance }}
            </p>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Total Days:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel" v-if="available_balance > 0">
              {{ total_days }}
            </p>
          </div>
        </div>

        <div id="remainBal" class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remaining Balance:</p>
          </div>
          <div class="col-lg-9">
            <p class="textLabel" v-if="available_balance > 0">
              {{ remain_balance }}
            </p>
          </div>
        </div>
        <!-- Hide if zero balance -->
        <div v-if="available_balance > 0">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date From:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="leave_apply.date_from"
                  :config="Dateoptions"
                  @input="date_onchange('from')"
                  v-b-tooltip.hover
                  title="Date From"
                  placeholder="Date from"
                  autocomplete="off"
                  v-validate="'required'"
                  name="date_from"
                ></date-picker>
              </div>
              <small
                class="text-danger pull-left"
                v-show="errors.has('date_from')"
                >Date from is required.</small
              >
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date To:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="leave_apply.date_to"
                  :config="Dateoptions"
                  @input="date_onchange('to')"
                  v-b-tooltip.hover
                  title="Date to"
                  placeholder="Date to"
                  autocomplete="off"
                  v-validate="'required'"
                  name="date_to"
                ></date-picker>
              </div>
              <small
                class="text-danger pull-left"
                v-show="errors.has('date_to')"
                >Date to is required.</small
              >
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Reason:</p>
            </div>
            <div class="col-lg-9">
              <textarea
                rows="2"
                name="reason"
                class="form-control"
                v-b-tooltip.hover
                title="Input your reason"
                placeholder="Reason"
                v-validate="'required'"
                v-model.trim="leave_apply.reason"
              ></textarea>
              <small class="text-danger pull-left" v-show="errors.has('reason')"
                >Reason is required.</small
              >
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Attachment:</p>
            </div>
            <div class="col-lg-9">
              <b-form-file
                v-model="imageFile"
                @change="imageChange"
                accept="image/*"
                :state="Boolean(leave_apply.attachment)"
                placeholder="Choose a file or drop it here..."
                drop-placeholder="Drop file here..."
              ></b-form-file>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <b-table
              class="elClr"
              striped
              show-empty
              outlined
              :fields="sched_fields"
              :items="sched_items"
              :busy="sched_tblisBusy"
              tbody-tr-class="elClr"
              head-variant=" elClr"
            >
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>
              <template v-slot:cell(shift_sched)="data">
                <span v-html="data.value"></span>
              </template>
              <!-- <template v-slot:cell(halfday)="data">
                <span v-html="data.value"></span>
              </template>
              <template v-slot:cell(type)="data">
                <span v-html="data.value"></span>
              </template>-->
              <template v-slot:cell(halfday)="row">
                <p-check
                  v-if="row.item.is_rest_day == 0"
                  class="p-icon p-curve p-jelly"
                  color="primary"
                  @change="halfday_check(row.item)"
                  v-model="row.item.halfday"
                >
                  <i slot="extra" class="icon fas fa-check"></i>
                </p-check>
              </template>

              <template v-slot:cell(type)="row">
                <model-list-select
                  v-if="row.item.is_rest_day == 0"
                  :list="rest_day_half_type"
                  v-model="row.item.halfday_type"
                  option-value="id"
                  option-text="name"
                  placeholder="Select type"
                  name="row"
                  :isDisabled="!row.item.halfday"
                ></model-list-select>
              </template>

              <template v-slot:cell(action)="row">
                <b-button
                  size="sm"
                  variant="info"
                  @click="removeLeaveinList(row.item)"
                >
                  <i class="fas fa-trash-alt"></i>
                </b-button>
              </template>
            </b-table>
          </div>
        </div>
        <!-- /form -->
        <div slot="modal-footer">
          <b-button
            v-if="available_balance > 0 && remain_balance >= 0"
            size="sm"
            variant="success"
            @click="btnApply()"
            >Submit</b-button
          >
        </div>
      </b-modal>
      <!-- End ModelApply -->

      <!-- ModalApprovers ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalApprovers"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        hide-footer
        size="xl"
        title="Approver"
      >
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input
                    v-model="approv_tblFilter"
                    placeholder="Filter"
                  ></b-form-input>
                  <b-input-group-append>
                    <b-button
                      :disabled="!approv_tblFilter"
                      @click="approv_tblFilter = ''"
                      >Clear</b-button
                    >
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select
                  v-model="approv_perPage"
                  :options="pageOptions"
                ></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>

          <b-table
            class="elClr"
            striped
            show-empty
            hover
            outlined
            :fields="approv_fields"
            :items="approv_items"
            :filter="approv_tblFilter"
            :busy="approv_tblisBusy"
            :current-page="approv_currentPage"
            :per-page="approv_perPage"
            :tbody-tr-class="tblRowClass"
            head-variant=" elClr"
            thead-class="cursorPointer-th"
            @filtered="approv_onFiltered"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>

            <template v-slot:cell(shift_sched)="data">
              <span v-html="data.value"></span>
            </template>
          </b-table>
        </div>

        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;">
            <span class="elClr">{{ approv_totalRows }} item/s found.</span>
          </div>

          <div class="col-md-4" style="background-color:;">
            <b-pagination
              v-model="approv_currentPage"
              :total-rows="approv_totalRows"
              :per-page="approv_perPage"
              class="my-0 pull-right"
            ></b-pagination>
          </div>
        </div>
      </b-modal>
      <!-- End ModalApprovers -->

      <!-- ModalViewDetails ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModalViewDetails"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Details"
      >
        <center>
          <table class="my-table">
            <tr>
              <td class="my-td">Reference no:</td>
              <td class="my-td">{{ item_edit.reference_no }}</td>

              <td class="my-td">Description:</td>
              <td class="my-td">{{ item_edit.leave_type.name }}</td>
            </tr>

            <tr>
              <td class="my-td">From:</td>
              <td class="my-td">{{ item_edit.date_from }}</td>

              <td class="my-td">To:</td>
              <td class="my-td">{{ item_edit.date_to }}</td>
            </tr>

            <tr>
              <td class="my-td">Date Filed:</td>
              <td class="my-td">{{ item_edit.date_filed }}</td>

              <td class="my-td">Total day/s:</td>
              <td class="my-td">{{ item_edit.total_days }}</td>
            </tr>

            <tr>
              <td class="my-td">Reason:</td>
              <td class="my-td" colspan="3">{{ item_edit.reason }}</td>
            </tr>
          </table>

          <div class="rowFields mx-auto row">
            <b-table
              class="elClr"
              striped
              show-empty
              outlined
              :fields="sched_fields_details"
              :items="sched_items_details"
              :busy="sched_tblisBusy_details"
              tbody-tr-class="elClr"
              head-variant=" elClr"
            >
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>
              <template v-slot:cell(shift_sched)="data">
                <span v-html="data.value"></span>
              </template>
              <!-- <template v-slot:cell(halfday)="data">
                <span v-html="data.value"></span>
              </template>
              <template v-slot:cell(halfday_type)="data">
                <span v-html="data.value"></span>
              </template>-->
              <template v-slot:cell(halfday)="row">
                <i class="fas fa-check" v-show="row.item.halfday == 1" />
                <i class="fas fa-times" v-show="row.item.halfday == 0" />
              </template>
              <template v-slot:cell(halfday_type)="row">
                <span v-show="row.item.halfday_type == 1">1st Half</span>
                <span v-show="row.item.halfday_type == 2">2nd Half</span>
              </template>
            </b-table>
          </div>
        </center>

        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-b-modal.modalthumbnail
            title="View Attachment"
          >
            <i class="fas fa-paperclip"></i>
          </b-button>

          <b-button
            size="sm"
            variant="danger"
            title="Cancel Application"
            v-if="item_edit.status == 'Pending'"
            @click="cancelApplication"
            >Cancel</b-button
          >
        </template>
      </b-modal>
      <!-- End ModalViewDetails -->

      <b-modal id="modalthumbnail" size="xl" hide-footer hide-header>
        <b-container fluid class="p-4 bg-dark">
          <center>
            <b-img
              thumbnail
              fluid
              :src="$url_attachments + item_edit.attachment"
              alt="Image 1"
            ></b-img>
          </center>
        </b-container>
      </b-modal>
    </div>
    <div id="to-approve">
      <div>
        Please check the following application waiting for approval on HRMESS
        system.<br /><br />
        <table class="my-table">
          <tr>
            <td colspan="2" class="my-td head-bg">
              LEAVE APPLICATION
            </td>
          </tr>
          <tr>
            <td class="my-td name-bg">
              NAME:
            </td>
            <td class="my-td name-bg">
              {{
                user.first_name.toUpperCase() +
                  " " +
                  user.last_name.toUpperCase()
              }}
            </td>
          </tr>
          <tr>
            <td class="my-td">Reference no:</td>
            <td class="my-td">REFNUM</td>
          </tr>
          <tr>
            <td class="my-td">Description:</td>
            <td class="my-td">LEAVETYPE</td>
          </tr>
          <tr>
            <td class="my-td">From:</td>
            <td class="my-td">{{ leave_apply.date_from }}</td>
          </tr>
          <tr>
            <td class="my-td">To:</td>
            <td class="my-td">{{ leave_apply.date_to }}</td>
          </tr>
          <tr>
            <td class="my-td">Total no. of days:</td>
            <td class="my-td">TOTALDAYS</td>
          </tr>
          <tr>
            <td class="my-td">Date filed:</td>
            <td class="my-td">DATEFILED</td>
          </tr>
          <tr>
            <td class="my-td">Reason:</td>
            <td class="my-td values">
              {{ leave_apply.reason }}
            </td>
          </tr>
        </table>
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
    "p-check": PrettyCheck,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "leave_type.name", label: "Leave type", sortable: true },
        {
          key: "date",
          label: "Date From - To",
          formatter: (value, key, item) => {
            return item.date_from + " - " + item.date_to;
          },
          sortable: true
        },
        { key: "total_days", sortable: true },
        { key: "date_filed", sortable: true },
        { key: "status", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      user: {},
      leave_apply: {
        employee_id: "",
        leave_type_id: "",
        reference_no: "tempnumber123",
        date_from: "",
        date_to: "",
        reason: "",
        attachment: "",
        status: "Pending",
        available_balance: "0"
      },
      item_edit: {
        employee_id: "",
        leave_type_id: "",
        reference_no: "tempnumber123",
        leave_type: {
          name: ""
        },
        date_from: "",
        date_to: "",
        reason: "",
        attachment: "",
        status: "Pending",
        available_balance: "0"
      },
      leave_types: [],
      available_balance: 0,
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      imageFile: null,

      approv_tblisBusy: false,
      approv_fields: [
        {
          key: "Name",
          label: "Name",
          formatter: (value, key, item) => {
            if (item.emp.middle_name == null) item.emp.middle_name = "";
            return (
              item.emp.first_name +
              " " +
              item.emp.middle_name +
              " " +
              item.emp.last_name
            );
          },
          sortable: true
        },
        { key: "level", sortable: true },
        { key: "status", sortable: true },
        { key: "remark", label: "Remark", sortable: true }
      ],
      approv_items: [],
      approv_tblFilter: null,
      approv_totalRows: 0,
      approv_currentPage: 1,
      approv_perPage: 20,
      sched_fields: [
        { key: "work_date", label: "Date" },
        {
          key: "shift_sched",
          label: "Shift Sched",
          formatter: (value, key, item) => {
            if (item.is_rest_day == 1)
              return "<p class='text-danger'>Rest Day</p>";
            else if (item.is_rest_day == 2)
              return "<p class='text-danger'>Leave</p>";
            else return item.shift_sched_in + " - " + item.shift_sched_out;
          }
        },
        { key: "halfday" },
        { key: "type" },
        { key: "action" }
      ],
      sched_items: [],
      sched_tblisBusy: false,
      total_days: 0,
      remain_balance: 0,
      rest_day_half_type: [
        { name: "1st Half", id: 1 },
        { name: "2nd Half", id: 2 }
      ],

      sched_fields_details: [
        { key: "leave_date", label: "Date" },
        { key: "halfday" },
        { key: "halfday_type" }
      ],
      sched_items_details: [],
      sched_tblisBusy_details: false,
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    if (this.$keycloak.isTokenExpired()) {
      this.$root.$emit("logout");
    }
    this.user = this.$global.getUser();
    this.load_leave(this.user.id);
    this.load_leave_types();
  },
  mounted() {},
  updated() {},
  methods: {
    load_leave(id) {
      this.$http.get("api/Leave/" + id).then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    load_leave_types() {
      this.$http.get("api/LeaveType").then(function(response) {
        this.leave_types = response.body;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    approv_onFiltered(filteredItems) {
      this.approv_totalRows = filteredItems.length;
      this.approv_currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      this.$bvModal.show("ModalViewDetails");
      this.item_edit = item;
      this.sched_items_details = item.leave_days;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnApply() {
      this.$validator.validateAll().then(result => {
        if (result) {
          // catch! if employee has no approver - done
          if (this.user.emp_approver.length > 0) {
            var sendTo = [];
            this.user.emp_approver.forEach(item => {
              if (item.level == 1) {
                var emp = item.approver.employee;
                var temp = {
                  email: emp.email1,
                  name: emp.first_name + " " + emp.last_name
                };
                sendTo.push(temp);
              }
            });

            this.leave_apply.user_name =
              this.user.first_name + " " + this.user.last_name;
            this.leave_apply.user_email = this.user.email1;
            this.leave_apply.employee_id = this.user.id;
            this.leave_apply.daysList = this.sched_items;
            this.leave_apply.total_days = this.total_days;
            this.leave_apply.msg = document.getElementById(
              "to-approve"
            ).innerHTML;
            this.leave_apply.sendTo = sendTo;
            this.leave_apply.CCto = [];
            console.log(this.leave_apply);

            this.$http
              .post("api/Leave", this.leave_apply)
              .then(response => {
                console.log(response.body);
                this.available_balance = 0;
                swal("Notification", "Added successfully", "success");
                this.items = response.body;
                this.totalRows = this.items.length;
                this.leave_apply = {
                  employee_id: "",
                  leave_type_id: "",
                  reference_no: "tempnumber123",
                  total_days: "",
                  reason: "",
                  attachment: "",
                  date_filed: "",
                  status: "Pending",
                  approve_level: ""
                };
                this.sched_items = [];
                this.total_days = 0;
                this.remain_balance = 0;

                this.$bvModal.hide("ModelAdd");
              })
              .catch(response => {
                swal({
                  title: "Error",
                  text: response.body.error,
                  icon: "error",
                  dangerMode: true
                }).then(value => {
                  if (value) {
                    this.$refs.name.focus();
                  }
                });
              });
          } else {
            swal(
              "No Approver!",
              "Please contact HR Department to update your approvers.",
              "info"
            );
          }
        }
      });
    },
    leave_types_onchange() {
      this.$http
        .get(
          "api/getBalance/" +
            this.user.id +
            "/" +
            this.leave_apply.leave_type_id
        )
        .then(function(response) {
          this.available_balance = response.body;
          this.leave_apply.available_balance = response.body;
        });
    },
    imageChange(e) {
      var fileReader = new FileReader();
      fileReader.readAsDataURL(e.target.files[0]);

      fileReader.onload = e => {
        this.leave_apply.attachment = e.target.result;
      };
    },
    openModalApprovers(item) {
      this.approv_tblisBusy = true;
      this.$http
        .get("api/getApprover/" + this.user.id + "/lv/" + item.id)
        .then(function(response) {
          this.approv_items = response.body;
          this.approv_tblisBusy = false;
          this.approv_totalRows = this.approv_items.length;
          this.approv_currentPage = 1;
        });

      this.$bvModal.show("ModalApprovers");
    },
    cancelApplication() {
      this.item_edit.user_id = this.user.id;
      this.item_edit.user_name =
        this.user.first_name + " " + this.user.last_name;
      console.log(this.item_edit);
      swal({
        title: "Notification",
        text: "Do you really want to cancel this application?",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(approve => {
        if (approve) {
          this.tblisBusy = true;
          this.$http
            .post("api/Leave/cancelApp", this.item_edit)
            .then(response => {
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              swal("Application Canceled");
              this.$bvModal.hide("ModalViewDetails");
            });
        }
      });
    },
    date_onchange(caller) {
      //console.log(this.leave_apply.date_from);
      if (this.leave_apply.date_from != "" && this.leave_apply.date_to != "") {
        this.$http
          .get(
            "api/getDTRinRange/" +
              this.leave_apply.date_from +
              "/" +
              this.leave_apply.date_to +
              "/" +
              this.user.id
          )
          .then(function(response) {
            this.sched_items = response.body.item;
            this.total_days = response.body.total_days;
            this.remain_balance = this.available_balance - this.total_days;
            if (this.remain_balance < 0) {
              document.getElementById("remainBal").style.backgroundColor =
                "red";
              document.getElementById("remainBal").style.color = "white";
            } else {
              document.getElementById("remainBal").style.backgroundColor = "";
              document.getElementById("remainBal").style.color = "black";
            }
            if (this.total_days == 0) {
              swal("No posted shift schedule/s for the following date/s");
            }
          });
      }
    },
    halfday_check(item) {
      if (!item.halfday) {
        item.halfday_type = 0;
        this.total_days += 0.5;
      } else {
        this.total_days -= 0.5;
      }

      this.remain_balance = this.available_balance - this.total_days;

      if (this.remain_balance < 0) {
        document.getElementById("remainBal").style.backgroundColor = "red";
        document.getElementById("remainBal").style.color = "white";
      } else {
        document.getElementById("remainBal").style.backgroundColor = "";
        document.getElementById("remainBal").style.color = "black";
      }
    },
    removeLeaveinList(item) {
      var removeIndex = this.sched_items
        .map(function(ii) {
          return ii.id;
        })
        .indexOf(item.id);

      if (item.is_rest_day == 0) {
        if (item.halfday) this.total_days -= 0.5;
        else this.total_days -= 1;
      }
      this.remain_balance = this.available_balance - this.total_days;
      if (this.remain_balance < 0) {
        document.getElementById("remainBal").style.backgroundColor = "red";
        document.getElementById("remainBal").style.color = "white";
      } else {
        document.getElementById("remainBal").style.backgroundColor = "";
        document.getElementById("remainBal").style.color = "black";
      }

      this.sched_items.splice(removeIndex, 1);
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
.modal-content,
modal-header {
  border: 1px solid red;
}
</style>
