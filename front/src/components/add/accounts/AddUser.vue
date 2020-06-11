<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Add User</p>
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
              title="Input the name of the user"
              placeholder="Name of the user"
              v-validate="'required'"
              v-model.trim="user.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Branch:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="branches"
              v-model="user.branch_id"
              option-value="id"
              option-text="name"
              name="branch"
              ref="branch"
              placeholder="Select/Search a Branch..."
              v-validate="'required'"
            ></model-list-select>
            <small class="text-danger pull-left" v-show="errors.has('branch')">Branch is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Email:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="email"
              class="form-control"
              v-b-tooltip.hover
              title="Input the Email address of the user"
              placeholder="Email Address"
              v-validate="{ required: true, email: true }"
              v-model.trim="user.email"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('email')">Email is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              name="password"
              ref="password"
              class="form-control"
              v-b-tooltip.hover
              title="Input the password of the user"
              placeholder="Password"
              autocomplete="off"
              v-validate="{ required: true, min: 8 }"
              v-model.trim="user.password"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('password')"
            >{{ errors.first('password') }}</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Re-type Password:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="password"
              name="retype"
              class="form-control"
              v-b-tooltip.hover
              title="Please re-type the pasword of the user"
              placeholder="Re-type password"
              v-validate="'required|confirmed:password'"
              v-model.trim="user.password2"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('retype')"
            >The password confirmation does not match.</small>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="heading-elements">
          <button type="button" class="btn btn-success btn-labeled pull-right" v-on:click="addUser">
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

import { ModelListSelect } from "vue-search-select";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      user: {
        name: "",
        branch_id: "",
        email: "",
        password: "",
        password2: ""
      },
      branches: ""
    };
  },

  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.branches = this.$global.getBranch();
  },
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
    addUser() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$http
            .post("api/user", this.user)
            .then(response => {
              swal(this.user.name, "Added successfully", "success");
              this.user.name = "";
              this.user.branch_id = "";
              this.user.email = "";
              this.user.password = "";
              this.user.password2 = "";
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
