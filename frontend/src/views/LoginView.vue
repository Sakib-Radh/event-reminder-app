<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Login</div>
          <div class="card-body">
            <form @submit.prevent="login">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input v-model="email" type="email" id="email" class="form-control" required />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input v-model="password" type="password" id="password" class="form-control" required />
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                {{ loading ? "Logging in..." : "Login" }}
              </button>
              <p class="text-center mt-3">
                Don't have an account? <router-link to="/register">Register</router-link>
              </p>
              <p v-if="errorMessage" class="text-danger text-center mt-2">{{ errorMessage }}</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
  import { ref } from "vue";
  import { useStore } from "vuex";
  import { useRouter } from "vue-router";
  import axios from '../services/axios';
  
  const store = useStore();
  const router = useRouter();

  const email = ref("");
  const password = ref("");

  const login = async () => {
    try {
      const response = await axios.post("/login", {
        email: email.value,
        password: password.value,
      });

      if( !(response.data.token && response.data.user) ) {
        console.log('Something went wrong');
        return;
      }

      store.dispatch("login", {
        token: response.data.token,
        user: response.data.user,
      });
      router.push("/tasks");
      
    } catch (error) {
      console.error("Login error:", error);
    }
  };
  
</script>
  