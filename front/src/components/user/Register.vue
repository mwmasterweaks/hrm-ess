<template>
  <div class="row">
    <div class="panel-body">
      <div class="form-group">
        <input class="form-control" v-model="name" type="text" placeholder="Name">
      </div>
      <div class="form-group">
        <input class="form-control" v-model="email" type="email" placeholder="Email">
      </div>
      <div class="form-group">
        <input class="form-control" v-model="password" type="password" placeholder="Password">
      </div>
      <div class="form-group">
        <input
          class="form-control"
          v-model="password2"
          type="password"
          placeholder="Re-type Password"
        >
      </div>
      <div class="form-group pull-right">
        <button class="btn btn-success" @click="Register">Register</button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      password2: ""
    };
  },
  methods: {
    Register() {
      if (this.password == this.password2) {
      } else {
        //password not match
      }
    }
  }
};
</script>
<style scoped>
</style>
