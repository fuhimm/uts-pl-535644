<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['logout'])

const internships = ref([])
const selectedInternship = ref(null)
const loading = ref(false)
const submitting = ref(false)
const editingId = ref(null)
const statusMessage = ref('')
const errorMessage = ref('')
const validationErrors = ref({})

const form = ref({
  student_name: '',
  company_name: '',
  position: '',
  start_date: '2026-11-01',
  status: 'Pending',
})

const resetForm = () => {
  editingId.value = null
  validationErrors.value = {}
  form.value = {
    student_name: '',
    company_name: '',
    position: '',
    start_date: '2026-11-01',
    status: 'Pending',
  }
}

const fetchInternships = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await api.get('/internships')
    internships.value = response.data.data || []
  } catch (err) {
    if (err.response?.status === 401) {
      handleForceLogout()
    } else {
      errorMessage.value = 'Gagal memuat data magang dari server.'
    }
  } finally {
    loading.value = false
  }
}

const viewDetail = async (id) => {
  errorMessage.value = ''
  try {
    const response = await api.get(`/internships/${id}`)
    selectedInternship.value = response.data.data
  } catch (err) {
    errorMessage.value = 'Gagal mengambil detail data magang.'
  }
}

const startEdit = (item) => {
  editingId.value = item.id
  validationErrors.value = {}
  statusMessage.value = ''
  form.value = {
    student_name: item.student_name,
    company_name: item.company_name,
    position: item.position,
    start_date: item.start_date ? item.start_date.substring(0, 10) : '2026-11-01',
    status: item.status,
  }
}

const handleSubmit = async () => {
  submitting.value = true
  errorMessage.value = ''
  statusMessage.value = ''
  validationErrors.value = {}

  try {
    if (editingId.value) {
      const response = await api.put(`/internships/${editingId.value}`, form.value)
      statusMessage.value = response.data.message || 'Data magang berhasil diperbarui.'
      if (selectedInternship.value?.id === editingId.value) {
        selectedInternship.value = response.data.data
      }
    } else {
      const response = await api.post('/internships', form.value)
      statusMessage.value = response.data.message || 'Data magang berhasil ditambahkan.'
    }
    resetForm()
    await fetchInternships()
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {}
      errorMessage.value = 'Validasi gagal (422). Mohon periksa isian form.'
    } else {
      errorMessage.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.'
    }
  } finally {
    submitting.value = false
  }
}

const handleDelete = async (id) => {
  errorMessage.value = ''
  statusMessage.value = ''
  try {
    const response = await api.delete(`/internships/${id}`)
    statusMessage.value = response.data.message || 'Data magang berhasil dihapus.'
    if (selectedInternship.value?.id === id) {
      selectedInternship.value = null
    }
    if (editingId.value === id) {
      resetForm()
    }
    await fetchInternships()
  } catch (err) {
    errorMessage.value = 'Gagal menghapus data magang.'
  }
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
  } catch (_) {
    // Abaikan jika token sudah kadaluarsa
  } finally {
    handleForceLogout()
  }
}

const handleForceLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('auth_user')
  emit('logout')
}

onMounted(() => {
  fetchInternships()
})
</script>

