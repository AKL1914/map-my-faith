<template>
    <div class="container my-4">
        <h2>Campaign Manager</h2>

        <!-- Add / Edit Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ editMode ? 'Edit' : 'Add' }} Campaign</h5>
                <form @submit.prevent="submitForm">
                    <div class="mb-3">
                        <label for="campaignName" class="form-label">Campaign Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="campaignName"
                            v-model="form.name"
                            required
                        />
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="isActive"
                            v-model="form.is_active"
                        />
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary me-2">
                        {{ editMode ? 'Update' : 'Create' }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-secondary"
                        v-if="editMode"
                        @click="resetForm"
                    >
                        Cancel
                    </button>
                </form>
            </div>
        </div>

        <!-- Campaigns Table -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Campaigns</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="campaign in campaigns" :key="campaign.id">
                        <td>{{ campaign.id }}</td>
                        <td>{{ campaign.name }}</td>
                        <td>
                <span class="badge" :class="campaign.is_active ? 'bg-success' : 'bg-secondary'">
                  {{ campaign.is_active ? 'Active' : 'Inactive' }}
                </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" @click="editCampaign(campaign)">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger" @click="deleteCampaign(campaign.id)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="campaigns.length === 0">
                        <td colspan="4" class="text-center">No campaigns found.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'CampaignManager',
    data() {
        return {
            campaigns: [],
            form: {
                id: null,
                name: '',
                is_active: false
            },
            editMode: false
        };
    },
    mounted() {
        this.fetchCampaigns();
    },
    methods: {
        fetchCampaigns() {
            axios.get('/api/campaigns')
                .then(response => {
                    this.campaigns = response.data;
                });
        },
        submitForm() {
            const payload = {
                name: this.form.name,
                is_active: this.form.is_active
            };

            if (this.editMode) {
                axios.put(`/api/campaigns/${this.form.id}`, payload)
                    .then(() => {
                        this.fetchCampaigns();
                        this.resetForm();
                    });
            } else {
                axios.post('/api/campaigns', payload)
                    .then(() => {
                        this.fetchCampaigns();
                        this.resetForm();
                    });
            }
        },
        editCampaign(campaign) {
            this.editMode = true;
            this.form = {
                id: campaign.id,
                name: campaign.name,
                is_active: !!campaign.is_active
            };
        },
        deleteCampaign(id) {
            if (confirm('Are you sure you want to delete this campaign?')) {
                axios.delete(`/api/campaigns/${id}`)
                    .then(() => this.fetchCampaigns());
            }
        },
        resetForm() {
            this.editMode = false;
            this.form = {
                id: null,
                name: '',
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
</style>
