<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trophy text-warning"></i> Leaderboard</h1>
            <button @click="fetchLeaderboard" :disabled="loading" class="btn btn-sm btn-secondary">
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span v-else><i class="bi bi-arrow-clockwise"></i> Refresh</span>
            </button>
        </div>

        <!-- Top 10 Users -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users"></i> Top 10 Users</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Area</th>
                            <th>Pins</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(entry, index) in leaderboardData" :key="entry.id">
                            <td><i class="fas fa-crown" :class="{ 'text-warning': index === 0 }"></i> {{ index + 1 }}</td>
                            <td>{{ entry.name }}</td>
                            <td>{{ entry.area || 'N/A' }}</td>
                            <td>{{ entry.pinCount }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Suburbs -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marker-alt"></i> Top Suburb Legends</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Suburb</th>
                            <th>Total Pins</th>
                            <th>Top User</th>
                            <th>User's Pins in Suburb</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(suburb, index) in topSuburbs" :key="suburb.suburb">
                            <td>{{ index + 1 }}</td>
                            <td>{{ suburb.suburb }}</td>
                            <td>{{ suburb.totalPins }}</td>
                            <td>{{ suburb.user_name || 'N/A' }}</td>
                            <td>{{ suburb.userPins || 0 }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Areas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map"></i> Top Areas (By User)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Area</th>
                            <th>Pins</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(area, index) in topAreas" :key="area.area">
                            <td>{{ index + 1 }}</td>
                            <td>{{ area.area || 'N/A' }}</td>
                            <td>{{ area.pinCount }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const leaderboardData = ref([]);
const topSuburbs = ref([]);
const topAreas = ref([]);
const loading = ref(false);

async function fetchLeaderboard() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/leaderboard');
        leaderboardData.value = data.leaderboardData;
        topSuburbs.value = data.topSuburbs;
        topAreas.value = data.topAreas;
    } catch (e) {
        // Optionally show error toast
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchLeaderboard();
});
</script>

<style scoped>
.table th, .table td {
    vertical-align: middle;
}
</style>
