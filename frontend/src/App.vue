<template>
  <v-app>
    <v-main>
      <v-navigation-drawer :width="281">
        <v-list-item title="clean-api Frontend"></v-list-item>
        <v-divider></v-divider>
        <router-link to="/">
          <v-list-item link title="Home"></v-list-item>
        </router-link>
        <router-link to="/users">
          <v-list-item link title="Users"></v-list-item>
        </router-link>
      </v-navigation-drawer>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script setup>
//
</script>
