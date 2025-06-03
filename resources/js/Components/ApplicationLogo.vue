<template>
  <div class="logo-container">
    <img
      src="../../images/logo.png"
      alt="Logo"
      width="50%"
      class="circular-logo hidden dark:block"
    >
    <img
      src="../../images/logo-dark.png"
      alt="Logo"
      width="50%"
      class="circular-logo block dark:hidden"
    >
  </div>
</template>

<script setup>
</script>

<style scoped>
.logo-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.circular-logo {
  border-radius: 50%;
  object-fit: cover;
}
</style>
