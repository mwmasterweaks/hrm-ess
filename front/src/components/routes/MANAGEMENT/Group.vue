<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Manage Group
          <b-button
            v-b-modal="'ModelAdd'"
            type="button"
            class="btn btn-success btn-labeled pull-right margin-right-10"
            v-if="roles.create_group"
            >Add</b-button
          >
        </p>
      </div>

      <div class="elClr panel-body">
        <div>
          <b-row style="margin:10px;">
            <b-col md="5" class="my-1">
              <b-form-group label-cols-sm="2" label="Filter" class="mb-0">
                <b-input-group>
                  <b-form-input
                    v-model="tblFilter"
                    placeholder="Filter"
                  ></b-form-input>
                  <b-input-group-append>
                    <b-button :disabled="!tblFilter" @click="tblFilter = ''"
                      >Clear</b-button
                    >
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-col>
            <b-col md="5 " class="my-1"></b-col>

            <b-col md="2 " class="my-1">
              <b-form-group label-cols-sm="4" label="Show" class="mb-0">
                <b-form-select
                  v-model="perPage"
                  :options="pageOptions"
                ></b-form-select>
              </b-form-group>
            </b-col>
          </b-row>
          <b-table
            class="elClr"
            show-empty
            striped
            hover
            outlined
            :fields="fields"
            :items="items"
            :filter="tblFilter"
            :busy="tblisBusy"
            :current-page="currentPage"
            :per-page="perPage"
            :tbody-tr-class="tblRowClass"
            head-variant=" elClr"
            @filtered="onFiltered"
            @row-clicked="tblRowClicked"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>
        </div>
      </div>
      <div class="elClr panel-footer">
        <div class="row" style="background-color:; padding:15px;">
          <div class="col-md-8" style="background-color:;">
            <span class="elClr">{{ totalRows }} item/s found.</span>
          </div>

          <div class="col-md-4" style="background-color:;">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              class="my-0 pull-right"
            ></b-pagination>
          </div>
        </div>
      </div>

      <!-- ModelAdd ---------------------------------------------------------------------------------------->
      <b-modal
        id="ModelAdd"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Add Group"
        @ok="handleOk"
      >
        <!-- form -->
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
              title="Name of group"
              placeholder="Group name"
              v-validate="'required'"
              v-model.trim="item_add.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')"
              >Group Name is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="desc"
              class="form-control"
              v-b-tooltip.hover
              title="Description of the group"
              v-validate="'required'"
              placeholder="Description"
              v-model.trim="item_add.desc"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('desc')"
              >Description is required.</small
            >
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button size="sm" variant="success" @click="btnAdd()">Add</b-button>
        </template>
      </b-modal>
      <!-- End ModelAdd -->

      <!-- modalEdit ---------------------------------------------------------------------------------------->
      <b-modal
        id="modalEdit"
        :header-bg-variant="' elBG'"
        :header-text-variant="' elClr'"
        :body-bg-variant="' elBG'"
        :body-text-variant="' elClr'"
        :footer-bg-variant="' elBG'"
        :footer-text-variant="' elClr'"
        size="xl"
        title="Update Group"
        @ok="handleOk"
      >
        <!-- form -->
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
              title="Name of group"
              placeholder="Group name"
              v-validate="'required'"
              v-model.trim="item_edit.name"
              autocomplete="off"
              autofocus="on"
            />
            <small class="text-danger pull-left" v-show="errors.has('name')"
              >Group Name is required.</small
            >
          </div>
        </div>

        <div class="rowFields mx-auto row">
          <div class="col-lg-3">
            <p class="textLabel">Description:</p>
          </div>
          <div class="col-lg-9">
            <input
              type="text"
              name="desc"
              class="form-control"
              v-b-tooltip.hover
              title="Description of the group"
              v-validate="'required'"
              placeholder="Description"
              v-model.trim="item_edit.desc"
              autocomplete="off"
            />
            <small class="text-danger pull-left" v-show="errors.has('desc')"
              >Description is required.</small
            >
          </div>
        </div>

        <!-- /form -->
        <template slot="modal-footer" slot-scope="{}">
          <b-button
            size="sm"
            variant="success"
            v-if="roles.update_group"
            @click="btnUpdate()"
            >Update</b-button
          >
          <b-button
            size="sm"
            variant="danger"
            v-if="roles.delete_group"
            @click="btnDelete()"
            >Delete</b-button
          >
        </template>
      </b-modal>
      <!-- End modalEdit -->
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
      user: {},
      tblisBusy: true,
      fields: [
        { key: "name", sortable: true },
        { key: "desc", label: "Description", sortable: true },
        { key: "created_at", sortable: true },
        { key: "updated_at", sortable: true }
      ],
      items: [],
      tblFilter: null,
      totalRows: 0,
      currentPage: 2,
      perPage: 10,
      pageOptions: [10, 25, 50, 100],
      item_add: {
        name: "",
        desc: ""
      },
      item_edit: {
        name: "",
        desc: ""
      },
      roles: []
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
    this.roles = this.$global.getRoles();
    this.items = this.$global.getGroup();
    this.tblisBusy = false;
    this.totalRows = this.items.length;
  },
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {}, 100);
      });
    },
    tblRowClass(item, type) {
      if (!item) return;
      else return "elClr cursorPointer";
    },
    tblHeadClass(item, type) {
      if (!item) return;
      else {
        return "elClr";
      }
    },
    onFiltered(filteredItems) {
      this.totalRows = filteredItems.length;
      this.currentPage = 1;
    },
    tblRowClicked(item, index, event) {
      this.$bvModal.show("modalEdit");
      this.item_edit = item;
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
    },
    btnUpdate() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.tblisBusy = true;
          swal({
            title: "Are you sure?",
            text: "Do you want to Update this item?",
            icon: "warning",
            buttons: true,
            dangerMode: true
          }).then(update => {
            if (update) {
              this.item_edit.user_id = this.user.id;
              this.item_edit.user_name =
                this.user.first_name + " " + this.user.last_name;

              this.$http
                .put("api/Group/" + this.item_edit.id, this.item_edit)
                .then(response => {
                  this.items = response.body;
                  this.$global.setGroup(response.body);
                  this.totalRows = this.items.length;
                  swal("Update!", "Update successfully", "success");
                  this.$bvModal.hide("modalEdit");
                  this.tblisBusy = false;
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
        }
      });
    },
    btnAdd() {
      this.$validator.validateAll().then(result => {
        if (result) {
          this.item_add.user_id = this.user.id;
          this.item_add.user_name =
            this.user.first_name + " " + this.user.last_name;

          this.$http
            .post("api/Group", this.item_add)
            .then(response => {
              swal("Notification", "Added successfully", "success");
              this.$global.setGroup(response.body);
              this.items = response.body;
              this.totalRows = this.items.length;
              this.item_add = {
                name: "",
                desc: ""
              };

              this.$bvModal.hide("ModelAdd");
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
    },
    btnDelete() {
      swal({
        title: "Are you sure?",
        text: "Do you really want to delete this item permanently",
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(willDelete => {
        if (willDelete) {
          this.items = [];
          this.tblisBusy = true;
          var temp =
            this.item_edit.id +
            "," +
            this.user.id +
            "," +
            this.user.first_name +
            " " +
            this.user.last_name;
          this.$http
            .delete("api/Group/" + temp)
            .then(response => {
              this.$bvModal.hide("modalEdit");
              swal("Deleted!", "Item has been deleted", "success").then(
                value => {
                  this.$global.setGroup(response.body);
                  this.items = response.body;
                  this.totalRows = this.items.length;
                  this.tblisBusy = false;
                }
              );
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
.modal-content,
modal-header {
  border: 1px solid red;
}
</style>
