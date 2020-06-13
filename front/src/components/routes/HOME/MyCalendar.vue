<template>
  <div class="mx-auto col-md-12 modified-continer">
    <div class="elBG panel">
      <div class="panel-heading">
        <p class="elClr panel-title">My Calendar</p>
      </div>
      <full-calendar></full-calendar>
    </div>
  </div>
</template>
<script>
import FullCalendar from "./../../Calendar.vue";

export default {
  components: {
    "full-calendar": FullCalendar
  },
  data() {
    return {
      temp: ""
    };
  },
  beforeCreate() {},
  created() {
    this.roles = this.$global.getRoles();
  },
  mounted() {},
  updated() {},
  methods: {}
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
