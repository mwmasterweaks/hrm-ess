<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">My profile</h6>
      </div>

      <div class="elClr panel-body">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">First Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.first_name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Middle Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.middle_name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Last Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.last_name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Gender:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.gender }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Date Hired:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.date_hired }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Group:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.group.name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Rate:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.rate.name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Position:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.position }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Branch:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.branch.name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Department:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.department.name }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Employee Status:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.employment_status }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Email:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.email }}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Created:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ user.created_at }}</span>
            </b>
          </div>
        </div>
        <br />
      </div>
      <div class="panel-footer">
        <div class="heading-elements">
          <!-- <b-form-input v-model="cmd" placeholder="Command"></b-form-input>
          <b-button @click="samp">send</b-button> -->
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
      user: {},
      cmd: ""
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    if (this.$keycloak.isTokenExpired()) {
      this.$root.$emit("logout");
    }
    this.user = this.$global.getUser();
    console.log(this.user);
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {
      // document.getElementById(
      //   "bgColor"
      // ).style.color = this.user.elBG;
    },
    samp() {
      this.$http.get("api/testfunc/" + this.cmd).then(function(response) {
        console.log(response.body);
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
