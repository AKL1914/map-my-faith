<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <button
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                @click="downloadReport"
                title="Download Report"
            >
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </button>
        </div>

        <!-- Campaign Filters Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
                <button class="btn btn-sm btn-outline-primary" @click="fetchPins">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Campaign Selector -->
                    <div class="dropdown mb-3 col-md-4">
                        <button
                            class="btn btn-outline-primary dropdown-toggle w-100 text-start text-truncate"
                            type="button"
                            id="campaignDropdown"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            {{ selectedCampaignName }}
                        </button>
                        <div class="dropdown-menu animated--fade-in w-100" aria-labelledby="campaignDropdown">
                            <a class="dropdown-item" href="#" @click.prevent="selectedCampaignId = ''; fetchPins()">
                                All Campaigns
                            </a>
                            <a
                                v-for="campaign in campaigns"
                                :key="campaign.id"
                                class="dropdown-item"
                                href="#"
                                @click.prevent="selectedCampaignId = campaign.id; fetchPins()"
                            >
                                {{ campaign.name }}
                            </a>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-2 mb-3">
                        <input type="date" id="dateFrom" class="form-control" v-model="dateFrom" />
                    </div>

                    <!-- Date To -->
                    <div class="col-md-2 mb-3">
                        <input type="date" id="dateTo" class="form-control" v-model="dateTo" />
                    </div>

                    <!-- Limit Selector -->
                    <div class="col-md-2 mb-3">
                        <div class="dropdown">
                            <button
                                class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                                type="button"
                                id="limitDropdown"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                {{ selectedLimitDisplay }}
                            </button>
                            <div class="dropdown-menu animated--fade-in w-100" aria-labelledby="limitDropdown">
                                <a href="#" class="dropdown-item" @click.prevent="selectedLimit = ''; fetchPins()">All</a>
                                <a
                                    v-for="n in [200, 400, 600, 800, 1000]"
                                    :key="n"
                                    href="#"
                                    class="dropdown-item"
                                    @click.prevent="selectedLimit = n; fetchPins()"
                                >
                                    {{ n }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Area Selector -->
                    <div class="col-md-2 mb-3">
                        <div class="dropdown">
                            <button
                                class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                                type="button"
                                id="areaDropdown"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                {{ selectedAreaDisplay }}
                            </button>
                            <div class="dropdown-menu animated--fade-in w-100" aria-labelledby="areaDropdown">
                                <a href="#" class="dropdown-item" @click.prevent="selectedArea = ''; fetchPins()">All Areas</a>
                                <a
                                    v-for="n in 6"
                                    :key="n"
                                    href="#"
                                    class="dropdown-item"
                                    @click.prevent="selectedArea = n; fetchPins()"
                                >
                                    Area {{ n }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4" v-for="stat in stats" :key="stat.label">
                <div class="card shadow h-100 py-2" :class="stat.borderClass">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" :class="stat.textClass">
                                    {{ stat.label }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ stat.value }}</div>
                            </div>
                            <div class="col-auto">
                                <i :class="stat.iconClass"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Map</h6>
            </div>
            <div class="card-body">
                <div class="mb-4 border rounded shadow" id="map"></div>
            </div>
        </div>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification';

const toast = useToast();

const redIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [15, 25],
    iconAnchor: [7, 25],
    popupAnchor: [1, -20],
    shadowSize: [25, 25]
});

const greenIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [15, 25],
    iconAnchor: [7, 25],
    popupAnchor: [1, -20],
    shadowSize: [25, 25]
});

