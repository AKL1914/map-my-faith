<template>
    <div class="container my-4">
        <h2>{{ eventName }} Pins</h2>

        <div v-if="loading" class="text-center my-3">
            Loading pins...
        </div>

        <div v-else>
            <table class="table table-striped table-responsive">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Distance</th>
                    <th>Updated At</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="pin in pins" :key="pin.id">
                    <td data-label="ID">{{ pin.id }}</td>
                    <td data-label="User Name">{{ pin.user ? pin.user.name : '—' }}</td>
                    <td data-label="Latitude">{{ pin.latitude }}</td>
                    <td data-label="Longitude">{{ pin.longitude }}</td>
                    <td data-label="Distance">{{ pin.distance ?? '—' }}</td>
                    <td data-label="Updated At">{{ formatDate(pin.updated_at) }}</td>
                </tr>
                <tr v-if="pins.length === 0">
                    <td colspan="6" class="text-center">No pins found.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const pins = ref([]);
const eventName = ref('');
const loading = ref(false);

// Get Event ID from global window object
const eventId = window.pinevent?.id || null;

const fetchPins = async () => {
    if (!eventId) {
        console.error('Event ID not found in window.pinevent');
        return;
    }

    loading.value = true;

    try {
        const response = await axios.get(`/api/events/${eventId}/pins`);
        eventName.value = response.data.event_name;
        pins.value = response.data.data;
    } catch (error) {
        console.error('Error fetching event pins:', error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

onMounted(() => {
    fetchPins();
});
</script>

<style scoped>
.table-responsive {
    overflow-x: auto;
}

.table td,
.table th {
    border: 1px solid #dee2e6;
}

/* Mobile-friendly card-style table */
@media (max-width: 576px) {
    table thead {
        display: none;
    }

    table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    table tbody tr td {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #ddd;
    }

    table tbody tr td:last-child {
        border-bottom: none;
    }

    table tbody tr td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #333;
    }
}
</style>
