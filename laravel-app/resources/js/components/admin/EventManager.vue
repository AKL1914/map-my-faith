<template>
    <h1 class="h3 mb-4 text-gray-800">Event Manager</h1>

    <!-- Event Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ editMode ? 'Edit Event' : 'Add Event' }}
            </h6>
        </div>
        <div class="card-body">
            <form @submit.prevent="submitForm">
                <div class="form-group mb-3">
                    <label for="eventName">Event Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="eventName"
                        v-model="form.name"
                        required
                    />
                </div>

                <div class="form-group mb-3">
                    <label for="eventDescription">Description</label>
                    <textarea
                        class="form-control"
                        id="eventDescription"
                        v-model="form.description"
                        rows="3"
                        required
                    ></textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="isActive"
                        v-model="form.is_active"
                    />
                    <label class="form-check-label" for="isActive">Active</label>
                </div>

                <div class="d-grid d-md-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ editMode ? 'Update' : 'Create' }}
                    </button>
                    <button
                        v-if="editMode"
                        type="button"
                        class="btn btn-secondary"
                        @click="resetForm"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Events</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-center" style="min-width: 280px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="event in events" :key="event.id">
                        <td>{{ event.id }}</td>
                        <td>{{ event.name }}</td>
                        <td>{{ event.description }}</td>
                        <td>
                                <span class="badge px-2 py-1" :class="event.is_active ? 'bg-success' : 'bg-secondary'">
                                    {{ event.is_active ? 'Active' : 'Inactive' }}
                                </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                <button class="btn btn-sm btn-warning" @click="editEvent(event)">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteEvent(event.id)">
                                    Delete
                                </button>
                                <a :href="`/admin/event/${event.id}/pins`" class="btn btn-sm btn-info text-white">
                                    View
                                </a>
                                <a :href="`/admin/event/${event.id}/participants`" class="btn btn-sm btn-outline-primary">
                                    Participants
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="events.length === 0">
                        <td colspan="5" class="text-center text-muted">No events found.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'EventManager',
    data() {
        return {
            events: [],
            form: {
                id: null,
                name: '',
                description: '',
                is_active: false
            },
            editMode: false
        };
    },
    mounted() {
        this.fetchEvents();
    },
    methods: {
        fetchEvents() {
            axios.get('/api/events')
                .then(response => {
                    this.events = response.data;
                });
        },
        submitForm() {
            const payload = {
                name: this.form.name,
                description: this.form.description,
                is_active: this.form.is_active
            };

            if (this.editMode) {
                axios.put(`/api/events/${this.form.id}`, payload)
                    .then(() => {
                        this.fetchEvents();
                        this.resetForm();
                    });
            } else {
                axios.post('/api/events', payload)
                    .then(() => {
                        this.fetchEvents();
                        this.resetForm();
                    });
            }
        },
        editEvent(event) {
            this.editMode = true;
            this.form = {
                id: event.id,
                name: event.name,
                description: event.description,
                is_active: !!event.is_active
            };
        },
        deleteEvent(id) {
            if (confirm('Are you sure you want to delete this event?')) {
                axios.delete(`/api/events/${id}`)
                    .then(() => this.fetchEvents());
            }
        },
        resetForm() {
            this.editMode = false;
            this.form = {
                id: null,
                name: '',
                description: '',
                is_active: false
            };
        }
    }
};
</script>

<style scoped>
.badge {
    font-size: 0.85rem;
    padding: 0.4em 0.6em;
}

@media (max-width: 576px) {
    .btn {
        width: 100%;
    }

    .d-flex.flex-wrap.gap-1 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
}
</style>
