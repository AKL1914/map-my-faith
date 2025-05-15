<template>
    <div class="container my-4">
        <!-- Toast notification -->
        <Toast />
        <h2 class="mb-3">User Manager</h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Users</h5>

                <!-- Actions & Search -->
                <div class="row gy-2 gx-3 align-items-center mb-3">
                    <div class="col-12 col-md-auto d-flex flex-wrap gap-2">
                        <button class="btn btn-success" @click="activateAll">Activate All</button>
                        <button class="btn btn-danger" @click="deactivateAll">Deactivate All</button>
                    </div>

                    <div class="col-12 col-md">
                        <div class="input-group">
                            <input
                                type="text"
                                v-model="searchQuery"
                                class="form-control"
                                placeholder="Search by name or email..."
                                @input="debouncedFetchUsers"
                            />
                        </div>
                    </div>
                </div>

                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Activated</th>
                            <th>Area</th>
                            <th>Group</th>
                            <th style="min-width: 260px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                  <span class="badge" :class="user.is_admin ? 'bg-success' : 'bg-secondary'">
                    {{ user.is_admin ? 'Yes' : 'No' }}
                  </span>
                            </td>
                            <td>
                  <span class="badge" :class="user.is_activated ? 'bg-success' : 'bg-secondary'">
                    {{ user.is_activated ? 'Yes' : 'No' }}
                  </span>
                            </td>
                            <td>
                                <input
                                    type="text"
                                    v-model="user.area"
                                    class="form-control form-control-sm"
                                    placeholder="Area"
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    v-model="user.group"
                                    class="form-control form-control-sm"
                                    placeholder="Group"
                                />
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <button
                                        class="btn btn-sm"
                                        :class="user.is_admin ? 'btn-secondary' : 'btn-warning'"
                                        @click="toggleAdmin(user)"
                                    >
                                        {{ user.is_admin ? 'Revoke Admin' : 'Make Admin' }}
                                    </button>
                                    <button
                                        class="btn btn-sm"
                                        :class="user.is_activated ? 'btn-danger' : 'btn-success'"
                                        @click="toggleActivation(user)"
                                    >
                                        {{ user.is_activated ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button
                                        class="btn btn-primary btn-sm"
                                        @click="updateAreaGroup(user)"
                                    >
                                        Save Area/Group
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="8" class="text-center">No users found.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Per Page -->
                <div class="row align-items-center mt-3 gy-2">
                    <div class="col">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <button class="page-link" @click="prevPage">Previous</button>
                                </li>
                                <li
                                    class="page-item"
                                    v-for="page in totalPages"
                                    :key="page"
                                    :class="{ active: currentPage === page }"
                                >
                                    <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="nextPage">Next</button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useToast, POSITION } from 'vue-toastification'; // Import Toast and POSITION
import 'vue-toastification/dist/index.css'; // Import Toast CSS
// Initialize toast
const toast = useToast()
export default {
    name: 'UserManager',
    data() {
        return {
            users: [],
            searchQuery: '',
            currentPage: 1,
            perPage: 10,
            totalPages: 1,
        };
    },
    created() {
        // Create debounced version of fetchUsers (make sure you have a debounce function globally or import one)
        this.debouncedFetchUsers = window.debounce(this.fetchUsers, 300);
    },
    mounted() {
        this.fetchUsers();
    },
    methods: {
        fetchUsers() {
            const params = {
                page: this.currentPage,
                per_page: this.perPage,
                search: this.searchQuery || undefined,
            };
            axios
                .get('/api/users', { params })
                .then((response) => {
                    this.users = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.perPage = response.data.per_page;
                    this.totalPages = response.data.last_page;

                    // Ensure area and group have values (in case API returns null)
                    this.users.forEach((user) => {
                        if (!user.area) user.area = '';
                        if (!user.group) user.group = '';
                    });
                })
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                    this.users = [];
                    this.totalPages = 1;
                });
        },
        toggleAdmin(user) {
            const newStatus = !user.is_admin;
            axios
                .put(`/api/users/${user.id}/admin`, { is_admin: newStatus })
                .then(() => this.fetchUsers())
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                });
        },
        toggleActivation(user) {
            const newStatus = !user.is_activated;
            axios
                .put(`/api/users/${user.id}/activation`, { is_activated: newStatus })
                .then(() => this.fetchUsers())
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                });
        },
        activateAll() {
            axios
                .post('/api/users/activate-all')
                .then(() => this.fetchUsers())
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                });
        },
        deactivateAll() {
            axios
                .post('/api/users/deactivate-all')
                .then(() => this.fetchUsers())
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                });
        },
        updateAreaGroup(user) {
            axios
                .put(`/api/users/${user.id}/area-group`, {
                    area: user.area,
                    group: user.group,
                })
                .then(() => {
                    toast.success('User area and group updated.', {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                    // alert('User area and group updated.');
                    this.fetchUsers();
                })
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER, // Center the toast at the top
                        timeout: 5000
                    });
                });
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.fetchUsers();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.fetchUsers();
            }
        },
        goToPage(page) {
            if (page !== this.currentPage) {
                this.currentPage = page;
                this.fetchUsers();
            }
        },
    },
};
</script>
