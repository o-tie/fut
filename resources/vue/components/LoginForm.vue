<template>
  <div class="d-flex align-items-center justify-content-center h-100">
    <div v-if="!isNewUser" class="login-form-wrapper">
      <form @submit.prevent="login" class="mt-3">
        <div class="container">
          <div class="d-flex mb-3">
            <label for="username" class="col-sm-3 col-form-label mx-2 login-form-label">Логін:</label>
            <div class="col-sm-9">
              <input type="text" id="username" v-model="username" class="form-control" required pattern="[a-zA-Z0-9]{3,10}" minlength="3" maxlength="10" placeholder="a-z A-Z 0-9"/>
            </div>
          </div>
          <div class="d-flex mb-3">
            <label for="password" class="col-sm-3 col-form-label mx-2 login-form-label">Пароль:</label>
            <div class="col-sm-9">
              <input type="password" id="password" v-model="password" class="form-control" required minlength="6"/>
            </div>
          </div>
          <div class="mb-3 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Увійти</button>
          </div>
        </div>
      </form>
      <div v-if="message" class="mt-3 alert alert-danger">{{ message }}</div>
    </div>
    <div v-else class="new-user-wrapper">
      <div v-if="newUserMessage" class="mt-3 alert alert-success" role="alert">{{ newUserMessage }}</div>
      <div class="buttons-wrapper d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mx-3" @click="cancel">Відбій</button>
        <button type="button" class="btn btn-primary" @click="create">Створити</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "LoginForm",
  data() {
    return {
      username: "",
      password: "",
      message: "",
      newUserMessage: "",
      isNewUser: false,
    };
  },
  methods: {
    login() {
      if (this.username.length < 3 || this.password.length < 3) {
        this.message = 'Ну щось взагалі мало символів, давай хоча б 3';
        return;
      }
      axios.post("/login", {params: {username: this.username, password: this.password}})
          .then((response) => {
            if (!response.data.success && response.data.isNewUser) {
                this.isNewUser = true;
                this.newUserMessage = response.data.message;
            }
            if (response.data.success) {
              window.location.replace('/players')
            }
          })
          .catch((error) => {
            if (error.response && error.response.data && error.response.data.message) {
              this.message = error.response.data.message;
            } else {
              this.message = "Something went wrong. Please try again.";
            }
          });
    },
    create() {
      axios.post("/api/create-user", {params: {username: this.username, password: this.password}})
          .then((response) => {
            if (response.data.success) {
              window.location.replace('/players')
            }
          })
          .catch((error) => {
            if (error.response && error.response.data && error.response.data.message) {
              this.error = error.response.data.message;
            } else {
              this.error = "Something went wrong. Please try again.";
            }
          });
    },
    cancel() {
      this.isNewUser = false;
    }
  },
};
</script>

<style scoped>
</style>
