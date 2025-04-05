<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg p-4">
          <h2 class="text-center mb-4">Register</h2>
          <form @submit.prevent="register">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input v-model="user.name" type="text" class="form-control" id="name" placeholder="Enter your name" required />
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input v-model="user.email" type="email" class="form-control" id="email" placeholder="Enter your email" required />
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input v-model="user.password" type="password" class="form-control" id="password" placeholder="Enter password" required />
            </div>

            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <input v-model="user.password_confirmation" type="password" class="form-control" id="password_confirmation" placeholder="Confirm password" required />
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
          </form>

          <p class="text-center mt-3">
            Already have an account? <router-link to="/login">Login</router-link>
          </p>
          
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue';
  import axios from '../services/axios'
  import { useRouter } from 'vue-router';
  import { useStore } from 'vuex';
  
  const user = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  
  const store = useStore();
  const router = useRouter();
  
  const register = async () => {
    if (user.value.password !== user.value.password_confirmation) {
      console.log('Passwords do not match!');
      return;
    }
  
    try {
      const response = await axios.post('/register', {
        name: user.value.name,
        email: user.value.email,
        password: user.value.password,
        password_confirmation: user.value.password_confirmation,
      });
    
      const { token, user: registeredUser } = response.data;
    
      store.dispatch('login', { token, user: registeredUser });
      console.log('success')
      router.push('/tasks');
    } catch (error) {
      console.error('Registration failed:', error);
    }
  };
</script>
