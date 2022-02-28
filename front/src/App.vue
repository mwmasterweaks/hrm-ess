<template>
  <div id="app">
    <login v-if="!isAuth"></login>

    <div v-if="isLoad == 6">
      <my-navbar></my-navbar>
      <sidebar ref="load"></sidebar>
      <div class="container">
        <router-view></router-view>
      </div>
    </div>
    <transition name="bounce">
      <div class="elBG divLoaderDiv" v-if="isAuth && isLoad != 6">
        <img src="./img/loading.gif" class="imgLoader" />
      </div>
    </transition>
    <div class="busyPageLoader" v-if="pageBusy">
      <img src="./img/loading.gif" class="imgLoader" />
    </div>
  </div>
</template>

<script>
import Navbar from "./components/Navbar.vue";
import Sidebar from "./components/Sidebar.vue";
import Login from "./components/user/Login.vue";
import Register from "./components/user/Register.vue";
import Keycloak from "keycloak-js";

export default {
  components: {
    "my-navbar": Navbar,
    sidebar: Sidebar,
    login: Login,
    register: Register
  },
  data() {
    return {
      tokenParsed: null,
      pageBusy: false,
      isAuth: null,
      isLoad: 0,
      user: [],
      email: ""
    };
  },

  created() {
    this.tokenParsed = this.$keycloak.tokenParsed;
    this.isAuth = this.$keycloak.authenticated;
    console.log(this.$keycloak);
    this.load();
  },
  mounted() {
    this.$root.$on("pageLoading", () => {
      this.pageBusy = true;
    });
    this.$root.$on("pageLoaded", () => {
      this.pageBusy = false;
    });
    this.$root.$on("logout", () => {
      this.logout();
    });
  },
  methods: {
    updateCountNotif() {
      vm.$refs.load.load();
    },
    load() {
      /*this.$http.post("api/oauth/token2", this.tokenParsed).then(response => {
        console.log(response);
      }); */

      this.$http
        .post("api/user/getUser", this.tokenParsed)
        .then(response => {
          console.log(response.body);
          this.user = response.body;
          this.$global.setUser(response.body);
          // this.user = this.$global.getUser();
          this.isLoad += 1;
          this.$global.setRoles(this.user.roles);
          this.roles = this.user.roles;
          this.getData();
        })
        .catch(response => {
          console.log(response.body);
          var res =
            "KeycloakGuard\\Exceptions\\ResourceAccessNotAllowedException";
          if (response.body.exception == res) {
            swal({
              title: "You are not allowed to access this site.",
              text: "Contact the administrator to obtain permission.",
              icon: "error",
              dangerMode: true
            }).then(ok => {
              this.logout();
            });
          } else {
            console.log(response);
            swal({
              title: "You are not authenticated.",
              text: "Please login again",
              icon: "error",
              dangerMode: true
            }).then(ok => {
              this.logout();
            });
          }
        });
    },
    getData() {
      this.$global.setEmail(this.tokenParsed.preferred_username);

      this.$http.get("api/Group").then(function(response) {
        this.$global.setGroup(response.body);
        this.isLoad += 1; //1
      });

      this.$http.get("api/Position").then(function(response) {
        this.$global.setPosition(response.body);
        this.isLoad += 1; // 2
      });

      this.$http.get("api/Branch").then(function(response) {
        this.$global.setBranch(response.body);
        this.isLoad += 1; // 3
      });

      this.$http.get("api/Department").then(function(response) {
        this.$global.setDepartment(response.body);
        this.isLoad += 1; //4
      });

      this.$http.get("api/LeaveType").then(function(response) {
        this.$global.setLeaveType(response.body);
        this.isLoad += 1; //5
      });
    },
    logout() {
      this.$global.destroyGlobal();
      this.$auth.destroyToken();
      this.$keycloak.logout({
        redirectURL: "http://10.254.11.111:3000/"
      });
    }
  }
};
</script>

