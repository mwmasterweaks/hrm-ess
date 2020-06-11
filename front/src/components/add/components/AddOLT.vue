<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Add OLT</p>
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
              title="Input the name of the olt"
              placeholder="Name of the olt"
              v-validate="'required'"
              v-model.trim="olt.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">IP:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="ip"
              ref="ip"
              class="form-control"
              v-b-tooltip.hover
              title="Input the ip of the olt"
              placeholder="IP of the OLT"
              v-validate="'required'"
              v-model.trim="olt.ip"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('ip')">IP is required.</small>
          </div>
        </div>
      </div>

      <div class="panel-footer">
        <div class="heading-elements">
          <button
            type="button"
            class="btn btn-success btn-labeled pull-right"
            v-on:click="addClick"
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
      olt: {
        name: "",
        ip: ""
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
    addClick() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/olt", this.olt)
            .then(response => {
              swal(this.olt.name, "Added successfully", "success");
              this.olt.name = "";
              this.olt.ip = "";
              this.$global.setOLT(Object.values(response.body)[1]);
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
