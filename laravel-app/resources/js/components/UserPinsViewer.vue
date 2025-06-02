<template>
    <div class="container mt-4">
        <div class="map-container">
            <!-- Pin Details Card -->
            <div v-if="userName" class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ userName }}</h5>
                    <p class="card-text">
                        <strong>Total Pins:</strong> {{ totalPins }}<br/>
                    </p>
                </div>
            </div>

            <!-- Map -->
            <div id="map" class="map mb-3"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
const userId = window.userId // Replace with dynamic ID if needed
const userName = ref('')
const pins = ref([])
const totalPins = ref(0)

const greenIcon = new window.L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [15, 25],       // smaller icon size
    iconAnchor: [7, 25],      // anchor at bottom middle
    popupAnchor: [1, -20],    // popup position
    shadowSize: [25, 25]      // shadow size
})

const redIcon = new window.L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [15, 25],       // smaller icon size
    iconAnchor: [7, 25],      // anchor at bottom middle
    popupAnchor: [1, -20],    // popup position
    shadowSize: [25, 25]      // shadow size
})

// Fetch pins and user info from API
async function fetchPins() {
    try {
        const response = await axios.get(`/api/user/${userId}/pins`)
        pins.value = response.data.pins
        userName.value = response.data.user_name
        totalPins.value = response.data.total_pins

        // Initialize map after data loaded
        initMap()
    } catch (error) {
        console.error('Error fetching pins:', error)
    }
}

function initMap() {
    const map = window.L.map('map')
    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20
    }).addTo(map)

    if (pins.value.length > 0) {
        // Center map on first pin
        const firstPin = pins.value[0]
        map.setView([firstPin.latitude, firstPin.longitude], 15)

        // Add markers for all pins
        pins.value.forEach(pin => {
            const icon = pin.is_accepted === 1 ? greenIcon : redIcon
            window.L.marker([pin.latitude, pin.longitude], {icon})
                .addTo(map)
                .bindPopup(pin.notes || 'No notes provided')
        })
    } else {
        // Default view if no pins
        map.setView([-41.2865, 174.7762], 13) // Auckland coords as fallback
    }
}

onMounted(() => {
    fetchPins()
})
</script>

<style scoped>
.map-container {
    display: flex;
    flex-direction: column;
}

.map {
    height: 60vh;
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}

.card {
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.card-text {
    font-size: 1rem;
    line-height: 1.5;
}
</style>
