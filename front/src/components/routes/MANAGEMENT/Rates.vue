<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Manage Rates
          <b-button
            @click="openModalAdd"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-if="roles.create_rate"
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

      <!-- modalAdd ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
      >
        <template #modal-title>
          <span v-if="item.state == 'create'">Rate Form</span>
          <span v-else>Manage Rate</span>
        </template>
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
              v-model.trim="item.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')"
              >Rate name is required.</small
            >
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
              v-model.trim="item.daily_rate"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('daily_rate')"
              >Daily rate is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">SSS Deduction:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="sss_deduction"
              class="form-control"
              v-b-tooltip.hover
              title="Input SSS Deduction every payroll"
              placeholder="SSS Deduction"
              v-model.trim="item.sss_deduction"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('sss_deduction')"
              >SSS Deductions is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">PHIC Deduction:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="phic_deduction"
              class="form-control"
              v-b-tooltip.hover
              title="Input PHIC Deduction every payroll"
              placeholder="PHIC Deduction"
              v-model.trim="item.phic_deduction"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('phic_deduction')"
              >PHIC Deduction is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">HDMF Deduction:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="number"
              name="hdmf_deduction"
              class="form-control"
              v-b-tooltip.hover
              title="Input HDMF Deduction every payroll"
              placeholder="HDMF Deduction"
              v-model.trim="item.hdmf_deduction"
              v-validate="'required'"
              autocomplete="off"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('hdmf_deduction')"
              >HDMF Deduction is required.</small
            >
          </div>
        </div>
        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            @click="btnAdd()"
            v-if="item.state == 'create'"
            >Add</b-button
          >
          <span v-else>
            <b-button size="sm" variant="success" @click="btnUpdate()"
              >Update</b-button
            >
            <b-button size="sm" variant="danger" @click="btnDelete()"
              >Delete</b-button
            >
          </span>
        </template>
      </b-modal>
      <!-- End modalAdd -->
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
      user: {},
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "daily_rate", sortable: true },
        { key: "sss_deduction", sortable: true },
        { key: "phic_deduction", sortable: true },
        { key: "hdmf_deduction", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      item: {
        name: "",
        daily_rate: "",
        sss_deduction: "",
        phic_deduction: "",
        hdmf_deduction: "",
        state: "create"
      },
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
    this.roles = this.$global.getRoles();
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
      this.item = item;
      this.item.state = "update";
      this.$bvModal.show("modalAdd");
    },
    openModalAdd() {
      this.clearData();
      this.$bvModal.show("modalAdd");
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
              this.item.user_id = this.user.id;
              this.item.user_name = this.user.name;

              this.$http
                .put("api/Rate/" + this.item.id, this.item)
                .then(response => {
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  swal("Update!", "Update successfully", "success");
                  this.clearData();
                  this.$bvModal.hide("modalAdd");
                  this.tblisBusy = false;
                })
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  });
                  this.tblisBusy = false;
                });
            }
          });
        }
      });
    },
    btnAdd() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.item.user_id = this.user.id;
          this.item.user_name = this.user.name;

          this.$http
            .post("api/Rate", this.item)
            .then(response => {
              swal("Notification", "Added successfully", "success");
              this.items = response.body;
              this.totalRows = this.items.length;
              this.clearData();

              this.$bvModal.hide("modalAdd");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
              this.tblisBusy = false;
            });
        }
      });
    },
    clearData() {
      this.item = {
        name: "",
        daily_rate: "",
        sss_deduction: "",
        phic_deduction: "",
        hdmf_deduction: "",
        state: "create"
      };
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
          this.tblisBusy = true;
          this.$http
            .delete("api/Rate/" + this.item.id)
            .then(response => {
              this.$bvModal.hide("modalAdd");
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
              });
              this.tblisBusy = false;
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
