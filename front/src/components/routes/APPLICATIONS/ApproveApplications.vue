<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Approve Applications</p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group
                label-cols-sm="4"
                label="Select App type"
                class="mb-0"
              >
                <model-list-select
                  :list="application_list"
                  v-model="selected_application"
                  option-value="name"
                  option-text="name"
                  placeholder="Application type"
                  name="app_type"
                  @input="OnChangeApplication"
                ></model-list-select>
              </b-form-group>
              <br />
              <b-form-group label-cols-sm="4" label="Filter" class="mb-0">
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
              <br />
              <br />
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
            :tbody-tr-class="tblRowClass"
            head-variant=" elClr"
            @filtered="onFiltered"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>

            <template v-slot:cell(work_date)="data">
              <span v-html="data.value"></span>
            </template>

            <!-- <template v-slot:cell(action)="data">
              <span v-html="data.value"></span>
            </template>-->

            <template slot="table-caption"></template>
            <template v-slot:cell(action)="row">
              <button
                class="btn btn-success"
                v-if="row.item.status == 'Pending'"
                @click="approveRequest(row.item)"
                title="Approve"
              >
                <i class="fas fa-thumbs-up"></i>
              </button>
              <button
                class="btn btn-danger"
                v-if="row.item.status == 'Pending'"
                v-b-modal.modalDisapprove
                @click="disapproveClicked(row.item)"
                title="Disapprove"
              >
                <i class="fas fa-thumbs-down"></i>
              </button>
              <button
                class="btn btn-warning"
                v-if="row.item.status == 'Pending'"
                v-b-modal.modalDTR
                @click="viewDTR(row.item)"
                title="View DTR"
              >
                <i class="fas fa-calendar-alt"></i>
              </button>
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
          <table class="my-table" v-if="chkStr(item.reference_no) == 'BA'">
            <tr>
              <td class="my-td">Reference no:</td>
              <td class="my-td">{{ item.reference_no }}</td>

              <td class="my-td">Description:</td>
              <td class="my-td">{{ item.description }}</td>
            </tr>

            <tr>
              <td class="my-td">Type</td>
              <td class="my-td">{{ item.type }}</td>

              <td class="my-td">Employee:</td>
              <td class="my-td">{{ item.first_name }} {{ item.last_name }}</td>
            </tr>

            <tr>
              <td class="my-td">Latitude:</td>
              <td class="my-td">{{ item.latitude }}</td>

              <td class="my-td">Longitude:</td>
              <td class="my-td">{{ item.longitude }}</td>
            </tr>

            <tr>
              <td class="my-td">Punch Time:</td>
              <td class="my-td" colspan="3">{{ item.punch_time_converted }}</td>
            </tr>
          </table>

          <table class="my-table" v-else>
            <tr>
              <td class="my-td">Reference no:</td>
              <td class="my-td">{{ item.reference_no }}</td>

              <td class="my-td">Description:</td>
              <td class="my-td">{{ item.description }}</td>
            </tr>

            <tr>
              <td class="my-td">Work Date:</td>
              <td class="my-td">{{ item.work_date }}</td>

              <td class="my-td">Employee:</td>
              <td class="my-td">{{ item.first_name }} {{ item.last_name }}</td>
            </tr>

            <tr v-if="chkStr(item.reference_no) == 'CR'">
              <td class="my-td">Shift:</td>
              <td class="my-td">{{ item.shift }}</td>

              <td class="my-td">Type:</td>
              <td class="my-td">{{ item.type }}</td>
            </tr>

            <tr>
              <td class="my-td">From:</td>
              <td class="my-td">{{ item.from }}</td>

              <td class="my-td">To:</td>
              <td class="my-td">{{ item.to }}</td>
            </tr>

            <tr v-if="chkStr(item.reference_no) == 'LV'">
              <td class="my-td">Date Filed:</td>
              <td class="my-td">{{ item.date_filed }}</td>

              <td class="my-td">Total day/s:</td>
              <td class="my-td">{{ item.total_days }}</td>
            </tr>

            <tr v-if="item.description == 'Overtime'">
              <td class="my-td">With Break:</td>
              <td class="my-td">{{ item.with_break }}</td>

              <td class="my-td">Break Hours:</td>
              <td class="my-td">{{ item.break_hours }}</td>
            </tr>

            <tr v-if="item.description == 'Overtime'">
              <td class="my-td">Date Filed:</td>
              <td class="my-td">{{ item.date_filed }}</td>

              <td class="my-td">Total Hours:</td>
              <td class="my-td">{{ item.total_hours }}</td>
            </tr>

            <tr>
              <td class="my-td">Reason:</td>
              <td class="my-td" colspan="3">{{ item.reason }}</td>
            </tr>
          </table>

          <div
            v-if="chkStr(item.reference_no) == 'LV'"
            class="rowFields mx-auto row"
          >
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
            @click="btnViewAttachment(item.attachment)"
            title="View Attachment"
          >
            <i class="fas fa-paperclip"></i>
          </b-button>
          <b-button
            size="sm"
            variant="success"
            title="Approve"
            @click="approveRequest(item)"
          >
            <i class="fas fa-thumbs-up"></i>
          </b-button>
          <b-button
            size="sm"
            variant="danger"
            title="Disapprove"
            v-b-modal.modalDisapprove
          >
            <i class="fas fa-thumbs-down"></i>
          </b-button>
        </template>
      </b-modal>
      <!-- End ModalViewDetails -->

      <!-- ModalDTR ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalDTR"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        :title="emp_name"
      >
        <b-row style="margin:10px;">
          <b-col md="5" class="my-1">
            <b-form-group label-cols-sm="2" label="Period" class="mb-0">
              <b-input-group>
                <model-list-select
                  :list="pay_period_list"
                  v-model="pay_period_select"
                  option-value="id"
                  option-text="period"
                  placeholder="Select pay period"
                  name="pay_period_list"
                  @input="pay_period_onchange"
                  v-validate="'required'"
                ></model-list-select>
                <!-- <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''">Clear</b-button>
                  </b-input-group-append>-->
              </b-input-group>
            </b-form-group>
          </b-col>
          <b-col md="5 " class="my-1"></b-col>
        </b-row>
        <b-table
          class="elClr"
          show-empty
          striped
          hover
          outlined
          :fields="dtrfields"
          :items="dtritems"
          :busy="dtrtblisBusy"
          head-variant=" elClr"
        >
          <div slot="table-busy" class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>

          <template v-slot:cell(shift_sched)="data">
            <span v-html="data.value"></span>
          </template>
          <template slot="table-caption"></template>
        </b-table>
      </b-modal>

      <!-- End ModalDTR -->

      <b-modal id="modalthumbnail" size="xl" hide-footer hide-header>
        <b-container fluid class="p-4 bg-dark">
          <center>
            <b-img
              thumbnail
              fluid
              :src="$url_attachments + item.attachment"
              alt="Image 1"
            ></b-img>
          </center>
        </b-container>
      </b-modal>

      <b-modal
        id="modalDisapprove"
        size="lg"
        centered
        hide-footer
        hide-header
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
      >
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Remarks:</p>
          </div>
          <div class="col-lg-9">
            <textarea
              rows="2"
              name="Remarks"
              class="form-control"
              v-b-tooltip.hover
              title="Please add remarks why you disapprove this application"
              placeholder="Remarks"
              v-validate="'required'"
              v-model.trim="remarks"
            ></textarea>
            <small class="text-danger pull-left" v-show="errors.has('Remarks')"
              >Remarks is required.</small
            >
          </div>
        </div>
        <b-button
          class="pull-right textLabel"
          size="sm"
          variant="success"
          @click="disapproveRequest()"
          >Submit</b-button
        >
      </b-modal>
    </div>
    <div id="ap-to-approve">
      <div>
        Please check the following application waiting for approval on HRMESS
        system.<br /><br />
        <table class="my-table">
          <tr>
            <td colspan="2" class="my-td head-bg">
              APPLICATIONTYPE
            </td>
          </tr>
          <tr>
            <td class="my-td name-bg">
              NAME:
            </td>
            <td class="my-td name-bg">
              EMPLOYEE
            </td>
          </tr>
          <tr>
            <td class="my-td">Reference no:</td>
            <td class="my-td">REFNUM</td>
          </tr>
          <tr v-show="apptype == 'LV'">
            <td class="my-td">Description:</td>
            <td class="my-td">LEAVETYPE</td>
          </tr>
          <tr v-show="apptype != 'LV'">
            <td class="my-td">Work date:</td>
            <td class="my-td">WORKDATE</td>
          </tr>
          <tr>
            <td class="my-td">From:</td>
            <td class="my-td">FROM</td>
          </tr>
          <tr>
            <td class="my-td">To:</td>
            <td class="my-td">TO</td>
          </tr>
          <tr v-show="apptype == 'CRD'">
            <td class="my-td">Shift:</td>
            <td class="my-td">SHIFT</td>
          </tr>
          <tr v-show="apptype == 'CRD'">
            <td class="my-td">Type:</td>
            <td class="my-td">TYPE</td>
          </tr>
          <tr v-show="apptype == 'OT'">
            <td class="my-td">With break:</td>
            <td class="my-td">WITHBREAK</td>
          </tr>
          <tr v-show="apptype == 'OT'">
            <td class="my-td">Break hours:</td>
            <td class="my-td">BREAKHOURS</td>
          </tr>
          <tr v-show="apptype == 'OT'">
            <td class="my-td">Total no. of hours:</td>
            <td class="my-td">TTLHOURS</td>
          </tr>
          <tr v-show="apptype == 'LV'">
            <td class="my-td">Total no. of days:</td>
            <td class="my-td">TTLDAYS</td>
          </tr>
          <tr>
            <td class="my-td">Date filed:</td>
            <td class="my-td">DATEFILED</td>
          </tr>
          <tr>
            <td class="my-td">Reason:</td>
            <td class="my-td values">
              REASON
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
//import sidebar from '../../Sidebar.vue';
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    // "sidebar": sidebar,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: true,
      dtrtblisBusy: true,
      fields: [
        { key: "reference_no", sortable: true },
        { key: "description", sortable: true },
        {
          key: "Name",
          label: "Employee",
          formatter: (value, key, item) => {
            return item.first_name + " " + item.last_name;
          },
          sortable: true
        },
        { key: "from", sortable: true },
        { key: "to", sortable: true },
        { key: "date_filed", sortable: true },
        { key: "action", sortable: true }
      ],
      dtrfields: [
        { key: "work_date", sortable: true },
        { key: "day", sortable: true },
        {
          key: "shift_sched",
          label: "Shift Sched",
          formatter: (value, key, item) => {
            if (item.is_rest_day == 1)
              return "<p class='text-danger'>Rest Day</p>";
            else if (item.is_rest_day == 2)
              return "<p class='text-danger'>Leave</p>";
            else if (item.is_rest_day == 3)
              return "<p class='text-danger'>Holiday</p>";
            else return item.shift_sched_in + " - " + item.shift_sched_out;
          },
          sortable: true
        },
        { key: "time_in", sortable: true },
        { key: "time_out", sortable: true }
      ],
      items: [],
      item: {},
      remarks: "",
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 25,
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
      application_list: [
        {
          name: "Leave"
        },
        {
          name: "Overtime"
        },
        {
          name: "Official Business"
        },
        {
          name: "Change Shift"
        },
        {
          name: "Change Rest Day"
        },
        {
          name: "Offset Request"
        },
        {
          name: "Missing Time Logs"
        },
        {
          name: "Manual Attendance"
        }
        // {
        //   name: "Biometric Attendance"
        // }
      ],
      selected_application: "",
      sched_fields_details: [
        { key: "leave_date", label: "Date" },
        { key: "halfday" },
        { key: "halfday_type" }
      ],
      sched_items_details: [],
      sched_tblisBusy_details: false,
      roles: [],
      apptype: "",
      pay_period_list: [],
      pay_period_select: "",
      dtritems: [],
      emp_id: 0,
      emp_name: ""
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
    this.$root.$emit("Sidebar");
    this.user = this.$global.getUser();
    this.load_item(this.user.id);
  },
  mounted() {},
  updated() {},
  methods: {
    load_item(id) {
      this.$http.get("api/getToApprove/" + id).then(function(response) {
        console.log(response.body);
        this.items = response.body.sort((a, b) =>
          a.dbdate_filed > b.dbdate_filed ? -1 : 1
        );
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
      this.item = item;
      if (this.item.leave_days != null)
        this.sched_items_details = this.item.leave_days;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },

    work_date_onchange() {
      this.$http
        .get(
          "api/getDTRinWorkDate/" + this.apply.work_date + "/" + this.user.id
        )
        .then(function(response) {
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
      var fileReader = new FileReader();
      fileReader.readAsDataURL(e.target.files[0]);

      fileReader.onload = e => {
        this.apply.attachment = e.target.result;
      };
    },
    OnChangeApplication() {
      this.tblFilter = this.selected_application;
      // this.$http
      //   .get(
      //     "api/getApplication/" +
      //       this.selected_application +
      //       "/" +
      //       this.user.approver.id
      //   )
      //   .then(function(response) {

      //   });
    },
    approveRequest(item) {
      this.apptype = item.reference_no.substr(
        0,
        item.reference_no.indexOf("-")
      );
      if (this.apptype == "BA") item.work_date = item.punch_time.substr(0, 10);
      item.userID = this.user.id;
      console.log(item);
      swal({
        title: "Confirmation",
        text: "Do you really want to approve this application?",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(approve => {
        if (approve) {
          item.msg = document.getElementById("ap-to-approve").innerHTML;
          console.log(item);
          this.tblisBusy = true;
          this.$http
            .put("api/approveRequest/", item)
            .then(response => {
              console.log(response.body);
              this.items = response.body;
              this.totalRows = this.items.length;
              this.tblisBusy = false;
              swal("Approved!");
              this.$root.$emit("Sidebar");
              this.$bvModal.hide("ModalViewDetails");
            })
            .catch(response => {
              console.log(response.body);
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                  this.tblisBusy = false;
                }
              });
            });
        }
      });
    },
    disapproveClicked(item) {
      this.item = item;
    },
    disapproveRequest() {
      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Confirmation",
            text: "Do you really want to disapprove this application?",
            icon: "info",
            buttons: true,
            dangerMode: true
          }).then(approve => {
            if (approve) {
              var item = this.item;
              item.remarks = this.remarks;
              item.userID = this.user.id;

              this.tblisBusy = true;
              this.$http.put("api/disapproveRequest", item).then(response => {
                this.remarks = "";
                this.items = response.body;
                this.totalRows = this.items.length;
                this.tblisBusy = false;
                this.$root.$emit("Sidebar");
                this.$bvModal.hide("modalDisapprove");
              });
            }
          });
        }
      });
    },
    chkStr(str) {
      if (str != null) {
        return str.substring(0, 2);
      }
    },
    btnViewAttachment(img) {},
    viewDTR(item) {
      console.log(item);
      var mid_i = item.middle_name == null ? "" : item.middle_name;
      this.emp_name =
        "DTR of " +
        item.first_name +
        " " +
        mid_i +
        " " +
        item.last_name +
        " (" +
        item.id +
        ")";
      this.emp_id = item.employee_id;

      if (this.pay_period_select != "") this.pay_period_onchange();
      else {
        this.dtrtblisBusy = false;
        this.$http.get("api/PayPeriod").then(function(response) {
          this.pay_period_list = response.body;
        });
      }
    },
    pay_period_onchange() {
      this.dtrtblisBusy = true;
      this.$http
        .get("api/getDTR/" + this.pay_period_select + "/" + this.emp_id)
        .then(function(response) {
          this.dtritems = response.body;
          this.dtrtblisBusy = false;
        });
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
