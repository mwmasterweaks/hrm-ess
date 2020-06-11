<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="mx-auto row">
      <div class="mx-auto col-md-10">
        <div class="elBG panel">
          <div class="panel-heading">
            <p class="elClr panel-title">Calendar</p>
          </div>

          <div class="elClr panel-body"></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import LineChart from "../packages/LineChart.js";
import BarChart from "../packages/Bar.js";
import PieChart from "../packages/Pie.js";
import RadarChart from "../packages/Radar.js";
import PolarChart from "../packages/PolarArea.js";
import { ModelListSelect } from "vue-search-select";
import FullCalendar from "./Calendar.vue";

//import LineChart from "./LineChart.vue";

export default {
  name: "LineChartContainer",
  components: {
    "model-list-select": ModelListSelect,
    "full-calendar": FullCalendar,
    LineChart,
    BarChart,
    PieChart,
    RadarChart,
    PolarChart
  },
  data() {
    return {
      user: [],
      loaded: false,
      loaded1: false,
      eticketCollection: {},
      dataChart: [],
      dataChart1: [],
      installCollection: {},
      chartOptions: {},
      yearsValue: [],
      tickets: [],
      selectedYear: "",
      selectedYear1: "",
      selectedChart: "Line",
      selectedChart1: "Line",
      chartList: [
        {
          id: "Line",
          name: "Line"
        },
        {
          id: "Bar",
          name: "Bar"
        },
        {
          id: "Pie",
          name: "Pie"
        },
        {
          id: "Radar",
          name: "Radar"
        },
        {
          id: "Polar",
          name: "Polar"
        }
      ]
    };
  },
  created() {
    this.user = this.$global.getUser();
  },
  mounted() {
    this.loaded = false;
    this.loaded1 = false;
    this.load();
    //this.fillData();
  },
  updated() {},
  beforeDestroy() {
    this.destroy();
  },
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {}, 100);
      });
    },
    destroy() {
      document.getElementById("navbarDashboard").className = "";
    },
    fillData() {
      this.eticketCollection = {
        labels: [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
        ],
        datasets: [
          {
            label: "Fixed ticket",
            data: [1, 2, 3, 4, 3, 4, 5, 4, 3, 4, 2],

            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",

              "rgba(200, 90, 1, 1)",
              "rgba(54, 162, 1, 1)",
              "rgba(255, 206, 1, 1)",
              "rgba(65, 52, 1, 100)",
              "rgba(153, 102, 1, 1)",
              "rgba(255, 159, 1, 1)"
            ],
            borderWidth: 1
          }
        ]
      };

      this.installCollection = {
        labels: [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
        ],
        datasets: [
          {
            label: "Activated",
            data: [12, 19, 3, 5, 2, 3],

            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",

              "rgba(200, 90, 1, 1)",
              "rgba(54, 162, 1, 1)",
              "rgba(255, 206, 1, 1)",
              "rgba(65, 52, 1, 100)",
              "rgba(153, 102, 1, 1)",
              "rgba(255, 159, 1, 1)"
            ],
            borderWidth: 1
          }
        ]
      };
      this.chartOptions = {
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true
              }
            }
          ]
        }
      };
      var yearNow = new Date();
      yearNow = yearNow.getFullYear();
      this.selectedYear = yearNow;
      this.selectedYear1 = yearNow;
      var temp = yearNow - 10;
      for (var x = 0; x < 21; x++) {
        var val = {
          id: temp,
          name: temp
        };
        this.yearsValue.push(val);
        temp++;
      }

      this.getEticketCollection();
      //this.getInstallCollection();
    },
    getRandomInt() {
      return Math.floor(Math.random() * (50 - 5 + 1)) + 5;
    },
    getInstallCollection() {
      this.$http
        .get(
          "api/clientDetail/installationSummary/" +
            this.selectedYear1 +
            "/" +
            this.user.branch_id
        )
        .then(response => {
          this.dataChart1 = response.body;
          this.installCollection.datasets = response.body;
          this.loaded1 = true;
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
    },
    getEticketCollection() {
      this.$http
        .get(
          "api/Ticket/eticketSummary/" +
            this.selectedYear +
            "/" +
            this.user.branch_id
        )
        .then(response => {
          this.dataChart = response.body;
          this.eticketCollection.datasets = response.body;
          this.loaded = true;
          this.getInstallCollection();
        });
      // .catch(response => {
      //   swal({
      //     title: "Error",
      //     text: response.body.error,
      //     icon: "error",
      //     dangerMode: true
      //   }).then(value => {
      //     if (value) {
      //     }
      //   });
      // });
    },
    changeData() {
      this.dataChart = [1, 2, 3, 4, 3, 4, 5, 4, 3, 4, 2, 1];
    },
    onChangeYear() {
      this.loaded = false;
      this.getEticketCollection();
    },
    onChangeYear1() {
      this.loaded1 = false;
      this.getInstallCollection();
    },
    TotalActivated(arr) {
      var c = arr.length;
      var total = 0;
      for (var x = 0; x < c; x++) {
        total += arr[x];
      }
      return total;
    }
  }
};
</script>
<style scope>
.smalll {
  margin: 250px auto;
}

.textLabel {
  margin-top: 9px;
  font-size: 15px;
}
.rowFields {
  margin-top: 15px;
}
</style>
