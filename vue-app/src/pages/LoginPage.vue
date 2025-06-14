<template>
  <div class="container mt-5">
    <h2>Login Page</h2>
    <!-- You can add a login form here if needed -->
  </div>
</template>

<script setup>
import {onMounted} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import {useToast} from 'vue-toastification';

const route = useRoute();
const router = useRouter();
const toast = useToast();

onMounted(() => {
  const {token, name, email, is_activated} = route.query;

  if (!token) {
    toast.error('Redirecting to account activation.');
    return router.push('/activate');
  }
  // Save token and user data to localStorage
  localStorage.setItem('token', token);
  localStorage.setItem('user', JSON.stringify({name, email, is_activated}));
  toast.success(`Welcome, ${name}!`);
  router.push('/maps');

});
</script>