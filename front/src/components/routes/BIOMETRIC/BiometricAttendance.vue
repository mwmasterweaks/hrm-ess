<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">
          Biometric Attendance
          <span class="cml-10">
            <b>{{ clock.date + " " }}</b>
          </span>
          <span style="color: white">
            <b>{{ clock.time }}</b>
          </span>
          <b-button
            type="button"
            class="btn btn-labeled pull-right btn-default cml-10 cplr-10 cbg-gray"
            @click="btnApply('Time Out')"
            >Punch Out</b-button
          >
          <b-button
            type="button"
            class="btn btn-labeled pull-right btn-default cplr-10 cbg-gray"
            @click="btnApply('Time In')"
            >Punch In</b-button
          >
        </p>
      </div>

      <div id="map-parent-div" class="elClr panel-body">
        <div id="location-div">
          <i class="fas fa-street-view fa-5x" id="cl-icon"></i
          ><br /><br /><br />
          <span id="location-span">LOCATION:</span>
          <a
            :href="
              'https://www.google.com/maps/@' +
                marker.position.lat +
                ',' +
                marker.position.lng +
                ',19z'
            "
            target="_blank"
            id="location-link"
            >{{ marker.position.lat + ", " + marker.position.lng }}
          </a>
        </div>
        <!-- <GmapMap
          :center="marker.position"
          :zoom="15"
          map-type-id="terrain"
          style="height: 600px"
        >
          <GmapMarker
            :position.sync="marker.position"
            :clickable="true"
            :animation="2"
            :label="{ text: 'YOUR LOCATION', color: 'white', fontSize: '15px' }"
          />
        </GmapMap> -->
      </div>

      <div class="elClr panel-footer"></div>
    </div>

    <div id="to-approve">
      <div>
        <table class="my-table">
          <tr>
            <td colspan="2" class="my-td head-bg">
              BIOMETRIC ATTENDANCE
            </td>
          </tr>
          <tr>
            <td class="my-td name-bg">
              NAME:
            </td>
            <td class="my-td name-bg">
              {{
                user.first_name.toUpperCase() +
                  " " +
                  user.last_name.toUpperCase()
              }}
            </td>
          </tr>
          <tr>
            <td class="my-td tr-even">Reference no:</td>
            <td class="my-td tr-even">REFNUM</td>
          </tr>
          <tr>
            <td class="my-td">Type:</td>
            <td class="my-td">BIOTYPE</td>
          </tr>
          <tr>
            <td class="my-td tr-even">Latitude:</td>
            <td class="my-td tr-even">{{ marker.position.lat }}</td>
          </tr>
          <tr>
            <td class="my-td">Longitude:</td>
            <td class="my-td">{{ marker.position.lng }}</td>
          </tr>
          <tr>
            <td class="my-td tr-even">Location:</td>
            <td class="my-td tr-even">
              <a
                :href="
                  'https://www.google.com/maps/@' +
                    marker.position.lat +
                    ',' +
                    marker.position.lng +
                    ',19z'
                "
                >www.google.com/maps</a
              >
            </td>
          </tr>
          <tr>
            <td class="my-td">Punch Time:</td>
            <td class="my-td">PUNCHTIME</td>
            <!-- <td class="my-td">{{ dt.split("/").join("-") }}</td> -->
          </tr>
        </table>

        <!-- <a :href="$url_back + '/api/publicApproveRequest/1/8' + '/getbioid/' + user.id">
          <button
            size="sm"
            variant="success"
            title="Approve"
            style="float: left; background: #4CAF50; color: #ffffff; margin: 10px; border:0; border-radius: 3px; padding: 5px;"
          >Approve</button>
        </a>
        <a :href="$url_back + '/api/publicApproveRequest/0/8' + '/getbioid/' + user.id">
          <button
            size="sm"
            variant="danger"
            title="Disapprove"
            style="float: left; background: #f44336; color: #ffffff; margin: 10px; margin-left: 5px; border:0; border-radius: 3px; padding: 5px;"
          >Decline</button>
        </a>-->
      </div>
    </div>
  </div>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import swal from "sweetalert";

import PrettyCheck from "pretty-checkbox-vue/check";
import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

