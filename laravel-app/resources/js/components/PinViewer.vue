<template>
    <div class="container mt-4">
        <div class="map-container">
            <div id="map" class="map mb-3"></div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'


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

const orangeIcon = new window.L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})

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

        const popupContent = `
            <strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>
            Status: ${isAccepted ? 'Accepted ✅' : 'Refused ❌'}<br/>
            ${pin.notes ? `<div>${pin.notes}</div>` : ''}
        `

        map.setView([lat, lng], 15)

        L.marker([lat, lng], { icon })
            .addTo(map)
            .bindPopup(popupContent)
            .openPopup()
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
</style>
