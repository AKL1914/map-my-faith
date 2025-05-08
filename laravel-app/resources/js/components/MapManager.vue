<template>
    <div class="container mt-4">
        <div class="map-container">
            <div id="map" class="map mb-3"></div>

            <div class="button-group d-flex flex-column gap-3 mb-4">
                <button @click="pinMyLocation('accepted')" class="btn btn-success">
                    ✅ Accepted
                </button>
                <button @click="confirmRefuse" class="btn btn-danger">
                    ❌ Refused
                </button>
            </div>

            <textarea
                v-model="notes"
                placeholder="Optional notes..."
                rows="3"
                class="form-control notes-textarea"
            ></textarea>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import axios from 'axios'

let map
const notes = ref('')
let currentPinMarker = null // Variable to store the current pin marker

// Marker icons
const redIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})

const greenIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})

const blueIcon = new L.Icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})

onMounted(async () => {
    map = L.map('map')
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20
    }).addTo(map)

    navigator.geolocation.getCurrentPosition(async position => {
        const lat = position.coords.latitude
        const lng = position.coords.longitude
        map.setView([lat, lng], 12) // zoomed out a bit

        // Your location marker
        currentPinMarker = L.marker([lat, lng], {icon: blueIcon})
            .addTo(map)
            .bindPopup('You are here')

        // Load all pins
        const { data } = await axios.get('/api/pins')
        data.forEach(pin => {
            const icon = pin.is_accepted === 1 ? greenIcon : redIcon
            const popupContent = `
        <strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>
        ${pin.notes ?? ''}
    `
            L.marker([pin.latitude, pin.longitude], { icon })
                .addTo(map)
                .bindPopup(popupContent)
        })
    })

    // Allow the user to click on the map to update the current pin
    map.on('click', function (e) {
        const lat = e.latlng.lat
        const lng = e.latlng.lng

        // If there's already a marker, move it to the new location
        if (currentPinMarker) {
            currentPinMarker.setLatLng([lat, lng])
        } else {
            // Otherwise, create a new marker at the clicked location
            currentPinMarker = L.marker([lat, lng], {icon: blueIcon}).addTo(map)
        }

        currentPinMarker.bindPopup('New location').openPopup()
    })
})

async function pinMyLocation(status) {
    if (!currentPinMarker) return // Prevent submission if no marker is set

    const lat = currentPinMarker.getLatLng().lat
    const lng = currentPinMarker.getLatLng().lng
    const campaign = 1
    const isAccepted = status === 'accepted' ? 1 : 0

    await axios.post('/api/pin', {
        latitude: lat,
        longitude: lng,
        campaign_id: campaign,
        notes: notes.value || '',
        is_accepted: isAccepted
    })

    // Show the new marker with appropriate color
    const icon = isAccepted === 1 ? greenIcon : redIcon
    L.marker([lat, lng], {icon})
        .addTo(map)
        .bindPopup(status === 'accepted' ? 'Accepted Pin' : 'Refused Pin')

    // Reset the form and marker position
    notes.value = ''
    currentPinMarker.setLatLng([lat, lng]) // Update the current pin's position
}

function confirmRefuse() {
    if (confirm('Are you sure you want to refuse?')) {
        pinMyLocation('refused')
    }
}
</script>

<style scoped>
.map-container {
    display: flex;
    flex-direction: column;
}

.map {
    height: 70vh;
    width: 100%;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}

.button-group {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.btn {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    width: 100%;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.notes-textarea {
    width: 100%;
    font-size: 16px;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    resize: vertical;
}
</style>