export default {
  components: {
    "date-picker": datePicker,
    "p-check": PrettyCheck,
    "model-list-select": ModelListSelect
  },
  data() {
    return {
      dt: "",
      clock: {
        time: "",
        date: ""
      },
      marker: {
        position: {
          lat: 0,
          lng: 0
        }
      },
      type: "",
      t: 0,
      aid: 0,
      eid: 0
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
    this.load_clock();
    this.$getLocation({
      enableHighAccuracy: true
    })
      .then(coordinates => {
        console.log(coordinates);
        this.marker.position = {
          lat: coordinates.lat,
          lng: coordinates.lng
        };
        console.log(this.marker);
      })
      .catch(error => {
        console.log("no coor");
        swal({
          title:
            "Please turn on Location Services to allow HRMESS determine your location.",
          icon: "info",
          buttons: {
            cancel: {
              text: "Got It!",
              value: false,
              visible: true
            },
            confirm: {
              text: "How?",
              value: true,
              visible: true
            }
          }
        }).then(value => {
          if (value == true) {
            window.open(
              "https://docs.buddypunch.com/en/articles/919258-how-to-enable-location-services-for-chrome-safari-edge-and-android-ios-devices-gps-setting",
              "_blank"
            );
          }
        });
      });
  },
  mounted() {},
  updated() {},
  methods: {
    load_clock() {
      setInterval(this.updateTime, 1000);
      this.updateTime();
    },
    updateTime() {
      var cd = new Date();
      const offset = cd.getTimezoneOffset();
      var ncd = new Date(cd.getTime() - offset * 60 * 1000);
      this.dt = ncd.toISOString().split("T")[0];
      this.dt += " " + cd.toString().substr(16, 8);
      this.clock.time = cd.toLocaleTimeString();
      this.clock.date = cd.toDateString();
      /* this.dt =
        cd.toLocaleString().substr(0, 9) + " " + cd.toString().substr(16, 8);
       */
    },
    btnApply(type) {
      console.log(this.marker.position.lat + " " + this.marker.position.lng);
      if (this.marker.position.lat == 0 && this.marker.position.lng == 0) {
        swal({
          title:
            "Please turn on Location Services to allow HRMESS determine your location.",
          icon: "info",
          buttons: {
            cancel: {
              text: "Got It!",
              value: false,
              visible: true
            },
            confirm: {
              text: "How?",
              value: true,
              visible: true
            }
          }
        }).then(value => {
          if (value == true) {
            window.open(
              "https://docs.buddypunch.com/en/articles/919258-how-to-enable-location-services-for-chrome-safari-edge-and-android-ios-devices-gps-setting",
              "_blank"
            );
          }
        });
      } else {
        if (this.user.emp_approver.length > 0) {
          this.type = type;
          var emp = this.user.emp_approver[0].approver.employee; // catch! if employee has no approver
          var sendTo = [
            {
              email: emp.email1,
              name: emp.first_name + " " + emp.last_name
            }
          ];

          // var sendTo = [
          //   {
          //     email: "mwmasterweaks@gmail.com",
          //     name: "Peter"
          //   }
          // ];
          var temp = {
            employee_id: this.user.id,
            punch_time: this.dt.split("/").join("-"),
            type: type,
            latitude: this.marker.position.lat,
            longitude: this.marker.position.lng,
            msg: document.getElementById("to-approve").innerHTML,
            user_email: this.user.email1,
            user_name: this.user.first_name + " " + this.user.last_name,
            sendTo: sendTo,
            CCto: []
          };
          console.log(temp);
          this.$http.post("api/BioAttendance", temp).then(response => {
            console.log(response.body);
            var mode = type == "Time In" ? "In" : "Out";
            if (response.body == 0) {
              swal({
                title: "No work schedule!",
                text:
                  "Your work schedule for this day has not been set. Please contact HR Department.",
                icon: "info"
              });
            } else if (response.body == 1) {
              swal({
                title: "Logged " + mode + "!",
                icon: "success"
              });
            } else if (response.body == 2) {
              swal({
                title: "Log " + mode + " time has already been recorded!",
                icon: "info"
              });
            }
          });
        } else {
          swal(
            "No Approver!",
            "Please contact HR Department to update your approvers.",
            "info"
          );
        }
      }
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
#to-approve {
  visibility: hidden;
}
#map-parent-div {
  text-align: center;
  width: 100%;
}
#location-div {
  background: #f0fff0;
  padding: 30px;
  padding-left: 50px;
  padding-right: 50px;
  border-radius: 10px;
  width: fit-content;
  display: inline-block;
}
#location-link {
  font-size: large;
  color: #009600;
  font-weight: bold;
}
#location-link:hover {
  color: #007500;
  text-decoration: none;
}
#location-span {
  color: #444444;
  font-weight: bold;
}
#cl-icon {
  color: #444444;
}
</style>
