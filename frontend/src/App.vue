<template>
  <v-app>
    <v-main>
      <v-navigation-drawer :width="281" v-if="authStore.isAuthenticated">
          <v-list-item title="clean-api Frontend"></v-list-item>
          <v-divider></v-divider>
          <router-link to="/">
            <v-list-item link title="Home"></v-list-item>
          </router-link>
          <router-link to="/users">
            <v-list-item link title="Users"></v-list-item>
          </router-link>
          <v-divider></v-divider>
          <v-list-item>
            <template #prepend>
              <v-avatar color="primary">
                <v-icon>mdi-account</v-icon>
              </v-avatar>
            </template>
            <v-list-item-title>{{ authStore.user?.firstName }} {{ authStore.user?.lastName }}</v-list-item-title>
            <v-list-item-subtitle>{{ authStore.user?.email }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item link title="Logout" @click="handleLogout">
            <template #prepend>
              <v-icon>mdi-logout</v-icon>
            </template>
          </v-list-item>
      </v-navigation-drawer>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script setup>
import {onMounted} from 'vue';
import {useRouter} from 'vue-router';
import {useAuthStore} from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
  await authStore.initAuth();
});

async function handleLogout() {
  await authStore.logout();
  router.push('/login');
}
</script>
