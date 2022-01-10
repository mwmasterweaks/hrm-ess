<template>
  <div class>
    <nav
      class="navbar navbar-expand-lg mainnav"
      style="background-color: #076c2d; z-index:1001;"
    >
      <router-link class to="/home">
        <a
          class
          href="javascript:void(0);"
          alt="Dashboard"
          style="margin-left: 10px;"
        >
          <!-- <span class="textColor systemName">HRM-Employee Self Service</span> -->
          <img src="../img/logo1.png" style="width:350px;" />
          <!-- <img src="../img/logo1.png" style="width:225px;" /> -->
          <!-- <span class="textColor systemName">EMPLOYEE SELF SERVICE</span> -->
        </a>
      </router-link>

      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="navbarDropdown"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <span class="textColor" style="color: black;">
                {{ tokenParsed.firstName }}
                {{ tokenParsed.family_name }}
              </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <router-link class="dropdown-item" tag="a" to="/Profile">
                <i class="far fa-id-card" style="font-size:18px"></i> My profile
              </router-link>

              <div class="dropdown-divider"></div>

              <router-link class="dropdown-item" tag="a" to="/AccountSettings">
                <i style="font-size:18px" class="fa fa-cog fa-spin"></i>
                Account settings
              </router-link>
              <router-link class="dropdown-item" tag="a" to="/Suggestions">
                <i style="font-size:18px" class="fas fa-comment-alt"></i>
                Suggestions
              </router-link>
              <router-link class="dropdown-item" tag="a" to="/Help">
                <i style="font-size:18px" class="fas fa-medkit"></i>
                Help
              </router-link>
              <a
                class="dropdown-item"
                href="javascript:void(0);"
                @click="logout"
              >
                <img
                  class="iconSize"
                  src="../img/logout.png"
                  style="font-size:18px"
                />
                Log Out
              </a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</template>
<script>
export default {
  data() {
    return {
      tokenParsed: null,
      email: "",
      colorSelect: "",
      authenticatedUser: {},
      roles: [],
      bg: "white",
      cl: "black"
    };
  },
  beforeCreate() {},
  created() {
    this.tokenParsed = this.$keycloak.tokenParsed;
    this.setAuthenticatedUser();
    this.$global.elBG(this.bg);
    this.$global.elClr(this.cl);
    this.load();
  },
  mounted() {},
  methods: {
    load() {},
    logout() {
      this.$root.$emit("logout");
    },
    setAuthenticatedUser() {
      this.authenticatedUser = this.$global.getUser();
    },
    getUserRoles() {
      this.$http.get("api/Role/" + this.authenticatedUser.id).then(response => {
        this.$global.setRoles(response.body.roles);
      });
    },
    test() {
      console.log(this.colorSelect);
    },
    ChangeColor(color) {
      document.body.style.backgroundColor = color;
      document.getElementById("displayColor").style.backgroundColor = color;
      document.getElementById("colorName").innerHTML =
        "Background color: " + color.charAt(0).toUpperCase() + color.slice(1);
    },
    ChangeColorText(color) {
      document.body.style.color = color;
      document.getElementById("displayColorText").style.backgroundColor = color;
      document.getElementById("colorNameText").innerHTML =
        "Text color: " + color.charAt(0).toUpperCase() + color.slice(1);
    }
  }
};
</script>
<style scope>
.mainnav {
  /* background: linear-gradient(#076c2d, #1fa753, #076c2d); */
  background: green;
}
.systemName {
  color: white;
  font-family: "Patua One", cursive;
}
</style>
