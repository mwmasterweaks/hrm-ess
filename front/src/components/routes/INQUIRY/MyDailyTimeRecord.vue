<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">My Daily Time Record</p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Period" class="mb-0">
                <b-input-group>
                  <model-list-select
                    :list="pay_period_list"
                    v-model="pay_period_select"
                    option-value="id"
                    option-text="period"
                    placeholder="Select pay period"
                    name="pay_period_list"
                    @input="pay_period_onchange"
                    v-validate="'required'"
                  ></model-list-select>
                  <!-- <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''">Clear</b-button>
                  </b-input-group-append>-->
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

            <template v-slot:cell(shift_sched)="data">
              <span v-html="data.value"></span>
            </template>
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
      tblisBusy: false,
      fields: [
        { key: "work_date", sortable: true },
        { key: "day", sortable: true },
        {
          key: "shift_sched",
          label: "Shift Sched",
          formatter: (value, key, item) => {
            if (item.is_rest_day == 1)
              return "<p class='text-danger'>Rest Day</p>";
            else if (item.is_rest_day == 2)
              return "<p class='text-danger'>Leave</p>";
            else if (item.is_rest_day == 3)
              return "<p class='text-danger'>Holiday</p>";
            else return item.shift_sched_in + " - " + item.shift_sched_out;
          },
          sortable: true
        },
        { key: "time_in", sortable: true },
        { key: "time_out", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 25,
      pageOptions: [10, 25, 50, 100],
      user: {},
      pay_period_list: [],
      pay_period_select: "",
      roles: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    //this.roles = this.$global.getRoles();
    this.user = this.$global.getUser();
    this.load_pay_period();
  },
  mounted() {},
  updated() {},
  methods: {
    load_leave_balance(id) {
      this.$http.get("api/LeaveBalance/" + id).then(function(response) {
        this.items = response.body;
        this.tblisBusy = false;
      });
    },
    load_pay_period() {
      this.$http.get("api/PayPeriod").then(function(response) {
        this.pay_period_list = response.body;
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
    pay_period_onchange() {
      this.tblisBusy = true;
      this.$http
        .get(
          "api/getDTR/" + this.pay_period_select + "/" + this.user.employee_id
        )
        .then(function(response) {
          this.items = response.body;
          this.tblisBusy = false;
          this.totalRows = this.items.length;
          this.currentPage = 1;
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
