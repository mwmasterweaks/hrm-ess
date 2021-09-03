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
              <b-form-group label-cols-sm="4" label="Select App type" class="mb-0">
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
                  <b-form-input v-model="tblFilter" placeholder="Filter"></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''">Clear</b-button>
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <br />
              <br />
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
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

            <template v-slot:cell(action)="data">
              <span v-html="data.value"></span>
            </template>

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
          <table class="my-table">
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

          <div v-if="chkStr(item.reference_no) == 'LV'" class="rowFields mx-auto row">
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
              <template v-slot:cell(halfday)="data">
                <span v-html="data.value"></span>
              </template>
              <template v-slot:cell(halfday_type)="data">
                <span v-html="data.value"></span>
              </template>

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
          <b-button size="sm" variant="success" title="Approve" @click="approveRequest(item)">
            <i class="fas fa-thumbs-up"></i>
          </b-button>
          <b-button size="sm" variant="danger" title="Disapprove" v-b-modal.modalDisapprove>
            <i class="fas fa-thumbs-down"></i>
          </b-button>
        </template>
      </b-modal>
      <!-- End ModalViewDetails -->

      <b-modal id="modalthumbnail" size="xl" hide-footer hide-header>
        <b-container fluid class="p-4 bg-dark">
          <center>
            <b-img
              thumbnail
              fluid
              :src="'http://localhost:8000/attachments/' + item.attachment"
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
            <small class="text-danger pull-left" v-show="errors.has('Remarks')">Remarks is required.</small>
          </div>
        </div>
        <b-button
          class="pull-right textLabel"
          size="sm"
          variant="success"
          @click="disapproveRequest()"
        >Submit</b-button>
      </b-modal>
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
        reference_no: "tempnumber123",
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
      ],
      selected_application: "",
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
    //this.roles = this.$global.getRoles();

    this.$root.$emit("Sidebar");
    this.user = this.$global.getUser();
    this.load_item(this.user.employee_id);
  },
  mounted() {},
  updated() {},
  methods: {
    load_item(id) {
      this.$http.get("api/getToApprove/" + id).then(function(response) {
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
          "api/getDTRinWorkDate/" +
            this.apply.work_date +
            "/" +
            this.user.employee_id
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
      item.userID = this.user.employee_id;
      swal({
        title: "Notification",
        text: "Do you really want to approve this application?",
        icon: "info",
        buttons: true,
        dangerMode: true
      }).then(approve => {
        if (approve) {
          this.tblisBusy = true;
          this.$http
            .put("api/approveRequest", item)
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
            title: "Notification",
            text: "Do you really want to approve this application?",
            icon: "info",
            buttons: true,
            dangerMode: true
          }).then(approve => {
            if (approve) {
              var item = this.item;
              item.remarks = this.remarks;
              item.userID = this.user.employee_id;

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
    btnViewAttachment(img) {}
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
