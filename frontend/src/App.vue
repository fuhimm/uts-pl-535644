<script setup>
import { ref, onMounted } from 'vue'
import LoginView from './components/LoginView.vue'
import InternshipDashboard from './components/InternshipDashboard.vue'

const isAuthenticated = ref(false)
const currentUser = ref(null)

const checkAuth = () => {
  const token = localStorage.getItem('auth_token')
  const savedUser = localStorage.getItem('auth_user')
  isAuthenticated.value = !!token
  if (savedUser) {
    try {
      currentUser.value = JSON.parse(savedUser)
    } catch (_) {
      currentUser.value = null
    }
  }
}

const onLoginSuccess = (user) => {
  currentUser.value = user
  isAuthenticated.value = true
}

const onLogout = () => {
  currentUser.value = null
  isAuthenticated.value = false
}

onMounted(() => {
  checkAuth()
})
</script>

<template>
  <LoginView v-if="!isAuthenticated" @login-success="onLoginSuccess" />
  <InternshipDashboard v-else :user="currentUser" @logout="onLogout" />
</template>
