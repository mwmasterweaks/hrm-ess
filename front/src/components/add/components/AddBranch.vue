<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Add Branch</p>
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
              title="Input the name of the branch"
              placeholder="Name of the branch"
              v-validate="'required'"
              v-model.trim="branch.name"
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
            v-on:click="addBranch"
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
      branch: {
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
    addBranch() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/Branch", this.branch)
            .then(response => {
              swal(this.branch.name, "Added successfully", "success");
              this.branch.name = "";
              this.$global.setBranch(Object.values(response.body)[1]);
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
