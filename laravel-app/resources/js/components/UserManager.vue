<template>
    <div class="container my-4">
        <h2>User Manager</h2>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Users</h5>
                <div class="mb-3">
                    <button class="btn btn-success me-2" @click="activateAll">Activate All</button>
                    <button class="btn btn-danger" @click="deactivateAll">Deactivate All</button>
                </div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Activated</th>
                        <th style="width: 260px;">Actions</th>
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
                            <button
                                class="btn btn-sm me-1"
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
                        </td>
                    </tr>
                    <tr v-if="users.length === 0">
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'UserManager',
    data() {
        return {
            users: [],
        };
    },
    mounted() {
        this.fetchUsers();
    },
    methods: {
        fetchUsers() {
            axios.get('/api/users')
                .then(response => {
                    this.users = response.data;
                });
        },
        toggleAdmin(user) {
            const newStatus = !user.is_admin;
            axios.put(`/api/users/${user.id}/admin`, { is_admin: newStatus })
                .then(() => this.fetchUsers());
        },
        toggleActivation(user) {
            const newStatus = !user.is_activated;
            axios.put(`/api/users/${user.id}/activation`, { is_activated: newStatus })
                .then(() => this.fetchUsers());
        },
        activateAll() {
            axios.post('/api/users/activate-all')
                .then(() => this.fetchUsers());
        },
        deactivateAll() {
            axios.post('/api/users/deactivate-all')
                .then(() => this.fetchUsers());
        }
    }
};
</script>
