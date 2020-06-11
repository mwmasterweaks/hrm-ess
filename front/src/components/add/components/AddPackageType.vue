<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">Add Package Type</h6>
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
              title="Input the package type"
              placeholder="Package type"
              v-validate="'required'"
              v-model.trim="pack.name"
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
            v-on:click="addPackageType"
          >ADD</button>
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
      pack: {
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
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu";
        }, 100);
      });
    },
    addPackageType() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/PackageType", this.pack)
            .then(response => {
              swal(this.pack.name, "Added successfully", "success");
              this.pack.name = "";
              this.$global.setPackageTypes(Object.values(response.body)[1]);
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
