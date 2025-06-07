<template>
    <div class="container-fluid px-4">
        <h1 class="h3 mb-4 text-gray-800">Campaigns</h1>

        <!-- Add/Edit Campaign Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ editMode ? 'Edit' : 'Add' }} Campaign
                </h6>
            </div>
            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="mb-3">
                        <label for="campaignName" class="form-label">Campaign Name</label>
                        <input
                            type="text"
                            id="campaignName"
                            class="form-control"
                            v-model="form.name"
                            placeholder="Enter campaign name"
                            required
                        />
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

                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save me-1"></i>
                            {{ editMode ? 'Update' : 'Create' }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            v-if="editMode"
                            @click="resetForm"
                        >
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Existing Campaigns Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Existing Campaigns</h6>
                <button class="btn btn-sm btn-outline-primary" @click="fetchCampaigns">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 160px;">Actions</th>
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
                            <td class="text-center">
                                <button
                                    class="btn btn-sm btn-warning me-2"
                                    @click="editCampaign(campaign)"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-danger"
                                    @click="deleteCampaign(campaign.id)"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="campaigns.length === 0">
                            <td colspan="4" class="text-center text-muted">
                                No campaigns found.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
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

            const request = this.editMode
                ? axios.put(`/api/campaigns/${this.form.id}`, payload)
                : axios.post('/api/campaigns', payload);

            request.then(() => {
                this.fetchCampaigns();
                this.resetForm();
            });
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
