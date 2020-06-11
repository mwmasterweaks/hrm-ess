<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">Add Client</p>
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
              class="form-control"
              v-b-tooltip.hover
              title="Input the name of the client"
              placeholder="Name of Client"
              v-model.trim="client.name"
              v-validate="'required'"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')">Name is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Location:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="location"
              class="form-control"
              v-b-tooltip.hover
              title="Input the address of the client"
              placeholder="Location of the Client"
              v-model.trim="client.location"
              v-validate="'required'"
            />
            <small
              class="text-danger pull-left"
              v-show="errors.has('location')"
            >location is required.</small>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Contact:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="contact"
              class="form-control"
              v-b-tooltip.hover
              title="Input the contact number of the client"
              placeholder="Contact number of the Client"
              v-model.trim="client.contact"
            />
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packages"
              v-model="pack"
              option-value="id"
              option-text="name"
              placeholder="Select a package code..."
              @input="onChangeDoSomething"
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Modem:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="modems"
              v-model="client.modem_id"
              option-value="id"
              option-text="name"
              placeholder="Select a modem name..."
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Package type:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="packageTypes"
              v-model="client.package_type_id"
              option-value="id"
              option-text="name"
              placeholder="Select a package type..."
              isDisabled
            ></model-list-select>
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Protocol:</p>
          </div>
          <div class="col-lg-9">
            <model-list-select
              :list="protocolOption"
              v-model="client.communication_protocol"
              option-value="id"
              option-text="name"
              placeholder="Select a communication protocol..."
            ></model-list-select>
          </div>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="heading-elements">
          <button
            type="button"
            class="btn btn-success btn-labeled pull-right"
            v-on:click="addClient"
          >ADD</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      protocolOption: [
        { id: "Internet", name: "Internet" },
        { id: "Intranet", name: "Intranet" }
      ],
      client: {
        name: "",
        branch_id: "",
        location: "",
        contact: "",
        package_id: "",
        modem_id: "",
        communication_protocol: "Internet",
        package_type_id: ""
      },
      pack: {
        package_id: "",
        package_type_id: ""
      },
      packages: [],
      packageTypes: [],
      modems: [],
      user: []
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {
    this.packages = this.$global.getPackages();
    this.packageTypes = this.$global.getPackageTypes();
    this.modems = this.$global.getModems();
    this.user = this.$global.getUser();
    this.load();
  },
  mounted() {},
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
    addClient() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.client.branch_id = this.user.branch_id;
          this.$http
            .post("api/Client", this.client)
            .then(response => {
              swal(this.client.name, "Added successfully", "success");
              this.client.name = "";
              this.client.location = "";
              this.client.contact = "";
              this.client.package_id = "";
              this.client.modem_id = "";
              this.client.communication_protocol = "Internet";
              this.client.package_type_id = "";
              this.pack = {
                package_id: "",
                package_type_id: ""
              };
              this.$global.setClients(Object.values(response.body)[1]);
            })
            .catch(response => {
              swal({
                title: "Error",
                text: response.body.error,
                icon: "error",
                dangerMode: true
              }).then(value => {
                if (value) {
                }
              });
            });
        }
      });
    },
    onChangeDoSomething() {
      this.client.package_id = this.pack.id;
      this.client.package_type_id = this.pack.package_type.id;
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
