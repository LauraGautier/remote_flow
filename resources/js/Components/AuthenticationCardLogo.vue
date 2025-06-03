<template>
  <div class="logo-container">
    <!-- Logo pour le mode clair -->
    <img
      :src="lightLogo"
      alt="Logo"
      width="40%"
      class="circular-logo block dark:hidden"
    >
    <!-- Logo pour le mode sombre -->
    <img
      :src="darkLogo"
      alt="Logo"
      width="40%"
      class="circular-logo hidden dark:block"
    >
  </div>
</template>

<script setup>
import lightLogo from '../../images/logo.png';
import darkLogo from '../../images/logo-dark.png';
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
