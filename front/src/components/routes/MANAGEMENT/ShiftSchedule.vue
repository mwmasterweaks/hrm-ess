<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Shift Schedule
          <b-button
            v-b-modal="'ModelAdd'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-if="roles.create_group"
            >Add</b-button
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
            :tbody-tr-class="tblRowClass"
            head-variant=" elClr"
            @filtered="onFiltered"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
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

      <!-- ModelAdd ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModelAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Schedule Form"
      >
        <!-- form -->
        <!-- Name -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="sched_items_addName"
              class="form-control"
              v-b-tooltip.hover
              title="Name"
              placeholder="Name"
              v-validate="'required'"
              v-model.trim="sched_items_add.name"
              autocomplete="off"
              autofocus="on"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('sched_items_addName')"
              >Name is required.</small
            >
          </div>
        </div>
        <!-- Period -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Period:</p>
          </div>
          <div class="col-lg-3">
            <model-list-select
              :list="pay_period_year_list"
              v-model="pay_period_select.year"
              option-value="year"
              option-text="year"
              placeholder="Select year"
              @input="pay_period_year_onchange"
            ></model-list-select>
          </div>
          <div class="col-lg-3" v-if="pay_period_select.year != null">
            <model-list-select
              :list="pay_period_month_list"
              v-model="pay_period_select.month"
              option-value="month"
              option-text="month"
              placeholder="Select month"
              @input="pay_period_month_onchange"
            ></model-list-select>
          </div>
          <div class="col-lg-3" v-if="pay_period_select.month != null">
            <model-list-select
              :list="pay_period_day_list"
              v-model="pay_period_select.day"
              option-value="id"
              option-text="day"
              placeholder="Select day"
              @input="pay_period_onchange_add"
            ></model-list-select>
          </div>
          <!-- <div class="col-lg-9">
            <model-list-select
              :list="pay_period_list"
              v-model="pay_period_select_add"
              option-value="id"
              option-text="period"
              placeholder="Select pay period"
              name="pay_period_list"
              @input="pay_period_onchange_add"
              v-validate="'required'"
            ></model-list-select>
          </div>-->
        </div>

        <div>
          <div class="rowFields mx-auto row">
            <div class="col-lg-3">
              <p class="textLabel">
                <b>Work Date</b>
              </p>
            </div>
            <div class="col-lg-1">
              <p class="textLabel">
                <b>Rest day</b>
              </p>
            </div>
            <div class="col-lg-2">
              <p class="textLabel">
                <b></b>
              </p>
            </div>
            <div class="col-lg-3">
              <p class="textLabel">
                <b>Shift sched. in</b>
              </p>
            </div>
            <div class="col-lg-3">
              <p class="textLabel">
                <b>Shift sched. out</b>
              </p>
            </div>
          </div>

          <div
            class="rowFields mx-auto row"
            v-for="sched_item in sched_items_add.item"
            :key="sched_item.work_date"
          >
            <div class="col-lg-3">
              <p class="textLabel">
                {{ sched_item.work_date }} ({{ sched_item.day }})
              </p>
            </div>
            <div class="col-lg-1">
              <p-check
                class="textLabel p-switch p-fill"
                color="success"
                v-model="sched_item.is_rest_day"
              ></p-check>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-3">
              <date-picker
                v-if="!sched_item.is_rest_day"
                name="date"
                v-model="sched_item.shift_sched_in"
                :config="DateTimeOptions"
              ></date-picker>
              <p class="textLabel" v-else>
                <center>
                  <b>-</b>
                </center>
              </p>
            </div>
            <div class="col-lg-3">
              <date-picker
                v-if="!sched_item.is_rest_day"
                name="date"
                v-model="sched_item.shift_sched_out"
                :config="DateTimeOptions"
              ></date-picker>
              <p class="textLabel" v-else>
                <center>
                  <b>-</b>
                </center>
              </p>
            </div>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnAdd()">Add</b-button>
        </template>
      </b-modal>
      <!-- End ModelAdd -->

      <!-- modalEdit ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalEdit"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Update Group"
        @ok="handleOk"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              title="Name of group"
              placeholder="Group name"
              v-validate="'required'"
              v-model.trim="item_edit.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')"
              >Group Name is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="desc"
              class="form-control"
              v-b-tooltip.hover
              title="Description of the group"
              v-validate="'required'"
              placeholder="Description"
              v-model.trim="item_edit.desc"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('desc')"
              >Description is required.</small
            >
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_group"
            @click="btnUpdate()"
            >Update</b-button
          >
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_group"
            @click="btnDelete()"
            >Delete</b-button
          >
        </template>
      </b-modal>
      <!-- End modalEdit -->
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import "bootstrap/dist/css/bootstrap.css";
import datePicker from "vue-bootstrap-datetimepicker";

