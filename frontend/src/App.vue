<template>
  <div>
    <nav class="bg-gray-800 p-4 text-white flex items-center">
      <div class="flex items-center gap-4">
        <img
          class="size-8"
          src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500"
          alt="Your Company"
        />

        <h1 class="text-xl font-bold">Kanban</h1>
        <router-link to="/" class="ml-4 px-4 py-2 bg-blue-500 rounded">Dashboard</router-link>
      </div>
    </nav>
    <router-view />
  </div>
</template>

<script setup></script>

<style scoped></style>
