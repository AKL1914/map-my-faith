<template>
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

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text fs-4">{{ totalUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Pins</h5>
                        <p class="card-text fs-4">{{ pins.length }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card text-white bg-info h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Suburbs</h5>
                        <p class="card-text fs-4">{{ totalSuburbs }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="mb-4 border rounded shadow" id="map"></div>
    </div>
</template>

<script>
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
    name: 'AdminDashboard',
    data() {
        return {
            campaigns: [],
            pins: [],
            selectedCampaignId: '',
            map: null,
            markers: [],
            totalUsers: 0,
            totalSuburbs: 0,
        };
    },
    mounted() {
        this.initMap();
        this.fetchCampaigns();
    },
    methods: {
        initMap() {
            this.map = window.L.map('map').setView([-36.8485, 174.7633], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
        },
        fetchCampaigns() {
            axios.get('/api/campaigns')
                .then(response => {
                    this.campaigns = response.data;
                    const activeCampaign = this.campaigns.find(campaign => campaign.is_active);
                    if (activeCampaign) {
                        this.selectedCampaignId = activeCampaign.id;
                        this.fetchPins();
                    }
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

                    const userIds = new Set();
                    const suburbs = new Set();

                    this.pins.forEach(pin => {
                        if (pin.user_id) userIds.add(pin.user_id);
                        if (pin.suburb) suburbs.add(pin.suburb.toLowerCase());
                    });

                    this.totalUsers = userIds.size;
                    this.totalSuburbs = suburbs.size;
                });
        },
        updateMapMarkers() {
            this.markers.forEach(marker => this.map.removeLayer(marker));
            this.markers = [];

            this.pins.forEach(pin => {
                if (pin.latitude && pin.longitude) {
                    const icon = pin.is_accepted === 1 ? greenIcon : redIcon;
                    const popupContent = `
                        <strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>
                        ${pin.notes ?? ''}
                    `;

                    const marker = window.L.marker([pin.latitude, pin.longitude], {icon})
                        .addTo(this.map)
                        .bindPopup(popupContent);

                    this.markers.push(marker);
                }
            });

            if (this.markers.length) {
                const group = new window.L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds(), {padding: [30, 30]});
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

@media (max-width: 576px) {
    .card-text {
        font-size: 1.2rem;
    }
}
</style>
