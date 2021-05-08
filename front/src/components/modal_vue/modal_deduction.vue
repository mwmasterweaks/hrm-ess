<template>
  <div>
    <b-modal
      id="modalDeduction"
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
            Manage Deduction for employee ID: {{ data.user.email }}
            <b-button
              type="button"
              class="btn btn-success btn-labeled pull-right margin-right-10"
              v-b-modal="'modalAddDeduction'"
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
              :items="data.deduction"
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
      id="modalAddDeduction"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
    >
      <template #modal-title>
        <span v-if="item.state == 'create'">Deduction From</span>
        <span v-else>Manage Deduction</span>
      </template>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Deduction Type:</p>
        </div>
        <div class="col-lg-9">
          <model-list-select
            :list="deduction_types"
            v-model="item.deduction_type_id"
            option-value="id"
            option-text="name"
            placeholder="Select Deduction type"
            name="deduction_types"
            v-validate="'required'"
          ></model-list-select>
          <small
            class="text-danger pull-left"
            v-show="errors.has('deduction_types')"
            >Deduction type is required.</small
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
            title="First day of the deduction"
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
            title="Last day of the deduction"
            placeholder="End Date (yyyy-MM-dd)"
            autocomplete="off"
            name="end_date"
          ></date-picker>
        </div>
      </div>

      <div class="rowFields mx-auto row">
        <div class="col-lg-3">
          <p class="textLabel">Deduction Amount:</p>
        </div>
        <div class="col-lg-9">
          <input
            type="number"
            name="amount"
            v-validate="'required|between:1,1000000'"
            class="form-control"
            v-b-tooltip.hover
            title="Deduction amount per payroll"
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
      fields: [
        { key: "type.name", label: "Type", sortable: true },
        { key: "effective_date", sortable: true },
        { key: "end_date", sortable: true },
        { key: "amount", sortable: true }
      ],
      item: {
        deduction_type_id: "",
        effective_date: null,
        end_date: null,
        amount: 0,
        state: "create"
      },
      deduction_types: [],
      tblisBusy: false,
      Dateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      }
    };
  },
  created() {
    this.load();
    this.user = this.$global.getUser();
  },
  methods: {
    load() {
      this.$http.get("api/DeductionType").then(function(response) {
        this.deduction_types = response.body;
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblRowClicked(item, index, event) {
      this.item = item;
      this.item.state = "update";
      this.$bvModal.show("modalAddDeduction");
    },
    btnAdd() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$root.$emit("pageLoading");
          this.tblisBusy = true;
          this.item.employee_id = this.data.id;

          var tempdata = {
            item: this.item,
            user_id: this.user.id,
            user_name: this.user.name
          };

          this.$http
            .post("api/Deduction", tempdata)
            .then(response => {
              swal("Notification", "Added successfully", "success");
              this.data.deduction = response.body;

              this.clearData();

              this.$bvModal.hide("modalAddDeduction");
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
            title: "Are you sure?",
            text: "Do you want to Update this item?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            if (update) {
              var tempdata = {
                item: this.item,
                user_id: this.user.id,
                user_name: this.user.name
              };

              this.$http
                .put("api/Deduction/" + this.item.id, tempdata)
                .then(response => {
                  this.data.deduction = response.body;
                  swal("Update!", "Update successfully", "success");
                  this.clearData();
                  this.$bvModal.hide("modalAddDeduction");
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
        title: "Are you sure?",
        text: "Do you really want to delete this item permanently",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.tblisBusy = true;
          this.$root.$emit("pageLoading");
          this.$http
            .post("api/Deduction/destroyItem", this.item)
            .then(response => {
              this.$bvModal.hide("modalAddDeduction");
              this.data.deduction = response.body;
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
        deduction_type_id: "",
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
