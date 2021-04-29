<template>
  <div class="mx-auto col-md-12">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Payslip</p>
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
    </div>

    <b-modal
      id="ModalMyPayslip"
      :header-bg-variant="' elBG'"
      :header-text-variant="' elClr'"
      :body-bg-variant="' elBG'"
      :body-text-variant="' elClr'"
      :footer-bg-variant="' elBG'"
      :footer-text-variant="' elClr'"
      size="xl"
      title="My Payslip"
    >
      <payslip_table v-bind:payslip="payslip"></payslip_table>
    </b-modal>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import payslip_table from "../../others/payslip_table.vue";

export default {
  components: {
    payslip_table,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      tblisBusy: false,
      fields: [
        { key: "pay_period.period", label: "Pay Period", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      branch: {
        name: ""
      },
      payslip: {},
      user: {},
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    console.log(this.user);
    this.load();
  },
  mounted() {},
  updated() {},
  methods: {
    load() {
      this.tblisBusy = true;
      this.$http
        .get("api/Payslip/" + this.user.employee_id)
        .then(function(response) {
          console.log(response.body);
          this.items = response.body;
          this.tblisBusy = false;
          this.totalRows = this.items.length;
        });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else if (this.roles.update_branch) {
        return "elClr cursorPointer";
      } else return "elClr";
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
      console.log(item);
      this.payslip = item;
      this.$bvModal.show("ModalMyPayslip");
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
