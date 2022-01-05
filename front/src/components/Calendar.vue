<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="panel-body">
        <Fullcalendar @eventClick="showEvent" :plugins="calendarPlugins" :events="events" />
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
      title="Manage Schedule"
    >
      <!-- form -->

      <!-- /form -->
      <div slot="modal-footer" class="w-100"></div>
    </b-modal>
    <!-- End modalEdit -->
  </div>
</template>

<script>
import Fullcalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";
import PrettyCheck from "pretty-checkbox-vue/check";
import PrettyRadio from "pretty-checkbox-vue/radio";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    Fullcalendar,
    "date-picker": datePicker,
    "model-list-select": ModelListSelect,
    "p-check": PrettyCheck,
    "p-radio": PrettyRadio
  },
  data() {
    return {
      calendarPlugins: [dayGridPlugin, interactionPlugin],
      tickets: {},
      schedules: {},
      events: [],
      roles: [],

      AppliedDateoptions: {
        format: "YYYY-MM-DD",
        useCurrent: false
      }
    };
  },
  created() {
    this.user = this.$global.getUser();
    this.roles = this.$global.getRoles();
    this.events = [
      // {
      //   id: 55,
      //   title: this.tickets[0].ticket_status.name,
      //   backgroundColor: "red",
      //   start: new Date(),
      //   end: new Date()
      // },
      // {
      //   id: 2,
      //   title: "2",
      //   backgroundColor: "yellow",
      //   start: new Date(),
      //   end: new Date()
      // }
    ];
    this.getEvents();
  },
  methods: {
    addNewEvent() {
      axios
        .post("/api/calendar", {
          ...this.newEvent
        })
        .then(data => {
          this.getEvents(); // update our list of events
          this.resetForm(); // clear newEvent properties (e.g. title, start_date and end_date)
        })
        .catch(err =>
          console.log("Unable to add new event!", err.response.data)
        );
    },
    showEvent(arg) {
      // if (arg.event.backgroundColor == "green") {
      //   this.client_details = arg.event.extendedProps.data;
      //   this.$bvModal.show("modalEdit");
      // } else {
      //   this.editTicket = arg.event.extendedProps.data;
      //   this.$bvModal.show("modalEditTicket");
      // }
    },
    updateEvent() {},

    deleteEvent() {
      axios
        .delete("/api/calendar/" + this.indexToUpdate)
        .then(resp => {
          this.resetForm();
          this.getEvents();
          this.addingMode = !this.addingMode;
        })
        .catch(err =>
          console.log("Unable to delete event!", err.response.data)
        );
    },

    getEvents() {
      var date = new Date();
      var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
      var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
      this.events = [];

      this.$http
        .get(
          "api/getDTRinRange/" +
            this.formatDate(firstDay) +
            "/" +
            this.formatDate(lastDay) +
            "/" +
            this.user.id
        )
        .then(function(response) {
          response.body.item.forEach(item => {
            if (!item.is_rest_day) {
              var time_in = item.time_in.split(" ");
              var time_out = item.time_out.split(" ");
              var temp = {
                id: item.id,
                title: time_in[1] + " - " + time_out[1],
                start: time_in[0],
                end: time_out[0],
                data: item
              };
              this.events.push(temp);
            }
          });
          console.log(response.body);
        });
    },
    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    }
  },
  watch: {
    indexToUpdate() {
      return this.indexToUpdate;
    }
  }
};
</script>

<style lang="css">
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
.fc-unthemed td.fc-today {
  background: #e3fce8;
}

.fc-title {
  color: #fff;
}

.fc-title:hover {
  cursor: pointer;
}
</style>
