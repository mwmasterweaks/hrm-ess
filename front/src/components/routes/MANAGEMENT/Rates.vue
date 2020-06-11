<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Manage Rates
          <b-button
            v-b-modal="'ModelAdd'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
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
        title="Add Rates"
        @ok="handleOk"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Rate Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              title="Name of rate"
              placeholder="Rate name"
              v-validate="'required'"
              v-model.trim="item_add.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Rate name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Daily Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="daily_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Daily Rate"
              placeholder="Daily Rate"
              v-model.trim="item_add.daily_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('daily_rate')"
            >Daily rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Regular Over Time Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="regular_ot_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Regular Over Time Rate (per 30 min)"
              placeholder="Regular Over Time Rate (per 30 min)"
              v-model.trim="item_add.regular_ot_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('regular_ot_rate')"
            >Regular Over Time rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Holiday Over Time Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="holiday_ot_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Holiday Over Time Rate (per 30 min)"
              placeholder="Holiday Over Time Rate (per 30 min)"
              v-model.trim="item_add.holiday_ot_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('holiday_ot_rate')"
            >Holiday Over Time rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Regular Holiday Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="regular_holiday_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Regular Holiday Rate (per day)"
              placeholder="Regular Holiday Rate (per day)"
              v-model.trim="item_add.regular_holiday_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('regular_holiday_rate')"
            >Regular Holiday rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Special Holiday Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="special_holiday_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Special Holiday Rate (per day)"
              placeholder="Special Holiday Rate (per day)"
              v-model.trim="item_add.special_holiday_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('special_holiday_rate')"
            >Special Holiday rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Night Differencial:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="night_differencial"
              class="form-control"
              v-b-tooltip.hover
              title="Night Differencial (per 30 min)"
              placeholder="Night Differencial (per 30 min)"
              v-model.trim="item_add.night_differencial"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('night_differencial')"
            >Night Differencial is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Undertime Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="undertime_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Undertime Rate(per minute)"
              placeholder="Undertime Rate(per minute)"
              v-model.trim="item_add.undertime_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('undertime_rate')"
            >Undertime rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Late Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="late_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Late Rate(per minute)"
              placeholder="Late Rate(per minute)"
              v-model.trim="item_add.late_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('late_rate')"
            >Late rate is required.</small>
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{  }">
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
        title="Update Rates"
        @ok="handleOk"
      >
        <!-- form -->
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Rate Name:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="name"
              ref="name"
              class="form-control"
              v-b-tooltip.hover
              title="Name of rate"
              placeholder="Rate name"
              v-validate="'required'"
              v-model.trim="item_edit.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Rate name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Daily Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="daily_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Daily Rate"
              placeholder="Daily Rate"
              v-model.trim="item_edit.daily_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('daily_rate')"
            >Daily rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Regular Over Time Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="regular_ot_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Regular Over Time Rate"
              placeholder="Regular Over Time Rate"
              v-model.trim="item_edit.regular_ot_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('regular_ot_rate')"
            >Regular Over Time rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Holiday Over Time Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="holiday_ot_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Holiday Over Time Rate"
              placeholder="Holiday Over Time Rate"
              v-model.trim="item_edit.holiday_ot_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('holiday_ot_rate')"
            >Holiday Over Time rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Regular Holiday Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="regular_holiday_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Regular Holiday Rate"
              placeholder="Regular Holiday Rate"
              v-model.trim="item_edit.regular_holiday_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('regular_holiday_rate')"
            >Regular Holiday rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Special Holiday Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="special_holiday_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Special Holiday Rate"
              placeholder="Special Holiday Rate"
              v-model.trim="item_edit.special_holiday_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('special_holiday_rate')"
            >Special Holiday rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Night Differencial:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="night_differencial"
              class="form-control"
              v-b-tooltip.hover
              title="Night Differencial"
              placeholder="Night Differencial"
              v-model.trim="item_edit.night_differencial"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('night_differencial')"
            >Night Differencial is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Undertime Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="undertime_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Undertime Rate"
              placeholder="Undertime Rate"
              v-model.trim="item_edit.undertime_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('undertime_rate')"
            >Undertime rate is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Late Rate:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="late_rate"
              class="form-control"
              v-b-tooltip.hover
              title="Late Rate"
              placeholder="Late Rate"
              v-model.trim="item_edit.late_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('late_rate')"
            >Late rate is required.</small>
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{  }">
          <b-button size="sm" variant="success" @click="btnUpdate()">Update</b-button>
          <b-button size="sm" variant="danger" @click="btnDelete()">Delete</b-button>
        </template>
      </b-modal>
      <!-- End modalEdit -->
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "daily_rate", sortable: true },
        { key: "regular_ot_rate", sortable: true },
        { key: "special_holiday_rate", sortable: true },
        { key: "night_differencial", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      item_add: {
        name: "",
        daily_rate: "",
        regular_ot_rate: "",
        holiday_ot_rate: "",
        regular_holiday_rate: "",
        special_holiday_rate: "",
        night_differencial: "",
        undertime_rate: "",
        late_rate: ""
      },
      item_edit: {
        name: "",
        daily_rate: "",
        regular_ot_rate: "",
        holiday_ot_rate: "",
        regular_holiday_rate: "",
        special_holiday_rate: "",
        night_differencial: "",
        undertime_rate: "",
        late_rate: ""
      },
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    //this.roles = this.$global.getRoles();
    this.load_items("Rate");
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load_items(model) {
      this.$http.get("api/" + model).then(function(response) {
        this.items = response.body;
        this.tblisBusy = false;
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
                .put("api/Rate/" + this.item_edit.id, this.item_edit)
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
          this.$http
            .post("api/Rate", this.item_add)
            .then(response => {
              swal("Notification", "Added successfully", "success");
              this.items = response.body;
              this.totalRows = this.items.length;
              this.item_add = {
                name: "",
                daily_rate: "",
                regular_ot_rate: "",
                holiday_ot_rate: "",
                regular_holiday_rate: "",
                special_holiday_rate: "",
                night_differencial: "",
                undertime_rate: "",
                late_rate: ""
              };

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
            .delete("api/Rate/" + this.item_edit.id)
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
