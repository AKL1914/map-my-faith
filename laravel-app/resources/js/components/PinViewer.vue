<template>
    <div class="container mt-4">
        <div class="map-container">
            <!-- Pin Details Card -->
            <div v-if="pin" class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ pin.user?.name ?? 'Unknown User' }}</h5>
                    <p class="card-text">
                        <strong>Status:</strong> {{ pin.is_accepted === 1 ? 'Accepted ✅' : 'Refused ❌' }}<br/>
                        <strong>Suburb:</strong> {{ pin.suburb ?? 'Not specified' }}<br/>
                        <strong>Notes:</strong> {{ pin.notes ? pin.notes : 'No notes provided' }}<br/>
                        <strong>Created:</strong> {{ formatDate(pin.created_at) }}
                    </p>
                </div>
            </div>
            <!-- Map -->
            <div id="map" class="map mb-3"></div>
        </div>
    </div>
</template>

<script setup>
import {onMounted} from 'vue'

// Get the pin object from the Blade-injected global JS variable
const pin = window.pin

// Icons
const greenIcon = new window.L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})

const redIcon = new window.L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})
// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return 'Not specified'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-NZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

onMounted(() => {
    const map = window.L.map('map')
    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20
    }).addTo(map)

    if (pin) {
        const lat = pin.latitude
        const lng = pin.longitude
        const isAccepted = pin.is_accepted === 1
        const icon = isAccepted ? greenIcon : redIcon

        map.setView([lat, lng], 15)

        window.L.marker([lat, lng], {icon})
            .addTo(map)
    }
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