export default {
  components: {
    "model-list-select": ModelListSelect,
    "date-picker": datePicker,
    "p-check": PrettyCheck
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "desc", label: "Description", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 0,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      item_add: {
        name: "",
        desc: ""
      },
      item_edit: {
        name: "",
        desc: ""
      },
      pay_period_list: [],
      pay_period_year_list: [],
      pay_period_month_list: [],
      pay_period_day_list: [],
      pay_period_select: {
        year: null,
        month: null,
        day: {}
      },
      pay_period_select_add: {},
      sched_items_add: {
        item: "",
        name: ""
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
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.items = this.$global.getGroup();
    this.tblisBusy = false;
    this.totalRows = this.items.length;
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {}, 100);
      });

      this.$http.get("api/PayPeriod").then(function(response) {
        this.pay_period_list = response.body;
      });

      this.$http.post("api/PayPeriod/getYear").then(function(response) {
        this.pay_period_year_list = response.body;
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
    tblRowClicked(item, index, event) {
      this.$bvModal.show("modalEdit");
      this.item_edit = item;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnUpdate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this item?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            if (update) {
              this.item_edit.user_id = this.user.id;
              this.item_edit.user_name = this.user.name;

              this.$http
                .put("api/Group/" + this.item_edit.id, this.item_edit)
                .then(response => {
                  this.items = response.body;
                  this.$global.setGroup(response.body);
                  this.totalRows = this.items.length;
                  swal("Update!", "Update successfully", "success");
                  this.$bvModal.hide("modalEdit");
                  this.tblisBusy = false;
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
        }
      });
    },
    btnAdd() {
      console.log(this.sched_items_add);
      this.$validator.validateAll().then(result => {
        if (result) {
          swal({
            title: "Are you sure?",
            text: "",
            icon: "info",
            buttons: true
          }).then(result => {
            if (result) {
              this.sched_items_add.user_id = this.user.id;
              this.sched_items_add.user_name = this.user.name;

              this.$http
                .post("api/ShiftSchedule", this.sched_items_add)
                .then(response => {
                  console.log(response.body);
                  this.sched_items_add = {
                    item: "",
                    name: ""
                  };
                  this.pay_period_select = {
                    year: null,
                    month: null,
                    day: {}
                  };
                  this.$bvModal.show("ModelAdd");
                });
            }
          });
        }
      });
    },
    btnDelete() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this item permanently",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.items = [];
          this.tblisBusy = true;
          this.$http
            .delete("api/Group/" + this.item_edit.id)
            .then(response => {
              this.$bvModal.hide("modalEdit");
              swal("Deleted!", "Item has been deleted", "success").then(
                value => {
                  this.$global.setGroup(response.body);
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  this.tblisBusy = false;
                }
              );
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
    pay_period_onchange_add() {
      var temp = null;
      this.$http
        .put("api/getDTR_add/" + temp, this.pay_period_select.day)
        .then(function(response) {
          this.sched_items_add.item = response.body;
          this.sched_items_add.pay_period = this.pay_period_select.day;
        });
    },
    pay_period_year_onchange() {
      this.$http
        .get("api/PayPeriod/getMonth/" + this.pay_period_select.year)
        .then(function(response) {
          this.pay_period_month_list = response.body;
          this.pay_period_select.month = null;
          this.pay_period_select.day = null;
        });
    },
    pay_period_month_onchange() {
      if (this.pay_period_select.month.toString().length == 1)
        this.pay_period_select.month =
          "0" + this.pay_period_select.month.toString();

      this.$http
        .get(
          "api/PayPeriod/getDay/" +
            this.pay_period_select.year +
            "-" +
            this.pay_period_select.month
        )
        .then(function(response) {
          this.pay_period_day_list = response.body;
          this.pay_period_select.day = {};
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
