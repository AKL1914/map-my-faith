<template>
    <div class="container my-4">
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
                            <th style="min-width: 240px;">Actions</th>
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
                                <div class="d-flex flex-wrap gap-1">
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
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Per Page -->
                <div class="row align-items-center mt-3 gy-2">
                    <div class="col-12 col-md-auto">
                        <label for="itemsPerPage" class="me-2">Items per page:</label>
                        <select
                            id="itemsPerPage"
                            v-model.number="perPage"
                            @change="fetchUsers"
                            class="form-select d-inline-block w-auto"
                        >
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                        </select>
                    </div>
                    <div class="col">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-end mb-0 flex-wrap">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <button class="page-link" @click="currentPage--; fetchUsers()">Previous</button>
                                </li>
                                <li
                                    class="page-item"
                                    v-for="page in totalPages"
                                    :key="page"
                                    :class="{ active: currentPage === page }"
                                >
                                    <button class="page-link" @click="currentPage = page; fetchUsers()">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="currentPage++; fetchUsers()">Next</button>
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
        // Create debounced version of fetchUsers
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
            axios.get('/api/users', { params })
                .then(response => {
                    this.users = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.perPage = response.data.per_page;
                    this.totalPages = response.data.last_page;
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    this.users = [];
                    this.totalPages = 1;
                });
        },
        toggleAdmin(user) {
            const newStatus = !user.is_admin;
            axios.put(`/api/users/${user.id}/admin`, { is_admin: newStatus })
                .then(() => this.fetchUsers())
                .catch(error => {
                    console.error('Error updating admin status:', error);
                });
        },
        toggleActivation(user) {
            const newStatus = !user.is_activated;
            axios.put(`/api/users/${user.id}/activation`, { is_activated: newStatus })
                .then(() => this.fetchUsers())
                .catch(error => {
                    console.error('Error updating activation status:', error);
                });
        },
        activateAll() {
            axios.post('/api/users/activate-all')
                .then(() => this.fetchUsers())
                .catch(error => {
                    console.error('Error activating all users:', error);
                });
        },
        deactivateAll() {
            axios.post('/api/users/deactivate-all')
                .then(() => this.fetchUsers())
                .catch(error => {
                    console.error('Error deactivating all users:', error);
                });
        },
    },
};
</script>
