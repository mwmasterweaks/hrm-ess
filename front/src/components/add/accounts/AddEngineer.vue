<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Add Engineer</p>
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
              title="Input the name of the Engineer"
              placeholder="Name of the Engineer"
              v-validate="'required'"
              v-model.trim="engineer.name"
              autocomplete="off"
              autofocus="on"
            >
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Position:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="position"
              class="form-control"
              v-b-tooltip.hover
              title="Input the position of engineer"
              placeholder="Engineer Position"
              v-validate="'required'"
              v-model.trim="engineer.position"
              autocomplete="off"
            >
            <small
              class="text-danger pull-left"
              v-show="errors.has('position')"
            >Position is required.</small>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="heading-elements">
          <button type="button" class="btn btn-success pull-right" v-on:click="addEngineer">ADD</button>
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
      engineer: {
        name: "",
        position: ""
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
    addEngineer() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/Engineer", this.engineer)
            .then(response => {
              swal(this.engineer.name, "Added successfully", "success");
              this.engineer.name = "";
              this.engineer.position = "";
              this.$global.setEngineer(Object.values(response.body)[1]);
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
