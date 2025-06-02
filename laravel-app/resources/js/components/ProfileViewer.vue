<template>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center">User Profile</h5>

                <div class="info-section">
                    <p><strong>Name:</strong> {{ user?.name ?? 'Unknown User' }}</p>
                    <p><strong>Email:</strong> {{ user?.email ?? 'Unknown Email' }}</p>
                    <p><strong>Area:</strong> {{ user?.area ?? '' }}</p>
                    <p><strong>Group:</strong> {{ user?.group ?? '' }}</p>
                    <p><strong>CFO:</strong> {{ user?.cfo ?? 'Not specified' }}</p>
                    <p><strong>Pins:</strong> {{ pinCount }}</p>
                </div>

                <!-- Show form only if area, group, or cfo is null or empty -->
                <div v-if="!user?.area || !user?.group || !user?.cfo" class="form-section">
                    <div class="form-group">
                        <label for="area">Area</label>
                        <select id="area" v-model="selectedArea" class="form-control">
                            <option value="">-- Select Area --</option>
                            <option v-for="(groups, area) in areaGroups" :key="area" :value="area">
                                Area {{ area }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="group">Group</label>
                        <select
                            id="group"
                            v-model="selectedGroup"
                            class="form-control"
                            :disabled="!selectedArea"
                        >
                            <option value="">-- Select Group --</option>
                            <option
                                v-for="group in areaGroups[selectedArea]"
                                :key="group"
                                :value="group"
                            >
                                Group {{ group }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cfo">CFO</label>
                        <select id="cfo" v-model="selectedCFO" class="form-control">
                            <option value="">-- Select CFO --</option>
                            <option value="BUKLOD">BUKLOD</option>
                            <option value="KADIWA">KADIWA</option>
                            <option value="BINHI">BINHI</option>
                        </select>
                    </div>

                    <button
                        @click="saveProfile"
                        class="btn btn-primary btn-block"
                        :disabled="loading"
                    >
                        {{ loading ? 'Saving...' : 'Save' }}
                    </button>

                    <p v-if="successMessage" class="alert alert-success mt-3">
                        {{ successMessage }}
                    </p>
                    <p v-if="errorMessage" class="alert alert-danger mt-3">
                        {{ errorMessage }}
                    </p>
                </div>

                <div v-else class="text-center mt-3 text-muted">
                    Your profile is already complete.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const user = window.user
const areaGroups = window.areaGroups
const pinCount = window.pinCount

const selectedArea = ref(user?.area ?? '')
const selectedGroup = ref(user?.group ?? '')
const selectedCFO = ref(user?.cfo ?? '')

const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

watch(selectedArea, (newArea) => {
    if (!areaGroups[newArea]?.includes(Number(selectedGroup.value))) {
        selectedGroup.value = ''
    }
})

const saveProfile = async () => {
    loading.value = true
    successMessage.value = ''
    errorMessage.value = ''

    try {
        await axios.post('/profile/update', {
            area: selectedArea.value || null,
            group: selectedGroup.value || null,
            cfo: selectedCFO.value || null,
        })

        successMessage.value = 'Profile updated successfully!'

        setTimeout(() => {
            window.location.href = '/maps'
        }, 1000)
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || 'Failed to update profile.'
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.container {
    max-width: 600px;
    margin: auto;
    padding: 1rem;
}

.card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: none;
}

.card-title {
    font-size: 1.5rem;
    margin-bottom: 1.25rem;
    font-weight: 600;
}

.info-section {
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
    appearance: none;
    background-color: #fff;
    border: 1px solid #ced4da;
    position: relative;
    z-index: 1;
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="16" viewBox="0 0 24 24" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2rem;
}

.btn {
    width: 100%;
    padding: 0.6rem;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 6px;
}

.alert {
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-size: 0.95rem;
}

@media (max-width: 576px) {
    .card-title {
        font-size: 1.25rem;
    }

    .form-control {
        font-size: 16px; /* Prevents iOS zoom */
        min-height: 44px;
    }

    .btn {
        font-size: 0.95rem;
    }
}
</style>
