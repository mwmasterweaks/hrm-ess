<template>
  <b-modal
    id="modal-view-job-order"
    size="lg"
    title="View Job Order"
    :header-bg-variant="' elBG'"
    :header-text-variant="' elClr'"
    :body-bg-variant="' elBG'"
    :body-text-variant="' elClr'"
    :footer-bg-variant="' elBG'"
    :footer-text-variant="' elClr'"
  >
    <template slot="modal-footer" slot-scope="{ }">
      <b-button size="sm" variant="success" @click="btnCreateJO()">Create</b-button>
    </template>
  </b-modal>
</template>
<script>
import { ModelListSelect } from "vue-search-select";
import ViewScheduleVue from "../view/components/ViewSchedule.vue";
export default {
  components: {
    "model-list-select": ModelListSelect
  },
  data() {
    client_id: "";
    return {
      //sched: ViewScheduleVue.data,
    };
  },
  created() {
    //console.log("asd");
  },
  events: {
    event_name: function(data) {
      console.log(data);
    }
  },
  methods: {
    fetchData() {
      //console.log(this.view_job_order.client_id);
    },
    btnCreateJO() {
      console.log(this.client_id);
    }
  }
};
</script>
<style scoped>
</style>


