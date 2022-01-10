import Vue from "vue";
import App from "./App.vue";
import Router from "./routes.js";
import VueResource from "vue-resource";
import VeeValidate from "vee-validate";
import VueRangedatePicker from "vue-rangedate-picker";
import Auth from "./packages/auth/Auth.js";
import Global from "./packages/local/Global.js";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import VueSidebarMenu from "vue-sidebar-menu";
import "vue-sidebar-menu/dist/vue-sidebar-menu.css";
import * as VueGoogleMaps from 'vue2-google-maps';
import VueGeolocation from 'vue-browser-geolocation';
import Keycloak from 'keycloak-js';

//import excel from "vue-excel-export";

//import DateRangePicker from "vue2-daterange-picker";

Vue.use(VueSidebarMenu);
Vue.use(VueRangedatePicker);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueResource);
Vue.use(Auth);
Vue.use(Global);
Vue.use(VueGeolocation);
Vue.use(VeeValidate, {
  fieldsBagName: "veeFields"
});
Vue.use(VueGoogleMaps, {
  load: {
    key: "AIzaSyCCvHdNEai5B7i5fXOhgl0nvz_eYLBn_tE"
  }
});

var protocol = window.location.protocol;
console.log(protocol);
console.log(window.location.host);
var clientID = "KateTest"
if (window.location.host == "hrmess.dctechmicro.com") {
  Vue.prototype.$url_back = protocol + "//hrmess.dctechmicro.com/back/";
  Vue.prototype.$url_attachments = protocol + "//hrmess.dctechmicro.com/back/public/attachments/";
  Vue.http.options.root = protocol + "//hrmess.dctechmicro.com/back/";
  clientID = "hrmess";
} else {
  Vue.prototype.$url_back = "http://localhost:8000";
  Vue.prototype.$url_attachments = "http://localhost:8000";
  Vue.http.options.root = "http://localhost:8000";
}


console.log(clientID);
let initOptions = {
  url: "https://apiauth.dctechmicro.com:8443/auth/",
  realm: 'DctecH APPS',
  clientId: clientID,
  onLoad: 'login-required'
}

let keycloak = Keycloak(initOptions);
// console.log(keycloak);


Vue.prototype.$keycloak = keycloak;
keycloak.init({ onLoad: initOptions.onLoad }).then((auth) => {
  if (!auth) {
    window.location.reload();
  } else {
    Vue.http.headers.common["Authorization"] = "Bearer " + keycloak.token;
    new Vue({
      el: "#app",
      render: h => h(App),
      router: Router
    });
  }
}).catch(() => {
  alert("Can't connect to keycloak");
});


