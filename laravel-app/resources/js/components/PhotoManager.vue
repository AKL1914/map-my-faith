<template>
    <div class="container-fluid px-4">
        <h1 class="h3 mb-4 text-gray-800">Photos</h1>
        <!-- Upload Photo Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Upload Photo</h6>
            </div>
            <div class="card-body">
                <form @submit.prevent="uploadPhoto" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control" @change="onFileChange" required />
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="visible" v-model="form.visible" />
                        <label class="form-check-label" for="visible">Visible</label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                </form>
            </div>
        </div>
        <!-- Photos Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Uploaded Photos</h6>
                <button class="btn btn-sm btn-outline-primary" @click="fetchPhotos">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Visible</th>
                            <th class="text-center" style="width: 160px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="photo in photos" :key="photo.id">
                            <td>{{ photo.id }}</td>
                            <td>
                                <img :src="photo.url" alt="Photo" style="max-width: 100px; max-height: 80px;" />
                            </td>
                            <td>
                                <span class="badge text-white" :class="photo.visible ? 'bg-success' : 'bg-secondary'">
                                    {{ photo.visible ? 'Visible' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-secondary me-2" @click="toggleVisible(photo)">
                                    <i :class="photo.visible ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deletePhoto(photo.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="photos.length === 0">
                            <td colspan="4" class="text-center text-muted">No photos found.</td>
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
    name: 'PhotoManager',
    data() {
        return {
            photos: [],
            form: {
                file: null,
                visible: true
            }
        };
    },
    mounted() {
        this.fetchPhotos();
    },
    methods: {
        fetchPhotos() {
            axios.get('/api/photos').then(res => {
                this.photos = res.data;
            });
        },
        onFileChange(e) {
            this.form.file = e.target.files[0];
        },
        uploadPhoto() {
            if (!this.form.file) return;
            const formData = new FormData();
            formData.append('photo', this.form.file);
            formData.append('visible', this.form.visible ? 1 : 0);
            axios.post('/api/photos', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            }).then(() => {
                this.fetchPhotos();
                this.form.file = null;
                this.form.visible = true;
                this.$el.querySelector('input[type="file"]').value = '';
            });
        },
        toggleVisible(photo) {
            axios.post(`/api/photos/${photo.id}`, {
                visible: !photo.visible
            }).then(() => this.fetchPhotos());
        },
        deletePhoto(id) {
            if (confirm('Are you sure you want to delete this photo?')) {
                axios.delete(`/api/photos/${id}`).then(() => this.fetchPhotos());
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
</style>
