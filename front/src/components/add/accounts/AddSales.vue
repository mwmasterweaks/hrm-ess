<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">Add Sales</h6>
      </div>

      <div class="elClr panel-body">
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
              title="Input the name of the seller"
              placeholder="Name of the seller"
              v-validate="'required'"
              v-model.trim="sales.name"
              autocomplete="off"
              autofocus="on"
            >
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="heading-elements">
          <button
            type="button"
            class="btn btn-success btn-labeled pull-right"
            v-on:click="addSales"
          >
            <b>
              <i class="glyphicon glyphicon-plus"></i>
            </b>ADD
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import swal from "sweetalert";
export default {
  data() {
    return {
      sales: {
        name: ""
      }
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {},
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("accountsMenu").className =
            "customeDropDown dropdown-menu";
        }, 100);
      });
    },
    addSales() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/Sales", this.sales)
            .then(response => {
              swal(this.sales.name, "Added successfully", "success");
              this.sales.name = "";
              this.$global.setSales(Object.values(response.body)[1]);
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
</style>