<style>
.displayColor {
  height: 15px;
  width: 15px;
  margin-top: 2px;
  margin-right: 8px;
  border: 1px solid black;
  /* color: #c03434; */
}
body {
  background-color: #f3f3f3;
  color: black;
  font-size: 14px;
  line-height: 1;
}
.btn {
  font-size: 14px;
  padding: 3px;
}
.form-control {
  padding: 3px;
  height: 2.2em;
  font-size: 14px;
}
input {
  font-size: 14px;
  padding: 3px;
  height: 2.2em;
}
.menu-heading {
  font-size: 14px;
}
.dropdown-menu {
  font-size: 14px;
}
.textColor {
  color: rgb(0, 0, 0);
}
.ml-auto .dropdown-menu {
  left: auto !important;
  right: 0px;
}
.iconSize {
  font-size: 18px;
  width: 18px;
  height: 18px;
}
.panel {
  margin-top: 30px;
  margin-bottom: 20px;
  border-color: #ddd;
  border: 1px solid #ddd;
  border-top-right-radius: 6px;
  border-top-left-radius: 6px;
  border-bottom-right-radius: 6px;
  border-bottom-left-radius: 6px;
}
.panel.has-scroll {
  max-width: 100%;
  overflow-x: auto;
}
.panel-heading {
  position: relative;
  padding: 15px;
  height: auto;
  background-color: rgba(0, 128, 0, 0.829);
}
.panel-body {
  position: relative;
  padding: 10px;
  padding-top: 20px;
  padding-bottom: 20px;

  /* background: linear-gradient(#eeefef, #ffffff 50%); */
}
.panel-footer {
  position: relative;
  padding: 10px;
  border-bottom-right-radius: 6px;
  border-bottom-left-radius: 6px;
  border-top: 1px solid #eeeeee;

  /* background: linear-gradient(#eeefef, #ffffff 80%); */
}
.panel-title {
  position: relative;
  font-size: 17px;
  color: rgba(255, 255, 255, 1);
}
.textLabel {
  margin-top: 9px;
  font-size: 12px;
}
.textLabel1 {
  font-size: 12px;
}
.tbl-display > tbody > tr > td {
  font-size: 12px;
  padding: 3px;
}

.panel > .panel-heading {
  padding-top: 20px;
  padding-bottom: 20px;
  color: royalblue;
}
.panel > .panel-heading > .panel-title {
  margin-top: 2px;
  margin-bottom: 2px;
}
.heading-elements {
  background-color: inherit;
  position: absolute;
  right: 20px;
  height: 36px;
  margin-top: -18px;
}
@media (max-width: 768px) {
  .heading-elements:not(.not-collapsible) {
    position: static;
    margin-top: 0;
    height: auto;
  }
}
.panel-footer > .heading-elements {
  position: static;
  margin-top: 0;
  padding-right: 20px;
}
.panel-footer > .heading-elements:after {
  content: "";
  display: table;
  clear: both;
}
.centerElem {
  margin: 0;
  position: relative;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.cursorPointer {
  cursor: pointer;
}
.cursorPointer-th > tr > th {
  cursor: pointer;
}
.cursorPointer-th > tr > th:hover {
  text-decoration: none;
  background-color: #f5f5f5;
  color: #2196f3;
}
.imgLoader {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.btn1 {
  background-color: #f27474;
}
.bgOrange {
  background-color: orange;
}
.divLoader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 999999;
}
.divLoaderDiv {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10000;
}
.scrollmenu {
  overflow: auto;
  white-space: nowrap;
  padding: 10px;
}
.borderbot-1 {
  border-bottom: solid black 1px;
}
.bordertop-2 {
  border-top: solid black 2px;
}
.borderleft-2 {
  border-left: solid black 2px;
}

.modified-continer {
  position: absolute;
  left: 60px;
  width: 95%;
}

.modified-row {
  position: absolute;
  width: 80%;
  left: 50%;
  transform: translate(-50%);
}

.marginice {
  margin: 15px;
  padding: 8px;
}
.checkboxStyle {
  margin-top: 4px;
  margin-right: 25px;
  margin-left: 25px;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.bounce-enter-active {
  animation: bounce-in 1s;
}
.bounce-leave-active {
  animation: bounce-in 1s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(2);
  }
  100% {
    transform: scale(1);
  }
}

.bd_style1 {
  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  background: linear-gradient(#eeefef, #eeefef 10%);
}

.bd_style2 {
  box-shadow: 0px 0px 6px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1);
  background: linear-gradient(#ffffff, #eeefef 80%);
}

.bd_style3 {
  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  background: linear-gradient(#eeefef, #ffffff 80%);
}

.bd_style4 {
  box-shadow: 0px 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255, 255, 255, 1),
    inset 0 0px 6px rgba(0, 0, 0, 0.25);
  background: linear-gradient(#ffffff, #eeefef 95%);
}

.vsm--list {
  padding-top: 75px;
}
.v-sidebar-menu {
  background: #0e0e0e;
}
#to-approve {
  visibility: hidden;
}
#ap-to-approve {
  visibility: hidden;
}
.my-td {
  padding: 5px;
}
.my-table,
.my-td {
  border: 1px solid slategrey;
}
.my-table {
  border-collapse: collapse;
  width: 100%;
}
.busyPageLoader {
  background-color: rgba(192, 192, 192, 0.3);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 5000;
}
.cml-10 {
  margin-left: 10px;
}
.cplr-10 {
  padding-left: 10px;
  padding-right: 10px;
}
.cbg-gray {
  background: #303030;
}
.btn-default {
  border: none;
  border-radius: 3px;
  color: #e9e9e9;
}
.btn-default:hover {
  background: #1a1a1a;
}
.btn-default:focus {
  box-shadow: none;
  outline: 0;
}
</style>
