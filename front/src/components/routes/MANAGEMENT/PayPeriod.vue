<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Manage Pay Period
          <b-button
            v-b-modal="'ModelAdd'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-if="roles.create_pay_period"
          >Add</b-button>
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
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
        title="Add Pay Period"
        @ok="handleOk"
      >
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Group:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="group_list"
              v-model="item_add.group_id"
              option-value="id"
              option-text="name"
              placeholder="Select Group"
              name="Group"
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('Group')">Group is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Year:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="year"
              v-validate="'required|between:2000,9000'"
              class="form-control"
              v-b-tooltip.hover
              title="Year"
              placeholder="Year"
              v-model.trim="item_add.year"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('year')">Input valid year.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Frequency:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="frequency_list"
              v-model="item_add.frequency"
              option-value="value"
              option-text="name"
              placeholder="Select Frequency"
              name="Frequency"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('Frequency')"
            >Frequency is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Pay Period:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="pay_period_list"
              v-model="item_add.pay_period_selected"
              option-value="value"
              option-text="name"
              placeholder="Pay period"
              name="pay_period"
              v-validate="'required'"
            ></model-list-select>
            <small
              class="text-danger pull-left"
              v-show="errors.has('pay_period')"
            >Pay period is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row" v-if="item_add.pay_period_selected == '2'">
          <div class="col-lg-3">
            <p class="textLabel">Day/s:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="year"
              v-validate="'required|between:1,30'"
              class="form-control"
              v-b-tooltip.hover
              title="Day"
              placeholder="Day"
              v-model="item_add.days"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('year')">Input valid year.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row" style="display:none">
          <div class="col-lg-3">
            <p class="textLabel">Date from:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker v-model="item_add.from" :config="Dateoptions" autocomplete="off"></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row" style="display:none">
          <div class="col-lg-3">
            <p class="textLabel">Date to:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker v-model="item_add.to" :config="Dateoptions" autocomplete="off"></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-12">
            <b-button class="pull-right" size="sm" variant="success" @click="btnGenerate()">Generate</b-button>
          </div>
        </div>

        <div v-if="generatedPeriod != null">
          <div class="rowFields mx-auto row">
            <div class="col-lg-4">
              <p class="textLabel">
                <b>From</b>
              </p>
            </div>
            <div class="col-lg-4">
              <p class="textLabel">
                <b>To</b>
              </p>
            </div>

            <div class="col-lg-4">
              <p class="textLabel">
                <b>Pay Period</b>
              </p>
            </div>
          </div>

          <div class="rowFields mx-auto row" v-for="period in  generatedPeriod" :key="period.from">
            <div class="col-lg-4">
              <p class="textLabel">{{ period.from }}</p>
            </div>
            <div class="col-lg-4">
              <p class="textLabel">{{ period.to }}</p>
            </div>
            <div class="col-lg-4">
              <p class="textLabel">{{ period.period }}</p>
            </div>
          </div>
        </div>

        <!-- /form -->
        <div slot="modal-footer">
          <b-button size="sm" v-if="generatedPeriod != null" variant="success" @click="btnAdd()">Add</b-button>
        </div>
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
        title="Update Pay Period"
        @ok="handleOk"
      >
        <!-- form -->

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Pay period:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker v-model="item_edit.period" :config="Dateoptions" autocomplete="off"></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date from:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker v-model="item_edit.from" :config="Dateoptions" autocomplete="off"></date-picker>
            </div>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Date to:</p>
          </div>
          <div class="col-lg-9">
            <div class="input-group">
              <date-picker v-model="item_edit.to" :config="Dateoptions" autocomplete="off"></date-picker>
            </div>
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{  }">
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_pay_period"
            @click="btnUpdate()"
          >Update</b-button>
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_pay_period"
            @click="btnDelete()"
          >Delete</b-button>
        </template>
      </b-modal>
      <!-- End modalEdit -->
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
      tblisBusy: true,
      fields: [
        { key: "period", sortable: true },
        { key: "frequency", sortable: true },
        { key: "year", sortable: true },
        { key: "group.name", label: "Group", sortable: true },
        { key: "from", sortable: true },
        { key: "to", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      item_add: {
        period: "",
        frequency: "",
        days: 1,
        group_id: "",
        year: "",
        from: "",
        to: ""
      },
      item_edit: {
        period: "",
        frequency: "",
        group_id: "",
        year: "",
        from: "",
        to: ""
      },
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      },
      group_list: [
        {
          id: "1",
          name: "g1"
        }
      ],
      frequency_list: [
        {
          name: "Weekly",
          value: "weekly"
        },
        {
          name: "Semi-monthly",
          value: "semi"
        },
        {
          name: "Monthly",
          value: "monthly"
        }
      ],
      pay_period_list: [
        {
          name: "Last day of period",
          value: 1
        },
        {
          name: "After last day of period",
          value: 2
        }
      ],
      after_pay_period_count: 0,
      generatedPeriod: null,
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.roles = this.$global.getRoles();
    this.get_group();
    this.load_items("PayPeriod");
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load_items(model) {
      this.$http.get("api/" + model).then(function(response) {
        this.items = response.body;
        this.totalRows = this.items.length;
        this.tblisBusy = false;
      });
    },
    get_group() {
      this.$http.get("api/Group").then(function(response) {
        this.group_list = response.body;
      });
    },
    load() {
      this.$nextTick(function() {
        setTimeout(function() {}, 100);
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
              this.$http
                .put("api/PayPeriod/" + this.item_edit.id, this.item_edit)
                .then(response => {
                  this.items = response.body;
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
      this.$validator.validateAll().then(result => {
        if (result) {
          var valueToSend = {
            data: this.generatedPeriod
          };
          this.$http.post("api/PayPeriod", valueToSend).then(response => {
            console.log(response.body);
            swal("Notification", "Added successfully", "success");
            this.items = response.body;
            this.totalRows = this.items.length;
            this.generatedPeriod = null;
            this.item_add = {
              period: "",
              frequency: "",
              days: 1,
              group_id: "",
              year: "",
              from: "",
              to: ""
            };

            this.$bvModal.hide("ModelAdd");
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
            .delete("api/PayPeriod/" + this.item_edit.id)
            .then(response => {
              this.$bvModal.hide("modalEdit");
              swal("Deleted!", "Item has been deleted", "success").then(
                value => {
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
    btnGenerate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/generatePayPeriod", this.item_add)
            .then(response => {
              this.generatedPeriod = response.body;
              console.log(response.body);
            });
        }
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
