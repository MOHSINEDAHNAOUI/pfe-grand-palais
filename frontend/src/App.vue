<template>
  <div>
    <AppHeader v-if="showHeader" />
    <div :class="needsPadding ? 'pt-44px' : ''">
      <RouterView />
    </div>
    <ChatBot />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, RouterView } from 'vue-router'
import AppHeader from '@/components/common/AppHeader.vue'
import ChatBot from '@/components/common/ChatBot.vue'

const route = useRoute()

const showHeader = computed(() => {
  return !route.path.startsWith('/admin') &&
         !route.path.startsWith('/login') &&
         !route.path.startsWith('/register') &&
         !route.path.startsWith('/verify-email') &&
         !route.path.startsWith('/email-verified') &&
         !route.path.startsWith('/booking/confirm') &&
         !route.path.startsWith('/complete-profile') &&
         !route.path.startsWith('/manager') &&
         !route.path.startsWith('/reception')
})

const needsPadding = computed(() => {
  return route.path.startsWith('/rooms') ||
         route.path.startsWith('/dashboard') ||
         route.path.startsWith('/booking') ||
         route.path.startsWith('/profile')
})
</script>

<style>
/* Apple nav is 44px — pages that need offset */
.pt-44px { padding-top: 44px; }
</style>