<template>
  <div class="dashboard-container">
    <header class="topbar">
      <div class="topbar-brand">
        <span class="brand-badge">SIMagang UGM</span>
        <div>
          <h1>Sistem Manajemen Magang Kampus</h1>
          <p>Ahmad Fahim &bull; NIM: 24/535644/SV/24279 &bull; Repo: <code>uts-pl-535644</code></p>
        </div>
      </div>

      <div class="topbar-actions">
        <div class="user-pill">
          <span class="dot-online"></span>
          <span>{{ user?.email || 'fahim@ugm.ac.id' }}</span>
        </div>
        <button @click="handleLogout" class="btn btn-danger">Logout</button>
      </div>
    </header>

    <div v-if="statusMessage" class="alert alert-success">
      {{ statusMessage }}
    </div>
    <div v-if="errorMessage" class="alert alert-error">
      {{ errorMessage }}
    </div>

    <main class="main-grid">
      <!-- Panel Kiri: Form Tambah / Ubah -->
      <section class="card form-card">
        <div class="card-header">
          <h2>{{ editingId ? `Ubah Data Magang (#${editingId})` : 'Tambah Data Magang Baru' }}</h2>
          <button v-if="editingId" @click="resetForm" type="button" class="btn btn-secondary btn-sm">
            Batal Edit
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="crud-form">
          <div class="form-group">
            <label>Nama Mahasiswa</label>
            <input
              v-model="form.student_name"
              type="text"
              placeholder="Contoh: Ahmad Fahim"
              required
            />
            <small v-if="validationErrors.student_name" class="field-error">
              {{ validationErrors.student_name[0] }}
            </small>
          </div>

          <div class="form-group">
            <label>Nama Perusahaan Mitra</label>
            <input
              v-model="form.company_name"
              type="text"
              placeholder="Contoh: PT Telkom Indonesia"
              required
            />
            <small v-if="validationErrors.company_name" class="field-error">
              {{ validationErrors.company_name[0] }}
            </small>
          </div>

          <div class="form-group">
            <label>Posisi / Divisi Magang</label>
            <input
              v-model="form.position"
              type="text"
              placeholder="Contoh: DevOps & Cloud Intern"
              required
            />
            <small v-if="validationErrors.position" class="field-error">
              {{ validationErrors.position[0] }}
            </small>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Tanggal Mulai</label>
              <input v-model="form.start_date" type="date" required />
              <small v-if="validationErrors.start_date" class="field-error">
                {{ validationErrors.start_date[0] }}
              </small>
            </div>

            <div class="form-group">
              <label>Status Magang</label>
              <select v-model="form.status" required>
                <option value="Pending">Pending</option>
                <option value="Diterima">Diterima</option>
                <option value="Selesai">Selesai</option>
              </select>
              <small v-if="validationErrors.status" class="field-error">
                {{ validationErrors.status[0] }}
              </small>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block" :disabled="submitting">
            {{ submitting ? 'Menyimpan...' : editingId ? 'Simpan Perubahan (PUT)' : 'Tambah Data Magang (POST)' }}
          </button>
        </form>

        <!-- Panel Detail Satu Data -->
        <div v-if="selectedInternship" class="detail-box">
          <div class="detail-header">
            <h3>Detail Magang #{{ selectedInternship.id }}</h3>
            <button @click="selectedInternship = null" class="btn-close">&times;</button>
          </div>
          <dl class="detail-list">
            <div>
              <dt>Mahasiswa:</dt>
              <dd>{{ selectedInternship.student_name }}</dd>
            </div>
            <div>
              <dt>Perusahaan:</dt>
              <dd>{{ selectedInternship.company_name }}</dd>
            </div>
            <div>
              <dt>Posisi:</dt>
              <dd>{{ selectedInternship.position }}</dd>
            </div>
            <div>
              <dt>Tanggal Mulai:</dt>
              <dd>{{ selectedInternship.start_date }}</dd>
            </div>
            <div>
              <dt>Status:</dt>
              <dd>
                <span :class="['status-badge', `status-${selectedInternship.status.toLowerCase()}`]">
                  {{ selectedInternship.status }}
                </span>
              </dd>
            </div>
          </dl>
        </div>
      </section>

      <!-- Panel Kanan: Tabel Daftar Magang -->
      <section class="card table-card">
        <div class="card-header">
          <h2>Daftar Magang Mahasiswa ({{ internships.length }})</h2>
          <button @click="fetchInternships" class="btn btn-secondary btn-sm">
            Refresh Data
          </button>
        </div>

        <div v-if="loading" class="empty-state">Memuat data magang...</div>

        <div v-else-if="internships.length === 0" class="empty-state">
          Belum ada data magang. Silakan tambahkan melalui formulir di samping.
        </div>

        <div v-else class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Mahasiswa</th>
                <th>Perusahaan</th>
                <th>Posisi</th>
                <th>Mulai</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in internships" :key="item.id">
                <td>#{{ item.id }}</td>
                <td class="font-semibold">{{ item.student_name }}</td>
                <td>{{ item.company_name }}</td>
                <td>{{ item.position }}</td>
                <td>{{ item.start_date }}</td>
                <td>
                  <span :class="['status-badge', `status-${item.status.toLowerCase()}`]">
                    {{ item.status }}
                  </span>
                </td>
                <td class="action-cell">
                  <button @click="viewDetail(item.id)" class="btn btn-info btn-xs">Detail</button>
                  <button @click="startEdit(item)" class="btn btn-warning btn-xs">Edit</button>
                  <button @click="handleDelete(item.id)" class="btn btn-danger btn-xs">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</template>
