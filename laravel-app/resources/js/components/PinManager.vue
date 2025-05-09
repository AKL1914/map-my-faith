<template>
    <div class="container my-4">
        <h2>Pin Manager</h2>

        <!-- Search Input -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search by notes, name, or email..."
                        v-model="searchQuery"
                        @input="debouncedSearch"
                    />
                    <button class="btn btn-outline-secondary" type="button" @click="clearSearch">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Pins Table -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Pins</h5>
                <div v-if="isLoading" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div v-else-if="error" class="alert alert-danger">
                    {{ error }}
                </div>
                <div v-else>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Campaign</th>
                            <th>Coordinates</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="pin in pins.data" :key="pin.id">
                            <td>{{ pin.id }}</td>
                            <td>{{ pin.user?.name }} ({{ pin.user?.email }})</td>
                            <td>{{ pin.campaign?.name }}</td>
                            <td>{{ parseFloat(pin.latitude).toFixed(4) }}, {{ parseFloat(pin.longitude).toFixed(4) }}</td>
                            <td>{{ pin.notes || '-' }}</td>
                            <td>
                                    <span
                                        class="badge"
                                        :class="pin.is_accepted ? 'bg-success' : 'bg-secondary'"
                                    >
                                        {{ pin.is_accepted ? 'Accepted' : 'Pending' }}
                                    </span>
                            </td>
                            <td>
                                <a
                                    class="btn btn-sm btn-info me-2"
                                    :href="`/admin/pin/${pin.id}`"
                                    target="_blank"
                                >
                                    View
                                </a>
                                <button
                                    class="btn btn-sm btn-warning me-2"
                                    @click="toggleAcceptance(pin)"
                                >
                                    {{ pin.is_accepted ? 'Unaccept' : 'Accept' }}
                                </button>
                                <button
                                    class="btn btn-sm btn-danger"
                                    @click="deletePin(pin.id)"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="pins.data?.length === 0">
                            <td colspan="7" class="text-center">No pins found.</td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav v-if="pins.total > pins.per_page">
                        <ul class="pagination justify-content-center">
                            <li
                                class="page-item"
                                :class="{ disabled: pins.current_page === 1 }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="fetchPins(pins.current_page - 1)"
                                >
                                    Previous
                                </a>
                            </li>
                            <li
                                class="page-item"
                                v-for="page in paginationRange"
                                :key="page"
                                :class="{ active: page === pins.current_page }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="fetchPins(page)"
                                >
                                    {{ page }}
                                </a>
                            </li>
                            <li
                                class="page-item"
                                :class="{ disabled: pins.current_page === pins.last_page }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="fetchPins(pins.current_page + 1)"
                                >
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import debounce from 'lodash/debounce';

export default {
    name: 'PinManager',
    data() {
        return {
            pins: {
                data: [],
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0
            },
            searchQuery: '',
            pageRange: 5,
            isLoading: false,
            error: null
        };
    },
    computed: {
        paginationRange() {
            const start = Math.max(1, this.pins.current_page - Math.floor(this.pageRange / 2));
            const end = Math.min(this.pins.last_page, start + this.pageRange - 1);
            return Array.from({ length: end - start + 1 }, (_, i) => start + i);
        }
    },
    created() {
        this.debouncedSearch = debounce(this.searchPins, 300);
    },
    mounted() {
        this.fetchPins();
    },
    methods: {
        async fetchPins(page = 1) {
            this.isLoading = true;
            this.error = null;
            try {
                const params = {
                    page,
                    search: this.searchQuery
                };
                const response = await axios.get('/api/pins', { params });
                console.log('API Response:', response.data); // For debugging
                this.pins = response.data;
            } catch (error) {
                console.error('Error fetching pins:', error);
                this.error = 'Failed to load pins. Please try again.';
            } finally {
                this.isLoading = false;
            }
        },
        searchPins() {
            this.fetchPins(1);
        },
        clearSearch() {
            this.searchQuery = '';
            this.fetchPins(1);
        },
        async toggleAcceptance(pin) {
            this.isLoading = true;
            this.error = null;
            try {
                await axios.patch(`/api/pins/${pin.id}`, {
                    is_accepted: !pin.is_accepted
                });
                this.fetchPins(this.pins.current_page);
            } catch (error) {
                console.error('Error updating pin:', error);
                this.error = 'Failed to update pin. Please try again.';
            } finally {
                this.isLoading = false;
            }
        },
        async deletePin(id) {
            if (confirm('Are you sure you want to delete this pin?')) {
                this.isLoading = true;
                this.error = null;
                try {
                    await axios.delete(`/api/pins/${id}`);
                    this.fetchPins(this.pins.current_page);
                } catch (error) {
                    console.error('Error deleting pin:', error);
                    this.error = 'Failed to delete pin. Please try again.';
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
};
</script>

<style scoped>
.badge {
    font-size: 0.85rem;
    padding: 0.4em 0.6em;
}

.pagination {
    margin-top: 1rem;
}
</style>
