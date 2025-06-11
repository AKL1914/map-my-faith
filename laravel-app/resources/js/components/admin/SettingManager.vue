<template>
    <h1 class="h3 mb-4 text-gray-800">Settings Manager</h1>

    <!-- Settings Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ editMode ? 'Edit Setting' : 'Add Setting' }}
            </h6>
        </div>
        <div class="card-body">
            <form @submit.prevent="submitForm">
                <div class="form-group mb-3">
                    <label for="settingName">Setting Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="settingName"
                        v-model="form.name"
                        required
                        :readonly="editMode"
                    />
                </div>

                <div class="form-group mb-3">
                    <label for="settingValue">Value</label>
                    <input
                        type="text"
                        class="form-control"
                        id="settingValue"
                        v-model="form.value"
                    />
                </div>

                <div class="form-group mb-3">
                    <label for="settingNote">Note</label>
                    <textarea
                        class="form-control"
                        id="settingNote"
                        v-model="form.note"
                        rows="2"
                    ></textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="isEnabled"
                        v-model="form.enabled"
                    />
                    <label class="form-check-label" for="isEnabled">Enabled</label>
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

    <!-- Settings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Settings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th class="text-center" style="min-width: 160px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="setting in settings" :key="setting.id">
                        <td>{{ setting.id }}</td>
                        <td>{{ setting.name }}</td>
                        <td>{{ setting.value }}</td>
                        <td>{{ setting.note }}</td>
                        <td>
                            <span class="badge px-2 py-1 text-white" :class="setting.enabled ? 'bg-success' : 'bg-secondary'">
                                {{ setting.enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                <button class="btn btn-sm btn-warning" @click="editSetting(setting)">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteSetting(setting.id)">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="settings.length === 0">
                        <td colspan="6" class="text-center text-muted">No settings found.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SettingManager',
    data() {
        return {
            settings: [],
            form: {
                id: null,
                name: '',
                value: '',
                note: '',
                enabled: false
            },
            editMode: false
        };
    },
    mounted() {
        this.fetchSettings();
    },
    methods: {
        fetchSettings() {
            axios.get('/admin/settings')
                .then(response => {
                    this.settings = response.data;
                });
        },
        submitForm() {
            const payload = {
                name: this.form.name,
                value: this.form.value,
                note: this.form.note,
                enabled: this.form.enabled
            };

            if (this.editMode) {
                axios.put(`/admin/settings/${this.form.id}`, payload)
                    .then(() => {
                        this.fetchSettings();
                        this.resetForm();
                    });
            } else {
                axios.post('/admin/settings', payload)
                    .then(() => {
                        this.fetchSettings();
                        this.resetForm();
                    });
            }
        },
        editSetting(setting) {
            this.editMode = true;
            this.form = {
                id: setting.id,
                name: setting.name,
                value: setting.value,
                note: setting.note,
                enabled: !!setting.enabled
            };
        },
        deleteSetting(id) {
            if (confirm('Are you sure you want to delete this setting?')) {
                axios.delete(`/admin/settings/${id}`)
                    .then(() => this.fetchSettings());
            }
        },
        resetForm() {
            this.editMode = false;
            this.form = {
                id: null,
                name: '',
                value: '',
                note: '',
                enabled: false
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
