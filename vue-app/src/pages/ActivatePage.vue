<template>
  <div class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
    <i class="fas fa-spinner fa-spin fa-5x text-secondary mb-4"></i>

    <h1 class="mb-4">Thank You!</h1>
    <p class="lead">Thank you for participating. The Administrator will activate your account.</p>
    <p class="text-muted">You will be redirected in 5 seconds...</p>
  </div>


</template>


<script setup>
setTimeout(function() {
  window.location.href = '/';
}, 5000);
</script>

<style>
html, body {
  height: 100%;
}

body {
  display: flex;
  flex-direction: column;
  font-family: 'Roboto', sans-serif;
}

main {
  flex: 1;
}
.bg-gray {
  background-color: #808080; /* Gray background */
}

.bg-blue {
  background-color: #4285f4;
}
</style>
