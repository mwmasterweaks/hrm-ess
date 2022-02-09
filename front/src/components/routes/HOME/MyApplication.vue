<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">My Application</p>
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
              <td class="my-td">{{ item_edit.workdate }}</td>
            </tr>

            <tr v-if="chkStr(item_edit.reference_no) == 'CR'">
              <td class="my-td">Shift:</td>
              <td class="my-td">{{ item_edit.shift }}</td>

              <td class="my-td">Type:</td>
              <td class="my-td">{{ item_edit.type }}</td>
            </tr>

            <tr>
              <td class="my-td">From:</td>
              <td class="my-td">{{ item_edit.from }}</td>

              <td class="my-td">To:</td>
              <td class="my-td">{{ item_edit.to }}</td>
            </tr>
            <tr v-if="chkStr(item_edit.reference_no) == 'LV'">
              <td class="my-td">Date Filed:</td>
              <td class="my-td">{{ item_edit.date_filed }}</td>

              <td class="my-td">Total day/s:</td>
              <td class="my-td">{{ item_edit.total_days }}</td>
            </tr>

            <tr v-if="chkStr(item_edit.reference_no) == 'OT'">
              <td class="my-td">With Break:</td>
              <td class="my-td">{{ item_edit.with_break }}</td>

              <td class="my-td">Break Hours:</td>
              <td class="my-td">{{ item_edit.break_hours }}</td>
            </tr>

            <tr v-if="chkStr(item_edit.reference_no) == 'OT'">
              <td class="my-td">Date Filed:</td>
              <td class="my-td">{{ item_edit.date_filed }}</td>

              <td class="my-td">Total Hours:</td>
              <td class="my-td">{{ item_edit.total_hours }}</td>
            </tr>

            <tr>
              <td class="my-td">Reason:</td>
              <td class="my-td" colspan="3">{{ item_edit.reason }}</td>
            </tr>
          </table>

          <div
            v-if="chkStr(item_edit.reference_no) == 'LV'"
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
            title="View Attachment"
          >
            <i class="fas fa-paperclip"></i>
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
              :src="$url_attachments + item_edit.attachment"
              alt="Image 1"
            ></b-img>
          </center>
        </b-container>
      </b-modal>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    "date-picker": datePicker,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      keycloak: null,
      tblisBusy: true,
      sortBy: "date_filed",
      sortDesc: true,
      fields: [
        { key: "reference_no", sortable: true },
        { key: "desc", sortable: true },
        { key: "workdate", sortable: true },
        { key: "from", sortable: true },
        { key: "to", sortable: true },
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
      item_edit: {
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
    //this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.load_item(this.user.id);
  },
  mounted() {},
  updated() {},
  methods: {
    load_item(id) {
      //this.$http.get("http://localhost:8088/hrmessback/api/getMyApp/" + id)
      this.$http.get("api/getMyApp/" + id).then(function(response) {
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
      console.log(item);
      this.$bvModal.show("ModalViewDetails");
      if (item.leave_days != null) this.sched_items_details = item.leave_days;
      this.item_edit = item;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnApply() {
      if (this.apply.total_hours > 0) {
        this.$validator.validateAll().then(result => {
          if (result) {
            this.apply.employee_id = this.user.id;
            this.apply.user_id = this.user.id;
            this.apply.user_name = this.user.name;

            this.$http
              .post("api/OverTime", this.apply)
              .then(response => {
                swal("Notification", "Added successfully", "success");

                this.items = response.body;
                this.totalRows = this.items.length;
                this.apply = {
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
                };

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
          }
        });
      } else {
        swal({
          title: "Overtime hour must be greater than zero",
          text: "",
          icon: "info",
          dangerMode: true
        }).then(value => {
          if (value) {
          }
        });
      }
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

              this.apply.time_in = response.body.shift_sched_out;
              this.apply.time_out = response.body.shift_sched_out;
            }
          } else {
            this.apply.shift = "No Shift";
          }
        });
    },
    updateOverTimeHour() {
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
    chkStr(str) {
      if (str != null) {
        return str.substring(0, 2);
      }
    },
    openModalApprovers(item) {
      this.approv_tblisBusy = true;
      var ext = "ot";
      if (this.chkStr(item.reference_no) == "LV") ext = "lv";
      if (this.chkStr(item.reference_no) == "OT") ext = "ot";
      if (this.chkStr(item.reference_no) == "OB") ext = "ob";
      if (this.chkStr(item.reference_no) == "CS") ext = "cs";
      if (this.chkStr(item.reference_no) == "CRD") ext = "crd";
      if (this.chkStr(item.reference_no) == "MTL") ext = "mtl";
      if (this.chkStr(item.reference_no) == "MA") ext = "ma";
      this.$http
        .get("api/getApprover/" + this.user.id + "/" + ext + "/" + item.id)
        .then(function(response) {
          this.approv_items = response.body;
          this.approv_tblisBusy = false;
          this.approv_totalRows = this.approv_items.length;
          this.approv_currentPage = 1;
        });

      this.$bvModal.show("ModalApprovers");
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
