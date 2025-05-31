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
                        <button
                            class="btn btn-success btn-sm"
                            @click="activateAll"
                            title="Activate all users"
                        >
                            <i class="bi bi-check-circle"></i>
                        </button>
                        <button
                            class="btn btn-danger btn-sm"
                            @click="deactivateAll"
                            title="Deactivate all users"
                        >
                            <i class="bi bi-x-circle"></i>
                        </button>
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
                            <th>CFO</th>
                            <th style="min-width: 180px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.id }}</td>
                            <td>
                                <input
                                    type="text"
                                    v-model="user.name"
                                    class="form-control form-control-sm w-100"
                                    style="min-width: 200px;"
                                    placeholder="Name"
                                />
                            </td>
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
                                <select
                                    v-model="user.cfo"
                                    class="form-select form-select-sm"
                                    style="min-width: 120px;"
                                >
                                    <option value="" disabled>Select CFO</option>
                                    <option value="BUKLOD">BUKLOD</option>
                                    <option value="KADIWA">KADIWA</option>
                                    <option value="BINHI">BINHI</option>
                                </select>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <button
                                        class="btn btn-sm"
                                        :class="user.is_admin ? 'btn-secondary' : 'btn-warning'"
                                        @click="toggleAdmin(user)"
                                        :title="user.is_admin ? 'Revoke admin access' : 'Grant admin access'"
                                    >
                                        <i :class="user.is_admin ? 'bi bi-person-x' : 'bi bi-person-check'"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm"
                                        :class="user.is_activated ? 'btn-danger' : 'btn-success'"
                                        @click="toggleActivation(user)"
                                        :title="user.is_activated ? 'Deactivate user' : 'Activate user'"
                                    >
                                        <i :class="user.is_activated ? 'bi bi-toggle-off' : 'bi bi-toggle-on'"></i>
                                    </button>
                                    <button
                                        class="btn btn-primary btn-sm"
                                        @click="updateUser(user)"
                                        title="Save changes"
                                    >
                                        <i class="bi bi-save"></i>
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
                                    <button class="page-link" @click="prevPage" title="Previous page">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                </li>
                                <li
                                    class="page-item"
                                    v-for="page in totalPages"
                                    :key="page"
                                    :class="{ active: currentPage === page }"
                                >
                                    <button class="page-link" @click="goToPage(page)" :title="`Go to page ${page}`">
                                        {{ page }}
                                    </button>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <button class="page-link" @click="nextPage" title="Next page">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
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
import { useToast, POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const toast = useToast();

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

                    this.users.forEach((user) => {
                        user.area = user.area || '';
                        user.group = user.group || '';
                        user.name = user.name || '';
                    });
                })
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
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
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
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
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
                    });
                });
        },
        activateAll() {
            axios
                .post('/api/users/activate-all')
                .then(() => this.fetchUsers())
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
                    });
                });
        },
        deactivateAll() {
            axios
                .post('/api/users/deactivate-all')
                .then(() => this.fetchUsers())
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
                    });
                });
        },
        updateUser(user) {
            axios
                .put(`/api/users/${user.id}`, {
                    name: user.name,
                    area: user.area,
                    group: user.group,
                    cfo: user.cfo,
                })
                .then(() => {
                    toast.success('User info updated.', {
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
                    });
                    this.fetchUsers();
                })
                .catch((error) => {
                    toast.error(error.response.data.message, {
                        position: POSITION.TOP_CENTER,
                        timeout: 5000,
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

<style scoped>
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
    line-height: 1;
}

table input.form-control-sm {
    min-width: 100px;
}

.btn i {
    pointer-events: none;
    vertical-align: middle;
}
</style>
