export default function(Vue) {
  Vue.global = {
    setRoles(param1) {
      localStorage.setItem("_Roles", JSON.stringify(param1));
    },
    getRoles() {
      return JSON.parse(localStorage.getItem("_Roles"));
    },
    //--------------------------------------------------------------
    setUser(param1) {
      localStorage.setItem("_User", JSON.stringify(param1));
    },
    getUser() {
      return JSON.parse(localStorage.getItem("_User"));
    },
    //--------------------------------------------------------------
    setEmail(param1) {
      localStorage.setItem("_email", JSON.stringify(param1));
    },
    getEmail() {
      return JSON.parse(localStorage.getItem("_email"));
    },
    //--------------------------------------------------------------
    set(param1) {
      localStorage.setItem("_", JSON.stringify(param1));
    },
    get() {
      return JSON.parse(localStorage.getItem("_"));
    },
    //--------------------------------------------------------------
    destroyGlobal() {
      localStorage.removeItem("_packages");
      localStorage.removeItem("_modems");
      localStorage.removeItem("_PackageTypes");
      localStorage.removeItem("_Clients");
      localStorage.removeItem("_Sales");
      localStorage.removeItem("_Roles");
      localStorage.removeItem("_Engineer");
      localStorage.removeItem("_Branch");
      localStorage.removeItem("_User");
      localStorage.removeItem("_email");
      localStorage.removeItem("_Schedule");
      localStorage.removeItem("_TicketStatus");
      localStorage.removeItem("_Ticket");
      localStorage.removeItem("_OLT");
      localStorage.removeItem("_PON");
      localStorage.removeItem("_");
    },

    elClr(color) {
      var sheet = window.document.styleSheets[0];
      sheet.insertRule(
        ".elClr { color: " + color + "; }",
        sheet.cssRules.length
      );
      localStorage.setItem("_color", JSON.stringify(color));
    },
    getColor() {
      return JSON.parse(localStorage.getItem("_color"));
    },
    elBG(color) {
      var sheet = window.document.styleSheets[0];
      sheet.insertRule(
        ".elBG { background-color: " + color + "; }",
        sheet.cssRules.length
      );
      localStorage.setItem("_bgColor", JSON.stringify(color));
    },
    getbgColor() {
      return JSON.parse(localStorage.getItem("_bgColor"));
    },
    loadJS() {
      //   var plugin = document.createElement("script");
      //   plugin.setAttribute(
      //     "src",
      //     "src/plugins/assets/js/plugins/forms/styling/uniform.min.js"
      //   );
      //   plugin.async = true;
      //   document.head.appendChild(plugin);
      //   plugin = document.createElement("script");
      //   plugin.setAttribute("src", "src/plugins/assets/js/core/app.js");
      //   plugin.async = true;
      //   document.head.appendChild(plugin);
      //   plugin = document.createElement("script");
      //   plugin.setAttribute("src", "src/plugins/assets/js/pages/form_inputs.js");
      //   plugin.async = true;
      //   document.head.appendChild(plugin);
    }
  };

  Object.defineProperties(Vue.prototype, {
    $global: {
      get() {
        return Vue.global;
      }
    }
  });
}
