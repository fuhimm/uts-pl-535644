<script setup>
import { ref } from 'vue'
import api from '../services/api'

const emit = defineEmits(['login-success'])

const email = ref('fahim@ugm.ac.id')
const password = ref('password123')
const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    const { token, user } = response.data
    localStorage.setItem('auth_token', token)
    localStorage.setItem('auth_user', JSON.stringify(user))
    emit('login-success', user)
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || 'Gagal login. Periksa kembali email dan password Anda.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <span class="badge-uts">UTS KEPL 2026/2027</span>
        <h1>Manajemen Magang Kampus</h1>
        <p class="subtitle">Ahmad Fahim &bull; 24/535644/SV/24279</p>
      </div>

      <div v-if="errorMessage" class="alert alert-error">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="auth-form">
        <div class="form-group">
          <label for="email">Email Kampus</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="fahim@ugm.ac.id"
            required
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="Masukkan password"
            required
          />
        </div>

        <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Masuk dengan Token Sanctum' }}
        </button>
      </form>

      <div class="auth-footer">
        <small>Default Akun: <code>fahim@ugm.ac.id</code> | Password: <code>password123</code></small>
      </div>
    </div>
  </div>
</template>
