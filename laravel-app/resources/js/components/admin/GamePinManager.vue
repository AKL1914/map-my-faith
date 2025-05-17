<template>
    <div class="container my-4">
        <!-- Event Selector -->
        <div class="mb-4">
            <label for="eventSelect" class="form-label">Filter by Event:</label>
            <select
                id="eventSelect"
                class="form-select"
                v-model="selectedEventId"
                @change="fetchPins"
            >
                <option
                    v-for="event in events"
                    :key="event.id"
                    :value="event.id"
                >
                    {{ event.name }}
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
        <div class="mb-4 border rounded shadow" id="map"></div>
    </div>
</template>

<script>
const treasureChestIcon = new L.Icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854866.png',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -28],
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
            totalUsers: 0,
            totalSuburbs: 0,
            loggedInUsers: 0,
        };
    },
    mounted() {
        this.initMap();
        this.fetchEvents();
    },
    methods: {
        initMap() {
            this.map = window.L.map('map').setView([-36.8485, 174.7633], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
        },
        fetchEvents() {
            axios.get('/api/events')
                .then(response => {
                    this.events = response.data;
                    const activeEvent = this.events.find(event => event.is_active);
                    if (activeEvent) {
                        this.selectedEventId = activeEvent.id;
                        this.fetchPins();
                    }
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                });
        },
        fetchPins() {
            if (!this.selectedEventId) {
                this.pins = [];
                this.updateMapMarkers();
                return;
            }

            axios.get(`/api/events/${this.selectedEventId}/pins`)
                .then(response => {
                    this.pins = response.data.data;
                    this.updateMapMarkers();

                    const userIds = new Set();
                    const suburbs = new Set();

                    this.pins.forEach(pin => {
                        if (pin.user_id) userIds.add(pin.user_id);
                        if (pin.suburb) suburbs.add(pin.suburb.toLowerCase());
                    });

                    this.totalUsers = userIds.size;
                    this.totalSuburbs = suburbs.size;

                    this.fetchLoggedInUsers(); // Keep if you have this method
                })
                .catch(error => {
                    console.error('Error fetching pins:', error);
                });
        },
        updateMapMarkers() {
            this.markers.forEach(marker => this.map.removeLayer(marker));
            this.markers = [];

            this.pins.forEach(pin => {
                if (pin.latitude && pin.longitude) {
                    const icon = treasureChestIcon;
                    const popupContent = `
            <strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>
            ${pin.notes ?? ''}
          `;

                    const marker = window.L.marker([pin.latitude, pin.longitude], { icon })
                        .addTo(this.map)
                        .bindPopup(popupContent);

                    this.markers.push(marker);
                }
            });

            if (this.markers.length) {
                const group = new window.L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds(), { padding: [30, 30] });
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
