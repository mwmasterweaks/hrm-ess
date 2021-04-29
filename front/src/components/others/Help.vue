<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <h6 class="elClr panel-title">
          <b>BETA Version</b>
        </h6>
      </div>

      <div class="elClr panel-body">
        <div>
          <span class="elClr">
            <b>BETA Version:</b>
          </span>
          <br />
          <span class="elClr">
            <b>Released: October 02, 2020</b>
          </span>
          <br />
          <span class="elClr">Launched of the website/system</span>
        </div>
        <embed src="doc.pdf" style="width:100%; height:1000px;" />
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
