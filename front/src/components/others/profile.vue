<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">My profile</h6>
      </div>

      <div class="elClr panel-body">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3"> First Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.first_name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Middle Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.middle_name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Last Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.last_name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Gender:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.gender}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Date Hired:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.date_hired}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Group:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.group.name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Rate:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.rate.name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Position:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.position.name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Branch:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.branch.name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Department:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.department.name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Employee Status:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.employee.employment_status}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Email:</div>
          <div class="col-lg-9">
            <b>
              <span>{{authenticatedUser.email}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Created:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ authenticatedUser.created_at}}</span>
            </b>
          </div>
        </div>
        <br />
              
      </div>
      <div class="panel-footer">
        <div class="heading-elements"></div>
      </div>
    </div>
  </div>
</template>
<script>


import swal from "sweetalert";
export default {
  data() {
    return {
      authenticatedUser: {}
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.authenticatedUser = this.$global.getUser();
    console.log(this.authenticatedUser);
    
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {
      // document.getElementById(
      //   "bgColor"
      // ).style.color = this.authenticatedUser.elBG;
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
