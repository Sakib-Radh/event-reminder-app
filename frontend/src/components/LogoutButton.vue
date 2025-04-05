<template>
    <button class="btn btn-danger" @click="logout">Logout</button>
</template>
  
<script setup>
  import { useStore } from "vuex";
  import { useRouter } from "vue-router";
  
  const store = useStore();
  const router = useRouter();
  
  const logout = () => {
    store.dispatch("logout");
    router.push("/login");
  };
</script>
  