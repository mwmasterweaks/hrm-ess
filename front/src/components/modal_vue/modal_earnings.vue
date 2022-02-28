<template>
  <div>
    <b-modal
      id="modalEarning"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title
      ok-only
    >
      <!--Form-------->
      <div class="elBG panel">
        <div class="panel-heading">
          <p class="elClr panel-title">
            Manage Earnings for employee ID: {{ data.user.email }}
            <b-button
              type="button"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              v-b-modal="'modalAddEarning'"
            >
              <i class="fas fa-plus-square"></i>
            </b-button>
          </p>
        </div>
        <div class="elClr panel-body">
          <div>
            <b-table
              class="elClr"
              striped
              show-empty
              hover
              outlined
              :fields="fields"
              :items="data.earning"
              :busy="tblisBusy"
              :tbody-tr-class="tblRowClass"
              head-variant=" elClr"
              thead-class="cursorPointer-th"
              @row-clicked="tblRowClicked"
            >
              <div slot="table-busy" class="text-center text-danger my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>Loading...</strong>
              </div>
            </b-table>
          </div>
        </div>
      </div>
      <!--Form-------->
      <template slot="modal-footer" slot-scope="{}"></template>
    </b-modal>

    <b-modal
      id="modalAddEarning"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
    >
      <template #modal-title>
        <span v-if="item.state == 'create'">Earning From</span>
        <span v-else>Manage Earning</span>
      </template>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Earning Type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="earning_types"
            v-model="item.earning_type_id"
            option-value="id"
            option-text="name"
            placeholder="Select Earning type"
            name="earning_types"
            v-validate="'required'"
          ></model-list-select>
          <small
            class="text-danger pull-left"
            v-show="errors.has('earning_types')"
            >Earning type is required.</small
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Effective Date:</p>
        </div>
        <div class="col-lg-9">
          <date-picker
            v-model="item.effective_date"
            :config="Dateoptions"
            v-b-tooltip.hover
            title="First day of earnings"
            placeholder="Effective Date (yyyy-MM-dd)"
            autocomplete="off"
            v-validate="'required'"
            name="effective_date"
          ></date-picker>

          <small
            class="text-danger pull-left"
            v-show="errors.has('effective_date')"
            >Effective Date is required.</small
          >
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">End Date:</p>
        </div>
        <div class="col-lg-9">
          <date-picker
            v-model="item.end_date"
            :config="Dateoptions"
            v-b-tooltip.hover
            title="Last day of earnings"
            placeholder="End Date (yyyy-MM-dd)"
            autocomplete="off"
            name="end_date"
          ></date-picker>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Earning Amount:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="number"
            name="amount"
            v-validate="'required|between:1,1000000'"
            class="form-control"
            v-b-tooltip.hover
            title="Earning amount per payroll"
            placeholder="Amount"
            v-model.trim="item.amount"
            autocomplete="off"
          />
          <small class="text-danger pull-left" v-show="errors.has('amount')"
            >Input valid number.</small
          >
        </div>
      </div>

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
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import datePicker from "vue-bootstrap-datetimepicker";

export default {
  props: ["data"],
  components: {
    "model-list-select": ModelListSelect,
    "date-picker": datePicker
  },
  data() {
    return {
      user: {},
      fields: [
        { key: "type.name", label: "Type", sortable: true },
        { key: "effective_date", sortable: true },
        { key: "end_date", sortable: true },
        { key: "amount", sortable: true }
      ],
      item: {
        earning_type_id: "",
        effective_date: null,
        end_date: null,
        amount: 0,
        state: "create"
      },
      earning_types: [],
      tblisBusy: false,
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      }
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.load();
  },
  methods: {
    load() {
      this.$http.get("api/EarningType").then(function(response) {
        this.earning_types = response.body;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblRowClicked(item, index, event) {
      this.item = item;
      this.item.state = "update";
      this.$bvModal.show("modalAddEarning");
    },
    btnAdd() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.tblisBusy = true;
          this.item.employee_id = this.data.id;
          this.item.user_id = this.user.id;
          this.item.user_name =
            this.user.first_name + " " + this.user.last_name;

          this.$http
            .post("api/Earning", this.item)
            .then(response => {
              swal("Success!", "Item added successfully.", "success");
              this.data.earning = response.body;

              this.clearData();

              this.$bvModal.hide("modalAddEarning");
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },
    btnUpdate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          swal({
            title: "Confirmation",
            text: "Do you want to update this item?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            if (update) {
              this.item.user_id = this.user.id;
              this.item.user_name =
                this.user.first_name + " " + this.user.last_name;

              this.$http
                .put("api/Earning/" + this.item.id, this.item)
                .then(response => {
                  this.data.earning = response.body;
                  swal("Update!", "Update successfully", "success");
                  this.clearData();
                  this.$bvModal.hide("modalAddEarning");
                  this.tblisBusy = false;
                  this.$root.$emit("pageLoaded");
                })
                .catch(response => {
                  swal({
                    title: "Error",
                    text: response.body.error,
                    icon: "error",
                    dangerMode: true
                  });
                  this.tblisBusy = false;
                  this.$root.$emit("pageLoaded");
                });
            }
          });
        }
      });
    },
    btnDelete() {
      swal({
        title: "Confirmation",
        text: "Do you really want to delete this item permanently?",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          this.item.user_id = this.user.id;
          this.item.user_name =
            this.user.first_name + " " + this.user.last_name;
          this.$http
            .post("api/Earning/destroyItem", this.item)
            .then(response => {
              this.$bvModal.hide("modalAddEarning");
              this.data.earning = response.body;
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
              swal("Deleted!", "Item has been deleted", "success");
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              });
              this.tblisBusy = false;
              this.$root.$emit("pageLoaded");
            });
        }
      });
    },
    clearData() {
      this.item = {
        earning_type_id: "",
        effective_date: null,
        end_date: null,
        amount: 0,
        state: "create"
      };
    }
  }
};
</script>
<style scoped>
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.rowFields {
  margin-top: 15px;
}
</style>
