<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Offset Application (soon)
          <b-button
            v-b-modal="'ModelApply'"
            type="button"
            style="display:none"
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
        title="Apply Change Rest Day"
        @ok="handleOk"
      >
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Change type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="change_type_list"
              v-model="apply.type"
              option-value="name"
              option-text="name"
              placeholder="Type"
              name="Type"
              v-validate="'required'"
              @input="OnChangeType"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('type')"
              >Type is required.</small
            >
          </div>
        </div>

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
              >{{ work_date_notif }}</small
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

        <div
          class="rowFields mx-auto row"
          v-if="apply.type != 'Shift to Rest Day'"
        >
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
                name="time_in"
              ></date-picker>
            </div>
          </div>
        </div>

        <div
          class="rowFields mx-auto row"
          v-if="apply.type != 'Shift to Rest Day'"
        >
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
                name="time_out"
              ></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">With Break:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="yesno_list"
              v-model="apply.with_break"
              option-value="name"
              option-text="name"
              placeholder="Yes or No"
              name="with_break"
              @input="updateTotalHour"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('with_break')"
              >With Break is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row" style="display: none">
          <div class="col-lg-3">
            <p class="textLabel">Break Hours:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="break_hours"
              v-validate="'required|between:0,30'"
              class="form-control"
              v-b-tooltip.hover
              title="Break Hours:"
              placeholder="Break Hours:"
              @input="updateTotalHour"
              v-model.trim="apply.break_hours"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('break_hours')"
              >Break Hours is required.</small
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
        size="xl"
        title="Approver"
        @ok="handleOk"
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

        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnApply()"
            >Submit</b-button
          >
        </template>
      </b-modal>
      <!-- End ModalApprovers -->
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
      tblisBusy: false,
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
        reference_no: "tempnumber123",
        shift: "",
        type: "",
        with_break: "No",
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
      work_date_notif: "Work date is required.",
      yesno_list: [
        {
          name: "Yes"
        },
        {
          name: "No"
        }
      ],
      change_type_list: [
        {
          name: "Rest Day to Shift"
        },
        {
          name: "Shift to Rest Day"
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
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    //this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    //console.log(this.user);
    //this.load_item(this.user.employee_id);
  },
  mounted() {},
  updated() {},
  methods: {
    load_item(id) {
      this.$http.get("api/ChangeRestDay/" + id).then(function(response) {
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
      this.$bvModal.show("modalEdit");
      this.item_edit = item;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnApply() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.apply.employee_id = this.user.employee_id;
          this.$http
            .post("api/ChangeRestDay", this.apply)
            .then(response => {
              swal("Notification", "Added successfully", "success");

              this.items = response.body;
              this.totalRows = this.items.length;
              this.apply = {
                employee_id: "",
                work_date: "",
                reference_no: "tempnumber123",
                shift: "",
                type: "",
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
          //console.log(response);
          if (response.body.is_rest_day != null) {
            if (response.body.is_rest_day) {
              this.apply.shift = "Rest day";
              if (this.apply.type == "Shift to Rest Day") {
                this.work_date_notif =
                  "Change Rest Day invalid. Specified date must not your rest day schedule.";
                this.apply.work_date = "";
                this.apply.shift = "";
              }
            } else {
              this.apply.shift =
                "From: " +
                response.body.shift_sched_in +
                " To: " +
                response.body.shift_sched_out;

              this.apply.time_in = response.body.shift_sched_in;
              this.apply.time_out = response.body.shift_sched_out;

              if (this.apply.type == "Rest Day to Shift") {
                this.work_date_notif =
                  "Change Rest Day invalid. Specified date must be your rest day schedule.";
                this.apply.work_date = "";
                this.apply.shift = "";
              }
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
    OnChangeType() {
      this.work_date_notif = "Work Date is required.";
      this.apply.work_date = "";
      this.apply.shift = "";
    },
    openModalApprovers(item) {
      this.approv_tblisBusy = true;
      this.$http
        .get("api/getApprover/" + this.user.employee_id + "/leave/" + item.id)
        .then(function(response) {
          console.log(response.body);
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
