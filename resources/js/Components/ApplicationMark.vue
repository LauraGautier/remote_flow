<template>
  <img
    :src="lightLogo"
    alt="Logo"
    class="h-9 w-9 rounded-full object-cover block dark:hidden"
  />
  <img
    :src="darkLogo"
    alt="Logo"
    class="h-9 w-9 rounded-full object-cover hidden dark:block"
  />
</template>

<script setup>
import lightLogo from '../../images/logo.png';
import darkLogo from '../../images/logo-dark.png';
</script>
