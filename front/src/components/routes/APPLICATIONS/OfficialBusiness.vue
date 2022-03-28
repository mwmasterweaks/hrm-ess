<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Official Business Application
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
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
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
        @ok="handleOk"
      >
        <div slot="modal-header" class="w-100">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <h5>Apply Official Business</h5>
            </div>
            <div class="col-lg-3">
              <p-check
                class="p-icon p-curve p-jelly"
                color="primary"
                v-model="apply.multiple_apply"
              >
                <i slot="extra" class="icon fas fa-check"></i> </p-check
              >Multiple Dates
            </div>
          </div>
        </div>

        <!-- form -->
        <div v-if="apply.multiple_apply">
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Date From:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="apply.date_from"
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
                  v-model="apply.date_to"
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
        </div>
        <div v-else>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Work Date:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="apply.work_date"
                  :config="Dateoptions"
                  v-b-tooltip.hover
                  title="Work date"
                  placeholder="Work date"
                  autocomplete="off"
                  @input="work_date_onchange"
                  v-validate="'required'"
                  name="work_date"
                ></date-picker>
              </div>
              <small
                class="text-danger pull-left"
                v-show="errors.has('work_date')"
                >Work date is required.</small
              >
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Shift:</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ apply.shift }}</p>
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Time-In:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="apply.time_in"
                  :config="DateTimeOptions"
                  v-b-tooltip.hover
                  title="Time-In"
                  placeholder="Time-In"
                  autocomplete="off"
                  @input="updateTotalHour"
                  v-validate="'required'"
                  name="time_in"
                ></date-picker>
              </div>
              <small
                class="text-danger pull-left"
                v-show="errors.has('time_in')"
                >Time-In is required.</small
              >
            </div>
          </div>

          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">Time-Out:</p>
            </div>
            <div class="col-lg-9">
              <div class="input-group">
                <date-picker
                  v-model="apply.time_out"
                  :config="DateTimeOptions"
                  v-b-tooltip.hover
                  title="Time-Out"
                  placeholder="Time-Out"
                  autocomplete="off"
                  @input="updateTotalHour"
                  v-validate="'required'"
                  name="time_out"
                ></date-picker>
              </div>
              <small
                class="text-danger pull-left"
                v-show="errors.has('time_out')"
                >Time-out is required.</small
              >
            </div>
          </div>

          <div class="rowFields mx-auto row" style="display: none">
            <div class="col-lg-3">
              <p class="textLabel">Total Hours:</p>
            </div>
            <div class="col-lg-9">
              <p class="textLabel">{{ apply.total_hours }}</p>
            </div>
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
              v-model.trim="apply.reason"
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
              :state="Boolean(apply.attachment)"
              placeholder="Choose a file or drop it here..."
              drop-placeholder="Drop file here..."
            ></b-form-file>
          </div>
        </div>

        <div v-if="apply.multiple_apply" class="rowFields mx-auto row">
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

            <template v-slot:cell(time_in)="row">
              <div class="input-group">
                <date-picker
                  v-if="row.item.is_rest_day == 0"
                  v-model="row.item.time_in"
                  :config="DateTimeOptions"
                  v-b-tooltip.hover
                  title="Time-In"
                  placeholder="Time-In"
                  autocomplete="off"
                ></date-picker>
              </div>
            </template>

            <template v-slot:cell(time_out)="row">
              <div class="input-group">
                <date-picker
                  v-if="row.item.is_rest_day == 0"
                  v-model="row.item.time_out"
                  :config="DateTimeOptions"
                  v-b-tooltip.hover
                  title="Time-Out"
                  placeholder="Time-Out"
                  autocomplete="off"
                ></date-picker>
              </div>
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
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnApply()"
            >Submit</b-button
          >
        </template>
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

              <td class="my-td">Work Date:</td>
              <td class="my-td">{{ item_edit.work_date }}</td>
            </tr>

            <tr>
              <td class="my-td">From:</td>
              <td class="my-td">{{ item_edit.time_in }}</td>

              <td class="my-td">To:</td>
              <td class="my-td">{{ item_edit.time_out }}</td>
            </tr>

            <tr>
              <td class="my-td">Reason:</td>
              <td class="my-td" colspan="3">{{ item_edit.reason }}</td>
            </tr>
          </table>
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
              OFFICIAL BUSINESS APPLICATION
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
            <td class="my-td tr-even">Reference no:</td>
            <td class="my-td tr-even">REFNUM</td>
          </tr>
          <tr>
            <td class="my-td">Work date:</td>
            <td class="my-td">{{ apply.work_date }}</td>
          </tr>
          <tr>
            <td class="my-td tr-even">From:</td>
            <td class="my-td tr-even">{{ apply.time_in }}</td>
          </tr>
          <tr>
            <td class="my-td">To:</td>
            <td class="my-td">{{ apply.time_out }}</td>
          </tr>
          <tr>
            <td class="my-td tr-even">Date filed:</td>
            <td class="my-td tr-even">DATEFILED</td>
          </tr>
          <tr>
            <td class="my-td">Reason:</td>
            <td class="my-td values">
              {{ apply.reason }}
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
      sortBy: "date_filed",
      sortDesc: true,
      fields: [
        { key: "work_date", sortable: true },
        { key: "time_in", sortable: true },
        { key: "time_out", sortable: true },
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
      apply: {
        employee_id: "",
        work_date: "",
        reference_no: "refnum00",
        shift: "",
        with_break: "",
        break_hours: 0,
        total_hours: "",
        time_in: "",
        time_out: "",
        reason: "",
        attachment: "",
        date_from: "",
        date_to: "",
        multiple_apply: false,
        status: "Pending"
      },
      item_edit: {
        employee_id: "",
        work_date: "",
        reference_no: "refnum00",
        shift: "",
        with_break: "",
        break_hours: 0,
        total_hours: "",
        time_in: "",
        time_out: "",
        reason: "",
        attachment: "",
        status: "Pending"
      },
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      DateTimeOptions: {
        // https://momentjs.com/docs/#/displaying/ YYYY-MM-DD h:mm
        format: "YYYY-MM-DD HH:mm",
        useCurrent: false,
        showClear: true,
        showClose: true
      },
      imageFile: null,
      yesno_list: [
        {
          name: "Yes"
        },
        {
          name: "No"
        }
      ],
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
      total_days: 0,
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
        { key: "time_in" },
        { key: "time_out" },
        { key: "action" }
      ],
      sched_items: [],
      sched_tblisBusy: false,
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
    //this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    //console.log(this.user);
    this.load_item(this.user.id);
  },
  mounted() {},
  updated() {},
  methods: {
    load_item(id) {
      this.$http.get("api/OfficialBusiness/" + id).then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
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
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnApply() {
      console.log("asd");
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
        this.apply.employee_id = this.user.id;
        this.apply.daysList = this.sched_items;
        this.apply.user_name = this.user.first_name + " " + this.user.last_name;
        this.apply.user_email = this.user.email1;
        this.apply.msg = document.getElementById("to-approve").innerHTML;
        this.apply.sendTo = sendTo;
        this.apply.CCto = [];

        this.$http
          .post("api/OfficialBusiness", this.apply)
          .then(response => {
            console.log(response.body);

            swal("Success!", "Item added successfully.", "success");

            this.items = response.body;
            this.totalRows = this.items.length;
            this.apply = {
              employee_id: "",
              work_date: "",
              reference_no: "refnum00",
              shift: "",
              with_break: "No",
              break_hours: 0,
              total_hours: "",
              time_in: "",
              time_out: "",
              reason: "",
              attachment: "",
              status: "Pending"
            };
            this.sched_items = [];
            this.$bvModal.hide("ModelApply");
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
      } else {
        swal(
          "No Approver!",
          "Please contact HR Department to update your approvers.",
          "info"
        );
      }
    },
    work_date_onchange() {
      this.$http
        .get(
          "api/getDTRinWorkDate/" + this.apply.work_date + "/" + this.user.id
        )
        .then(function(response) {
          //console.log(response);
          if (response.body.is_rest_day != null) {
            if (response.body.is_rest_day == 1) {
              this.apply.shift = "Rest day";
            } else if (response.body.is_rest_day == 2) {
              this.apply.shift = "Leave";
            } else {
              this.apply.shift =
                "From: " +
                response.body.shift_sched_in +
                " To: " +
                response.body.shift_sched_out;

              this.apply.time_in = response.body.shift_sched_in;
              this.apply.time_out = response.body.shift_sched_out;
            }
          } else {
            this.apply.shift = "No Shift";
          }
        });
    },
    updateTotalHour() {
      var date1 = new Date(this.apply.time_in);
      var date2 = new Date(this.apply.time_out);
      var hours = Math.abs(date1 - date2) / 36e5;
      if (this.apply.with_break == "Yes") hours -= this.apply.break_hours;
      this.apply.total_hours = hours;
    },
    imageChange(e) {
      console.log(e);
      var fileReader = new FileReader();
      fileReader.readAsDataURL(e.target.files[0]);

      fileReader.onload = e => {
        this.apply.attachment = e.target.result;
      };
      console.log(this.apply);
    },
    openModalApprovers(item) {
      this.approv_tblisBusy = true;
      this.$http
        .get("api/getApprover/" + this.user.id + "/ob/" + item.id)
        .then(function(response) {
          console.log(response.body);
          this.approv_items = response.body;
          this.approv_tblisBusy = false;
          this.approv_totalRows = this.approv_items.length;
          this.approv_currentPage = 1;
        });

      this.$bvModal.show("ModalApprovers");
    },
    cancelApplication() {
      this.item_edit.user_id = this.user.id;
      swal({
        title: "Confirmation",
        text: "Do you really want to cancel this application?",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(approve => {
        if (approve) {
          this.item_edit.user_name =
            this.user.first_name + " " + this.user.last_name;

          this.tblisBusy = true;
          this.$http
            .post("api/OfficialBusiness/cancelApp", this.item_edit)
            .then(response => {
              console.log(response.body);
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              swal("Application Canceled");
              this.$bvModal.hide("ModalViewDetails");
            });
        }
      });
    },
    multiple_apply_clicked() {
      //
    },
    date_onchange(caller) {
      if (this.apply.date_from != "" && this.apply.date_to != "") {
        this.$http
          .get(
            "api/getDTRinRange/" +
              this.apply.date_from +
              "/" +
              this.apply.date_to +
              "/" +
              this.user.id
          )
          .then(function(response) {
            console.log(response.body);

            this.sched_items = response.body.item;
            this.total_days = response.body.total_days;
            if (this.total_days == 0) {
              swal("No posted shift schedule/s for the following date/s");
            }
          });
      }
    },
    removeLeaveinList(item) {
      var removeIndex = this.sched_items
        .map(function(ii) {
          return ii.id;
        })
        .indexOf(item.id);
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
