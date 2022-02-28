<template>
  <div class="mx-auto col-md-10">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">Suggestions / Issues</h6>
      </div>

      <div class="elClr panel-body">
        <b-card-group deck>
          <b-card>
            <div slot="header">
              <input
                type="text"
                name="title"
                class="form-control"
                v-b-tooltip.hover
                title="Input the title of your suggestion/issue"
                placeholder="Title"
                v-validate="'required'"
                v-model.trim="addsuggest.title"
                autocomplete="off"
              />
              <small class="text-danger pull-left" v-show="errors.has('title')"
                >Title is required.</small
              >
            </div>
            <b-card-body>
              <textarea
                class="form-control"
                rows="4"
                name="message"
                v-b-tooltip.hover
                title="Input message "
                placeholder="Message"
                v-model.trim="addsuggest.message"
              ></textarea>
            </b-card-body>
            <div slot="footer">
              <b-button
                class="pull-right"
                size="sm"
                variant="success"
                @click="btnSubmit()"
                :disabled="addsuggest.title == '' || addsuggest.message == ''"
                >Submit</b-button
              >
            </div>
          </b-card>
        </b-card-group>
        <!-- ------------------------------------------------------------------------------------------- -->
        <br />
        <br />
        <hr />
        <br />
        <br />
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
            show-empty
            class="elClr"
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
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
          >
            <div slot="table-busy" class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
            <template slot="table-caption"></template>
          </b-table>
        </div>
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
      <div class="panel-footer">
        <div class="heading-elements"></div>
      </div>
    </div>

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
      title="Suggestion/Issue"
    >
      <!-- form -->

      <b-card-group deck>
        <b-card>
          <div slot="header">
            <input
              type="text"
              name="titleedit"
              class="form-control"
              v-b-tooltip.hover
              title="Input the title of your suggestion/issue"
              placeholder="Title"
              v-validate="'required'"
              v-model.trim="editSuggest.title"
              autocomplete="off"
            />
          </div>
          <b-card-body>
            <textarea
              class="form-control"
              rows="7"
              name="message"
              v-b-tooltip.hover
              title="Input message "
              placeholder="Message"
              v-model.trim="editSuggest.message"
            ></textarea>
          </b-card-body>
          <b-list-group flush style="margin-top:30px;">
            <textarea
              style="outline: none;  resize: none; overflow: auto;"
              class="form-control"
              rows="3"
              v-b-tooltip.hover
              title="Input Comment "
              placeholder="Write a comment...."
              v-model.trim="comment"
            ></textarea>
            <b-button size="sm" variant="success" @click="submitComment"
              >submit comment</b-button
            >
          </b-list-group>
          <b-list-group flush>
            <b-list-group-item v-for="comment in comments" :key="comment.id">
              <p style="color:#72b8dc">
                <b>{{ comment.user.email }} :</b>
              </p>
              &nbsp;&nbsp;{{ comment.comment }}
            </b-list-group-item>
          </b-list-group>
        </b-card>
      </b-card-group>

      <!-- /form -->
      <div slot="modal-footer" slot-scope="{}">
        <b-button
          size="sm"
          variant="success"
          v-if="editSuggest.user.id == user.id"
          :disabled="editSuggest.title == '' || editSuggest.message == ''"
          @click="btnUpdate()"
          >Update</b-button
        >
      </div>
    </b-modal>
    <!-- End modalEdit -->
  </div>
</template>
<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      sortBy: "created_at",
      sortDesc: true,

      addsuggest: {
        title: "",
        message: ""
      },

      tblisBusy: true,
      fields: [
        { key: "title", label: "Title", sortable: true },
        {
          key: "message",
          label: "Message",
          formatter: value => {
            var temp = "";
            if (value.length > 50) temp = "...";
            return value.slice(0, 50) + temp;
          }
        },
        { key: "created_at", label: "Submitted at", sortable: true },

        {
          key: "user.first_name",
          label: "Submitted by",
          sortable: true
        }
      ],
      editSuggest: {
        id: "",
        user_id: "",
        title: "",
        message: "",
        user: {
          id: "",
          name: ""
        }
      },
      comment: "",
      items: [],
      comments: [],
      tblFilter: null,
      totalRows: 1,
      currentPage: 2,
      perPage: 5,
      pageOptions: [5, 10, 20],
      roles: [],
      user: {}
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
    this.load();
  },
  mounted() {},
  updated() {},
  methods: {
    load() {
      this.$http.get("api/Suggestion").then(function(response) {
        this.items = response.body;
        this.tblisBusy = false;
        this.totalRows = this.items.length;
        console.log(this.items);
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
      // console.log('kefetyyyyyy');
      this.$bvModal.show("modalEdit");
      this.editSuggest = item;
      this.comments = [];
      this.$http.put("api/getComments/" + item.id).then(function(response) {
        this.comments = response.body;
      });
    },

    btnSubmit() {
      // console.log()
      this.addsuggest.user_id = this.user.id;
      this.addsuggest.userid = this.user.id;
      this.addsuggest.user_name =
        this.user.first_name + " " + this.user.last_name;
      swal({
        title: "Confirmation",
        text: "Submit suggestion?",
        icon: "info",
        buttons: ["No", "Yes"]
      }).then(yes => {
        if (yes) {
          this.tblisBusy = true;
          this.$http
            .post("api/Suggestion", this.addsuggest)
            .then(response => {
              swal("Submited!", "submited successfully", "success").then(
                value => {
                  this.items = response.body;
                  this.totalRows = this.items.length;
                }
              );
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
    },
    btnUpdate() {
      swal({
        title: "Confirmation",
        text: "Update suggestion/issue?", // temporary
        icon: "warning",
        buttons: true,
        dangerMode: true
      }).then(update => {
        if (update) {
          this.editSuggest.user_id = this.user.id;
          this.editSuggest.userid = this.user.id;
          this.editSuggest.user_name =
            this.user.first_name + " " + this.user.last_name;

          this.$http
            .put("api/Suggestion/" + this.editSuggest.id, this.editSuggest)
            .then(response => {
              console.log(response.body);
              this.items = response.body;
              swal("Update!", "Update successfully", "success");
              this.$bvModal.hide("modalEdit");
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

    submitComment() {
      swal({
        title: "Confirmation",
        text: "Submit comment?",
        icon: "info",
        buttons: ["No", "Yes"]
      }).then(yes => {
        if (yes) {
          var temp = {
            suggestion_id: this.editSuggest.id,
            user_id: this.user.id,
            comment: this.comment,
            status: "new",
            userid: this.user.id,
            user_name: this.user.first_name + " " + this.user.last_name
          };
          this.$http
            .post("api/SuggestionComment", temp)
            .then(response => {
              swal("Submited!", "submited successfully", "success");
              this.comment = "";
              this.comments = response.body;
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
</style>
