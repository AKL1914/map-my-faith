<template>
    <div>
        <div class="container my-4">
            <!-- Campaign Selector -->
            <div class="mb-4">
                <label for="campaignSelect" class="form-label">Filter by Campaign:</label>
                <select
                    id="campaignSelect"
                    class="form-select"
                    v-model="selectedCampaignId"
                    @change="fetchPins"
                >
                    <option value="">All Campaigns</option>
                    <option
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        :value="campaign.id"
                    >
                        {{ campaign.name }}
                    </option>
                </select>

                <!-- Pin Count -->
                <div class="mt-2">
                    <span class="badge bg-info">
                        {{ pins.length }} Pin{{ pins.length !== 1 ? 's' : '' }} Found
                    </span>
                </div>
            </div>

            <!-- Map -->
            <div class="mb-4 border rounded shadow" style="height: 500px;" id="map"></div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Marker icons
const redIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const greenIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

export default {
    data() {
        return {
            campaigns: [],
            pins: [],
            selectedCampaignId: '',
            map: null,
            markers: [],
        };
    },
    mounted() {
        this.initMap();
        this.fetchCampaigns();
        this.fetchPins();
    },
    methods: {
        initMap() {
            this.map = L.map('map').setView([-36.8485, 174.7633], 12); // Auckland

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
        },
        fetchCampaigns() {
            axios.get('/api/campaigns')
                .then(response => {
                    this.campaigns = response.data;
                });
        },
        fetchPins() {
            const url = this.selectedCampaignId
                ? `/api/pins/campaign/${this.selectedCampaignId}`
                : '/api/pins';

            axios.get(url)
                .then(response => {
                    this.pins = response.data;
                    this.updateMapMarkers();
                });
        },
        updateMapMarkers() {
            // Clear existing markers
            this.markers.forEach(marker => this.map.removeLayer(marker));
            this.markers = [];

            this.pins.forEach(pin => {
                if (pin.latitude && pin.longitude) {
                    const icon = pin.is_accepted === 1 ? greenIcon : redIcon;
                    const popupContent = `
                        <strong>${pin.address || 'No address'}</strong><br>
                        ${pin.campaign?.name || ''}<br>
                        <span class="badge ${pin.is_accepted ? 'bg-success' : 'bg-danger'}">
                            ${pin.is_accepted ? 'Accepted' : 'Not Accepted'}
                        </span><br>
                        <small>${pin.notes || ''}</small>
                    `;

                    const marker = L.marker([pin.latitude, pin.longitude], {icon})
                        .addTo(this.map)
                        .bindPopup(popupContent);

                    this.markers.push(marker);
                }
            });

            if (this.markers.length) {
                const group = new L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds(), {padding: [30, 30]});
            }
        }
    }
};
</script>

<style scoped>
#map {
    width: 100%;
}
</style>
