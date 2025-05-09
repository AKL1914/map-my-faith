<template>
    <div class="container mt-4">
        <div class="map-container">
            <div id="map" class="map mb-3"></div>

            <div class="button-group d-flex gap-3 mb-4">
                <button
                    :disabled="loading"
                    @click="pinMyLocation('accepted')"
                    class="btn btn-success">
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-else>✅ Accepted</span>
                </button>
                <button
                    :disabled="loading"
                    @click="confirmRefuse"
                    class="btn btn-danger">
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-else>❌ Refused</span>
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
import { ref, onMounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import axios from 'axios'

let map
const notes = ref('')
let currentPinMarker = null
let lastSubmitTime = 0
let pinLayerGroup = null
const loading = ref(false)

// Get current user ID
const currentUserId = window.authUser?.id || null

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

const orangeIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
})

async function fetchPinsWithinBounds() {
    if (!map) return

    const bounds = map.getBounds()
    const { data } = await axios.get('/api/pins/bounds', {
        params: {
            north: bounds.getNorth(),
            south: bounds.getSouth(),
            east: bounds.getEast(),
            west: bounds.getWest(),
        }
    })

    if (pinLayerGroup) pinLayerGroup.clearLayers()
    pinLayerGroup = L.layerGroup().addTo(map)

    data.forEach(pin => {
        // Use orange icon for current user's pins, otherwise green/red based on status
        const icon = pin.user_id === currentUserId ? orangeIcon : (pin.is_accepted === 1 ? greenIcon : redIcon)
        const popupContent = `
            <strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>
            Status: ${pin.is_accepted === 1 ? 'Accepted ✅' : 'Refused ❌'}<br/>
            ${pin.notes ?? ''}
            ${pin.user_id === currentUserId
            ? `<button class="btn btn-sm btn-outline-danger mt-2 delete-btn" data-id="${pin.id}">🗑</button>`
            : ''}
        `
        const marker = L.marker([pin.latitude, pin.longitude], { icon })
            .addTo(pinLayerGroup)
            .bindPopup(popupContent)

        // Attach event listener for the delete button after the popup is opened
        marker.on('popupopen', () => {
            const deleteButton = document.querySelector(`.delete-btn[data-id="${pin.id}"]`)
            if (deleteButton) {
                deleteButton.addEventListener('click', () => {
                    deletePin(pin.id)
                })
            }
        })
    })
}

onMounted(() => {
    map = L.map('map')
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20
    }).addTo(map)

    pinLayerGroup = L.layerGroup().addTo(map)

    navigator.geolocation.getCurrentPosition(async position => {
        const lat = position.coords.latitude
        const lng = position.coords.longitude
        map.setView([lat, lng], 12)

        currentPinMarker = L.marker([lat, lng], { icon: blueIcon })
            .addTo(map)
            .bindPopup('You are here')

        await fetchPinsWithinBounds()
    })

    map.on('moveend', fetchPinsWithinBounds)

    map.on('click', function (e) {
        const lat = e.latlng.lat
        const lng = e.latlng.lng

        if (currentPinMarker) {
            currentPinMarker.setLatLng([lat, lng])
        } else {
            currentPinMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(map)
        }

        currentPinMarker.bindPopup('New location').openPopup()
    })
})

async function pinMyLocation(status) {
    const now = Date.now()
    if (!currentPinMarker || now - lastSubmitTime < 4000 || loading.value) return // Prevent double submissions

    loading.value = true // Start loading
    const lat = currentPinMarker.getLatLng().lat
    const lng = currentPinMarker.getLatLng().lng
    const isAccepted = status === 'accepted' ? 1 : 0

    await axios.post('/api/pin', {
        latitude: lat,
        longitude: lng,
        notes: notes.value || '',
        is_accepted: isAccepted,
        campaign_id: 1
    })

    lastSubmitTime = now
    notes.value = ''
    currentPinMarker.setLatLng([lat, lng])
    await fetchPinsWithinBounds()

    setTimeout(() => {
        loading.value = false // End loading after 5 seconds
    }, 5000)
}

function confirmRefuse() {
    if (confirm('Are you sure you want to refuse?')) {
        pinMyLocation('refused')
    }
}

async function deletePin(pinId) {
    if (confirm('Are you sure you want to delete this pin?')) {
        await axios.delete(`/api/pin/${pinId}`)
        await fetchPinsWithinBounds()
    }
}
</script>

<style scoped>
.map-container {
    display: flex;
    flex-direction: column;
}

.map {
    height: 60vh;
    width: 100%;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
}

.button-group {
    display: flex;
    gap: 16px; /* Changed to a row layout */
    justify-content: space-between; /* Ensures space between buttons */
}

.btn {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    flex: 1; /* Makes the buttons the same width */
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

.delete-btn {
    margin-top: 8px;
    font-size: 14px;
}
</style>
