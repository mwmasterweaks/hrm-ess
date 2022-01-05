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
if (window.location.host == "hrmess.dctechmicro.com") {
  Vue.prototype.$url_back = protocol + "//hrmess.dctechmicro.com/back/";
  Vue.http.options.root = protocol + "//hrmess.dctechmicro.com/back/";
} else {
  Vue.prototype.$url_back = "http://localhost:8000";
  Vue.http.options.root = "http://localhost:8000";
}



let initOptions = {
  url: "https://apiauth.dctechmicro.com:8443/auth/",
  realm: 'DctecH APPS',
  clientId: 'PeterTest',
  onLoad: 'login-required'
}

let keycloak = Keycloak(initOptions);
console.log(keycloak);


// Vue.http.headers.common["Authorization"] = "Bearer " + Vue.auth.getToken();
// Vue.http.headers.common["Authorization"] = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImJlNzAwYTA1NTQ3YzI5ZThiZGJlZTNhZDE0MzgwZjVkODViZWJkMDRmYTEwYzcwYzBjYmIyMDFlYWViZmVmNThhOTZlNmQzMTYzMTQzMjkyIn0.eyJhdWQiOiIyIiwianRpIjoiYmU3MDBhMDU1NDdjMjllOGJkYmVlM2FkMTQzODBmNWQ4NWJlYmQwNGZhMTBjNzBjMGNiYjIwMWVhZWJmZWY1OGE5NmU2ZDMxNjMxNDMyOTIiLCJpYXQiOjE2Mzg1OTY4OTMsIm5iZiI6MTYzODU5Njg5MywiZXhwIjoxNjcwMTMyODkzLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.SLbv3qZyCotBC_I-zh3-T6wd25OZ2HX6F9wbm-H6EKJEDoQaivHC515lU4-wG48k4XcAEYbaqGk5t9yd2ifJxqqFT6ciA3LFqi8_JFkUI--BiDe3sANtRDTI7TQp93LdHbmYeWOvfJqb4jAs0f9JZKPVzNtdzTCMBei1sRdvrIPgE7lA_v4E-VCrCYZnbRwFCVOfcOBI6SwWSAjKpY_kr5b-7GiST1B_d8UqAzAJ1q5x7EwDkBupeVVm9vfqGuCnRO2Wwt-p1q5S7MWzcPc3BDLcCUh7et9LoTOJV14wD3MQKRd8W1MbpxiSPfl-l2JL6W7pT6mOQj7FbIRBdtHei4azUP6gEl9oQvPo2yb4SL2Ov3b33QguQYIsDD9Nb0YghdSRFAqG6CrO8k6LKOKpd1tp00UV6jLinoBE7rUqjj_0AQKQe9dgD9DvWXKx21FSfOADZVA9XLYQV5G3SLgucELGicoYp8gGvBmYHZValLw-QcYqG4mKX6jAy_7hXqDR7dR_L5RB3tQFGUDV3TNfetL9MweserozH5udPK0FdKht0fQJYGKXn6Y5-FpxKNXfj004PuhegLzFfgL-NrZAxOZyqx5i-3MO_ouCHaCjZ4Q_NdTKOJCIZ4qS46skYSJPJ3i9eU2lpzD3cDXbY7kMD2Uf0b_UIdluoYRlqPuSkHo";
// Router.beforeEach((to, from, next) => {
//   if (to.matched.some(record => record.meta.forVisitors)) {
//     if (Vue.auth.isAuthenticated()) {
//       next({
//         path: "/home"
//       });
//     } else next();
//   } else if (to.matched.some(record => record.meta.forAuth)) {
//     if (!Vue.auth.isAuthenticated()) {
//       next({
//         path: "/login"
//       });
//     } else next();
//   } else next();
// });

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


