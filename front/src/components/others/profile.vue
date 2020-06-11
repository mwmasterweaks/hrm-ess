<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">My profile</h6>
      </div>

      <div class="elClr panel-body">
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Name:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ authenticatedUser.name}}</span>
            </b>
          </div>
        </div>
        <br />
        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Email:</div>
          <div class="col-lg-9">
            <b>
              <span>{{ authenticatedUser.email}}</span>
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

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Background color:</div>
          <div class="col-lg-9">
            <b>
              <span id="bgColor">{{ authenticatedUser.elBG}}</span>
            </b>
          </div>
        </div>
        <br />

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">Text color:</div>
          <div class="col-lg-9">
            <b>
              <span id="txColor">{{ authenticatedUser.elClr}}</span>
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
