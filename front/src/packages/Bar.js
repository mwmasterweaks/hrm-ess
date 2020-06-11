import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  props: ["data", "options"],
  mounted() {
    this.renderLineChart();
  },
  computed: {
    chartData: function() {
      return this.data;
    }
  },
  methods: {
    renderLineChart: function() {
      this.renderChart(this.chartData);
    }
  },
  watch: {
    data: function() {
      //this._chart.destroy();
      //this.renderChart(this.data, this.options);
      this.renderLineChart();
    }
  }
};
