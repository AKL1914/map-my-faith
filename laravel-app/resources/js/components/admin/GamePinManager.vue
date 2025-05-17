<template>
    <div class="container my-4">
        <div class="mb-4">
            <label for="eventSelect" class="form-label">Filter by Event:</label>
            <select id="eventSelect" class="form-select" v-model="selectedEventId" @change="fetchPins">
                <option v-for="event in events" :key="event.id" :value="event.id">{{ event.name }}</option>
            </select>
            <div class="mt-2">
                <span class="badge bg-info">{{ pins.length }} Pin{{ pins.length !== 1 ? 's' : '' }} Found</span>
            </div>
        </div>
        <div class="mb-4 border rounded shadow" id="map"></div>
        <div class="button-group d-flex gap-3 mb-4">
            <button :disabled="loading" @click="pinMyLocation('accepted')" class="btn google-btn-pin">
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span v-else><i class="bi bi-geo-alt-fill"></i> Pin Location</span>
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { POSITION, useToast } from 'vue-toastification';

const treasureChestIcon = new L.Icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854866.png',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -28],
});

const blueIcon = new L.Icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

export default {
    name: 'GamePinManager',
    data() {
        return {
            events: [],
            pins: [],
            selectedEventId: '',
            map: null,
            markers: [],
            currentPinMarker: null,
            loading: false,
            lastSubmitTime: 0,
        };
    },
    mounted() {
        this.initMap();
        this.fetchEvents();
        navigator.geolocation.getCurrentPosition(async (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            this.map.setView([lat, lng], 18);
            this.currentPinMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(this.map).bindPopup('You are here');
            await this.fetchPins();
        });
        this.map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            if (this.currentPinMarker) {
                this.currentPinMarker.setLatLng([lat, lng]);
            } else {
                this.currentPinMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(this.map);
            }
            this.currentPinMarker.bindPopup('New location').openPopup();
        });
    },
    methods: {
        initMap() {
            this.map = L.map('map').setView([-36.8485, 174.7633], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
        },
        fetchEvents() {
            axios.get('/api/events')
                .then(res => {
                    this.events = res.data;
                    const active = this.events.find(e => e.is_active);
                    const fallback = this.events[0];

                    if (active || fallback) {
                        this.selectedEventId = active ? active.id : fallback.id;
                        this.fetchPins();
                    }
                })
                .catch(err => console.error('Error fetching events:', err));
        },
        fetchPins() {
            if (!this.selectedEventId) {
                this.pins = [];
                this.updateMapMarkers();
                return;
            }
            axios.get(`/api/events/${this.selectedEventId}/pins`)
                .then(res => {
                    this.pins = res.data.data;
                    this.updateMapMarkers();
                })
                .catch(err => console.error('Error fetching pins:', err));
        },
        updateMapMarkers() {
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];
            this.pins.forEach(pin => {
                if (pin.latitude && pin.longitude) {
                    const icon = treasureChestIcon;
                    const popup = `
                        <strong>${pin.user?.name ?? 'Unknown User'}</strong>
                        <button class="btn btn-sm btn-outline-danger mt-2 delete-btn" data-id="${pin.id}">🗑</button>
                    `;
                    const marker = L.marker([pin.latitude, pin.longitude], { icon })
                        .addTo(this.map)
                        .bindPopup(popup);
                    marker.on('popupopen', () => {
                        const btn = document.querySelector(`.delete-btn[data-id="${pin.id}"]`);
                        if (btn) {
                            btn.addEventListener('click', () => this.deletePin(pin.id));
                        }
                    });
                    this.markers.push(marker);
                }
            });
            if (this.markers.length) {
                const group = new L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds(), { padding: [30, 30] });
            }
        },
        async deletePin(id) {
            const toast = useToast();
            if (!confirm('Are you sure you want to delete this pin?')) return;
            try {
                await axios.delete(`/api/game-pins/${id}`);
                toast.success('Pin deleted successfully!', {position: POSITION.TOP_CENTER});
                await this.fetchPins();
            } catch (err) {
                toast.error(err.response.data.message, {position: POSITION.TOP_CENTER});
            }
        },
        async pinMyLocation() {
            const toast = useToast();
            const now = Date.now();
            if (!this.currentPinMarker || now - this.lastSubmitTime < 4000 || this.loading) return;
            this.loading = true;
            const {lat, lng} = this.currentPinMarker.getLatLng();
            try {
                await axios.post('/api/game-pins', {
                    latitude: lat,
                    longitude: lng,
                    event_id: this.selectedEventId,
                });
                this.lastSubmitTime = now;
                toast.success('Location pinned successfully!', {position: POSITION.TOP_CENTER, timeout: 5000});
                await this.fetchPins();
            } catch (err) {
                toast.error(err.response?.data?.message || 'Error pinning location.', {
                    position: POSITION.TOP_CENTER,
                    timeout: 5000
                });
            } finally {
                setTimeout(() => {
                    this.loading = false;
                }, 500);
            }
        }
    }
};
</script>

<style scoped>
#map {
    width: 100%;
    height: 500px;
}

.button-group {
    display: flex;
    gap: 16px;
}

.google-btn-pin {
    background-color: #4285f4;
    color: white;
    font-weight: 500;
    padding: 1rem 1.5rem;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: background-color 0.3s ease;
}

.google-btn-pin:hover {
    background-color: #357ae8;
}
</style>
