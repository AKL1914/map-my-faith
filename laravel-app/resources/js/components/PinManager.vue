<template>
    <div class="container my-4">
        <h1 class="h3 mb-4 text-gray-800">Pins</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Filters
                </h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fromDate" class="form-label">From Date</label>
                        <input
                            id="fromDate"
                            type="date"
                            class="form-control"
                            v-model="fromDate"
                            @input="debouncedSearch"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="toDate" class="form-label">To Date</label>
                        <input
                            id="toDate"
                            type="date"
                            class="form-control"
                            v-model="toDate"
                            @input="debouncedSearch"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Pins
                </h6>
            </div>
            <div class="card-body">
                <div v-if="isLoading" class="text-center my-3">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-else-if="error" class="alert alert-danger">
                    {{ error }}
                </div>

                <div v-else>
                    <!-- Responsive Table -->
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="d-none d-md-table-header-group">
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
                            <tr v-for="pin in pins.data" :key="pin.id" class="d-block d-md-table-row mb-3 mb-md-0 border rounded p-2 p-md-0">
                                <td class="d-block d-md-table-cell"><strong>ID:</strong> {{ pin.id }}</td>
                                <td class="d-block d-md-table-cell"><strong>User:</strong> {{ pin.user?.name }} ({{ pin.user?.email }})</td>
                                <td class="d-block d-md-table-cell"><strong>Campaign:</strong> {{ pin.campaign?.name }}</td>
                                <td class="d-block d-md-table-cell"><strong>Coordinates:</strong> {{ parseFloat(pin.latitude).toFixed(4) }}, {{ parseFloat(pin.longitude).toFixed(4) }}</td>
                                <td class="d-block d-md-table-cell"><strong>Notes:</strong> {{ pin.notes || '-' }}</td>
                                <td class="d-block d-md-table-cell">
                                    <strong>Status:</strong>
                                    <span class="badge" :class="pin.is_accepted ? 'bg-success' : 'bg-secondary'">
                      {{ pin.is_accepted ? 'Accepted' : 'Pending' }}
                    </span>
                                </td>
                                <td class="d-block d-md-table-cell">
                                    <div class="d-grid gap-2 d-md-flex">
                                        <a
                                            class="btn btn-sm btn-info"
                                            :href="`/admin/pin/${pin.id}`"
                                            target="_blank"
                                        >
                                            View
                                        </a>
                                        <button
                                            class="btn btn-sm btn-warning"
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
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="pins.data?.length === 0">
                                <td colspan="7" class="text-center">No pins found.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav v-if="pins.total > pins.per_page">
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{ disabled: pins.current_page === 1 }">
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
                                <a class="page-link" href="#" @click.prevent="fetchPins(page)">
                                    {{ page }}
                                </a>
                            </li>
                            <li class="page-item" :class="{ disabled: pins.current_page === pins.last_page }">
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
import { debounce } from 'lodash';

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
            fromDate: '',
            toDate: '',
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
                    search: this.searchQuery,
                    from_date: this.fromDate,
                    to_date: this.toDate
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
            this.fromDate = '';
            this.toDate = '';
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
                    await axios.delete(`/api/pin/${id}`);
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

@media (max-width: 768px) {
    table tr {
        margin-bottom: 1rem;
    }

    td {
        border-top: none !important;
    }
}
</style>
