<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">
          <b>Version</b>
          <span class="elClr">1.0.1</span>
        </h6>
      </div>

      <div class="elClr panel-body">
        <div>
          <span class="elClr">
            <b>Version: 1.0.1</b>
          </span>
          <br />
          <span class="elClr">
            <b>Released: July 25, 2019</b>
          </span>
          <br />
          <span class="elClr">Launched of the website/system</span>
        </div>
        <embed src="inetinfosystem.pdf" style="width:100%; height:1000px;" />
      </div>
      <div class="panel-footer">
        <div class="heading-elements"></div>
      </div>
    </div>
  </div>
</template>
<script>
import swal from "sweetalert";

export default {
  data() {
    return {
      sales: {
        name: ""
      }
    };
  },
  beforeCreate() {
    this.$global.loadJS();
  },
  created() {},
  mounted() {
    this.load();
  },
  updated() {},
  methods: {
    load() {}
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
