import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { VueQueryPlugin } from '@tanstack/vue-query'

import App from './App.vue'
import router from './router'

import './assets/index.css'

const app = createApp(App)

// Pinia
const pinia = createPinia()
app.use(pinia)

// Vue Router
app.use(router)

// Tanstack Query
app.use(VueQueryPlugin, {
  queryClientConfig: {
    defaultOptions: {
      queries: {
        staleTime: 1000 * 60 * 5, // 5 minutes
        refetchOnWindowFocus: true,
        retry: 1,
      },
    },
  },
})

app.mount('#app')
