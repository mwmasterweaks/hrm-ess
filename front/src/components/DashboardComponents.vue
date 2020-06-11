<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="mx-auto row" style="background-color:dimgray;">
      <div class="mx-auto col-md-6">
        <div class="elBG panel">
          <div class="panel-heading">
            <p class="elClr panel-title">Installation Summary</p>
          </div>
          <div class="elClr panel-body">
            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">Chart :</p>
              </div>
              <div class="col-lg-9">
                <model-list-select
                  :list="chartList"
                  v-model="selectedChart1"
                  option-value="id"
                  option-text="name"
                  placeholder="Select a chart"
                ></model-list-select>
              </div>
            </div>

            <div class="rowFields mx-auto row">
              <div class="col-lg-3">
                <p class="textLabel">Year :</p>
              </div>
              <div class="col-lg-9">
                <model-list-select
                  :list="yearsValue"
                  v-model="selectedYear1"
                  option-value="id"
                  option-text="name"
                  placeholder="Select a year"
                  @input="onChangeYear1"
                ></model-list-select>
              </div>
            </div>

            <div class="rowFields mx-auto row">
              <div class="col-lg-4" v-if="loaded1">
                <b>
                  <p class="textLabel text-center">Activated</p>
                </b>

                <div class="row">
                  <p class="col-lg-4 textLabel">Jan:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[0] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[0] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[0] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[0] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[0] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Feb:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[1] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[1] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[1] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[1] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[1] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Mar:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[2] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[2] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[2] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[2] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[2] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Apr:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[3] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[3] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[3] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[3] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[3] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">May:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[4] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[4] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[4] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[4] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[4] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Jun:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[5] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[5] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[5] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[5] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[5] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Jul:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[6] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[6] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[6] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[6] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[6] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Aug:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[7] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[7] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[7] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[7] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[7] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Sep:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[8] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[8] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[8] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[8] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[8] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Oct:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[9] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[9] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[9] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[9] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[9] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Nov:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[10] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[10] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[10] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[10] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[10] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Dec:</p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 99, 132, 1);">{{ dataChart1[0].data[11] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(54, 162, 235, 1);">{{ dataChart1[1].data[11] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(255, 206, 86, 1);">{{ dataChart1[2].data[11] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(75, 192, 192, 1);">{{ dataChart1[3].data[11] }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span style="color:rgba(153, 102, 255, 1);">{{ dataChart1[4].data[11] }}</span>
                  </p>
                </div>

                <div class="row">
                  <p class="col-lg-4 textLabel">Total:</p>
                  <p class="col-lg-1 textLabel">
                    <span
                      style="color:rgba(255, 99, 132, 1);"
                    >{{ TotalActivated(dataChart1[0].data) }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span
                      style="color:rgba(54, 162, 235, 1);"
                    >{{ TotalActivated(dataChart1[1].data) }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span
                      style="color:rgba(255, 206, 86, 1);"
                    >{{ TotalActivated(dataChart1[2].data) }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span
                      style="color:rgba(75, 192, 192, 1);"
                    >{{ TotalActivated(dataChart1[3].data) }}</span>
                  </p>
                  <p class="col-lg-1 textLabel">
                    <span
                      style="color:rgba(153, 102, 255, 1);"
                    >{{ TotalActivated(dataChart1[4].data) }}</span>
                  </p>
                </div>
              </div>
              <div class="col-lg-8">
                <line-chart
                  v-if="loaded1 && selectedChart1 == 'Line'"
                  :data="installCollection"
                  :options="{responsive: true, maintainAspectRatio: false}"
                />

                <bar-chart
                  v-if="loaded1 && selectedChart1 == 'Bar'"
                  :data="installCollection"
                  :options="{responsive: true, maintainAspectRatio: false}"
                />

                <pie-chart
                  v-if="loaded1 && selectedChart1 == 'Pie'"
                  :data="installCollection"
                  :options="{responsive: true, maintainAspectRatio: false}"
                />

                <radar-chart
                  v-if="loaded1 && selectedChart1 == 'Radar'"
                  :data="installCollection"
                  :options="{responsive: true, maintainAspectRatio: false}"
                />

                <polar-chart
                  v-if="loaded1 && selectedChart1 == 'Polar'"
                  :data="installCollection"
                  :options="{responsive: true, maintainAspectRatio: false}"
                />
              </div>
              <div class="elBG divLoaderDiv" v-if="!loaded1">
                <img src="../img/loading.gif" class="imgLoader" />
              </div>
              <!-- <button v-on:click="changeData">Change data</button> -->
            </div>
          </div>
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
      loaded: false,
      loaded1: false,
      dataChart: [],
      dataChart1: [],
      installCollection: {},
      chartOptions: {},
      yearsValue: [],
      tickets: [],
      selectedYear1: "",
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
  created() {},
  mounted() {
    this.loaded = false;
    this.loaded1 = false;
    this.load();
    this.fillData();
  },
  updated() {},
  beforeDestroy() {
    this.destroy();
  },
  methods: {
    load() {
      this.$nextTick(function() {
        setTimeout(function() {
          document.getElementById("componentMenu").className =
            "customeDropDown dropdown-menu show";
        }, 100);
      });
    },
    destroy() {
      document.getElementById("navbarDashboard").className = "";
    },
    fillData() {
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
      this.getInstallCollection();
    },
    getRandomInt() {
      return Math.floor(Math.random() * (50 - 5 + 1)) + 5;
    },
    getInstallCollection() {
      this.$http
        .get("api/clientDetail/installationSummary/" + this.selectedYear1)
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
