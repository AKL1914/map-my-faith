<template>
    <h1 class="h3 mb-4 text-gray-800">Event Participants</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ eventName }} Participants</h6>
        </div>

        <div class="card-body">
            <p class="mb-4">Total participants: <strong>{{ meta.total }}</strong></p>

            <div v-if="loading" class="text-center my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="mt-2 text-muted">Loading participants...</div>
            </div>

            <div v-else>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Distance</th>
                            <th>Created Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="participant in participants" :key="participant.id">
                            <td>{{ participant.id }}</td>
                            <td>{{ participant.user_name }}</td>
                            <td>{{ participant.distance }}</td>
                            <td>{{ formatDate(participant.created_at) }}</td>
                        </tr>
                        <tr v-if="participants.length === 0">
                            <td colspan="4" class="text-center text-muted">No participants found.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <nav v-if="meta.last_page > 1" class="mt-3" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li
                            class="page-item"
                            :class="{ disabled: meta.current_page === 1 }"
                        >
                            <a href="#" class="page-link" @click.prevent="changePage(meta.current_page - 1)">
                                &laquo; Previous
                            </a>
                        </li>

                        <li
                            v-for="page in meta.last_page"
                            :key="page"
                            class="page-item"
                            :class="{ active: page === meta.current_page }"
                        >
                            <a href="#" class="page-link" @click.prevent="changePage(page)">
                                {{ page }}
                            </a>
                        </li>

                        <li
                            class="page-item"
                            :class="{ disabled: meta.current_page === meta.last_page }"
                        >
                            <a href="#" class="page-link" @click.prevent="changePage(meta.current_page + 1)">
                                Next &raquo;
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>


<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const participants = ref([]);
const eventName = ref('');
const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});
const loading = ref(false);

// Get Event ID from global window object
const eventId = window.pinevent?.id || null;

const fetchParticipants = (page = 1) => {
    if (!eventId) {
        console.error('Event ID not found in window.pinevent');
        return;
    }

    loading.value = true;

    axios
        .get(`/api/events/${eventId}/participants?page=${page}`)
        .then((response) => {
            eventName.value = response.data.event_name;
            participants.value = response.data.data;
            meta.value = response.data.meta;
        })
        .catch((error) => {
            console.error('Error fetching participants:', error);
        })
        .finally(() => {
            loading.value = false;
        });
};

const changePage = (page: number) => {
    if (page < 1 || page > meta.value.last_page) return;
    fetchParticipants(page);
};

const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

onMounted(() => {
    fetchParticipants();
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

p {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #555;
}
</style>