export default {
    name: 'AdminDashboard',
    data() {
        return {
            campaigns: [],
            pins: [],
            selectedCampaignId: '',
            dateFrom: '',
            dateTo: '',
            selectedArea: '',
            selectedLimit: 200,
            map: null,
            markers: [],
            totalUsers: 0,
            totalPins: 0,
            totalSuburbs: 0,
            loggedInUsers: 0,
        };
    },
    computed: {
        selectedCampaignName() {
            return this.campaigns.find(c => c.id === this.selectedCampaignId)?.name || 'Select Campaign';
        },
        selectedLimitDisplay() {
            return this.selectedLimit || 'Select Limit';
        },
        selectedAreaDisplay() {
            return this.selectedArea ? `Area ${this.selectedArea}` : 'All Areas';
        },
        stats() {
            return [
                {
                    label: 'Total Users',
                    value: this.totalUsers,
                    iconClass: 'fas fa-users fa-2x text-gray-300',
                    borderClass: 'border-left-primary',
                    textClass: 'text-primary'
                },
                {
                    label: 'Total Pins',
                    value: this.totalPins,
                    iconClass: 'fas fa-map-pin fa-2x text-gray-300',
                    borderClass: 'border-left-success',
                    textClass: 'text-success'
                },
                {
                    label: 'Suburbs',
                    value: this.totalSuburbs,
                    iconClass: 'fas fa-city fa-2x text-gray-300',
                    borderClass: 'border-left-info',
                    textClass: 'text-info'
                },
                {
                    label: 'Logged In Users',
                    value: this.loggedInUsers,
                    iconClass: 'fas fa-user-check fa-2x text-gray-300',
                    borderClass: 'border-left-warning',
                    textClass: 'text-warning'
                }
            ];
        }
    },
    mounted() {
        this.initMap();
        this.fetchCampaigns();
    },
    watch: {
        dateFrom() { this.fetchPins(); },
        dateTo() { this.fetchPins(); },
        selectedCampaignId() { this.fetchPins(); }
    },
    methods: {
        initMap() {
            this.map = window.L.map('map').setView([-36.8485, 174.7633], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
                minZoom: 10
            }).addTo(this.map);
        },
        fetchCampaigns() {
            axios.get('/api/campaigns').then(response => {
                this.campaigns = response.data;
                const active = this.campaigns.find(c => c.is_active);
                if (active) this.selectedCampaignId = active.id;
            });
        },
        fetchPins() {
            const params = {
                date_from: this.dateFrom,
                date_to: this.dateTo,
                area: this.selectedArea,
                limit: this.selectedLimit || ''
            };
            const url = this.selectedCampaignId ? `/api/pins/campaign/${this.selectedCampaignId}` : '/api/pins';
            axios.get(url, { params }).then(response => {
                this.pins = response.data.data || [];
                this.totalPins = response.data.total_pins || this.pins.length;
                this.totalUsers = response.data.total_users || 0;
                this.totalSuburbs = new Set(this.pins.map(p => p.suburb?.toLowerCase()).filter(Boolean)).size;
                this.updateMapMarkers();
                this.fetchLoggedInUsers();
            }).catch(() => {
                this.pins = [];
                this.totalPins = 0;
                this.totalUsers = 0;
                this.totalSuburbs = 0;
            });
        },
        fetchLoggedInUsers() {
            axios.get('/admin/active-users-count').then(r => {
                this.loggedInUsers = r.data.count;
            }).catch(() => this.loggedInUsers = 0);
        },
        updateMapMarkers() {
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];
            this.pins.forEach(pin => {
                if (pin.latitude && pin.longitude) {
                    const icon = pin.is_accepted === 1 ? greenIcon : redIcon;
                    const popup = `<strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>${pin.notes ?? ''}`;
                    const marker = L.marker([pin.latitude, pin.longitude], { icon })
                        .addTo(this.map)
                        .bindPopup(popup);
                    this.markers.push(marker);
                }
            });
            if (this.markers.length) {
                const group = new L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds(), { padding: [30, 30] });
            }
        },
        downloadReport() {
            const params = {
                campaign_id: this.selectedCampaignId || '',
                date_from: this.dateFrom || '',
                date_to: this.dateTo || '',
                area: this.selectedArea || '',
                limit: this.selectedLimit || '',
            };
            axios.get('/admin/dashboard/generate-report', { params }).then(r => {
                toast.success(r.data.message || 'Report generated successfully.');
            }).catch(e => {
                toast.error(e.response?.data?.message || 'Failed to generate report.');
            });
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
