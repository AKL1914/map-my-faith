<template>
    <div class="container mt-4">
        <div class="map-container">
            <div id="map" class="map mb-3"></div>

            <div class="button-group d-flex gap-3 mb-4">
                <button
                    :disabled="loading"
                    @click="pinMyLocation('accepted')"
                    class="btn google-btn-pin">
                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-else><i class="bi bi-geo-alt-fill"></i> Pin Location</span>
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
import { ref, onMounted } from 'vue';
import { useToast, POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

let map;
const notes = ref('');
let currentPinMarker = null;
let lastSubmitTime = 0;
let pinLayerGroup = null;
let gamePinLayerGroup = null;
const loading = ref(false);

const currentUserId = window.authUser?.id || null;

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

const blueIcon = new L.Icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const orangeIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});


const treasureChestIcon = new L.Icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854866.png', // treasure chest image
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -28],
});

const toast = useToast();

async function fetchPinsWithinBounds() {
    if (!map) return;

    const bounds = map.getBounds();
    const {data} = await axios.get('/api/pins/bounds', {
        params: {
            north: bounds.getNorth(),
            south: bounds.getSouth(),
            east: bounds.getEast(),
            west: bounds.getWest(),
        }
    });

    if (pinLayerGroup) pinLayerGroup.clearLayers();
    pinLayerGroup = window.L.layerGroup().addTo(map);

    data.forEach(pin => {
        const icon = pin.user_id === currentUserId ? orangeIcon : (pin.is_accepted === 1 ? greenIcon : redIcon);
        const popupContent = `
            <strong>${pin.user?.name ?? 'Unknown User'}</strong><br/>
            Status: ${pin.is_accepted === 1 ? 'Accepted ✅' : 'Refused ❌'}<br/>
            ${pin.notes ?? ''}
            ${pin.user_id === currentUserId
            ? `<button class="btn btn-sm btn-outline-danger mt-2 delete-btn" data-id="${pin.id}">🗑</button>`
            : ''}
        `;
        const marker = L.marker([pin.latitude, pin.longitude], {icon})
            .addTo(pinLayerGroup)
            .bindPopup(popupContent);

        marker.on('popupopen', () => {
            const deleteButton = document.querySelector(`.delete-btn[data-id="${pin.id}"]`);
            if (deleteButton) {
                deleteButton.addEventListener('click', () => {
                    deletePin(pin.id);
                });
            }
        });
    });
}

async function fetchGamePins() {
    if (!map) return;

    const {data} = await axios.get('/api/game-pins');

    if (gamePinLayerGroup) gamePinLayerGroup.clearLayers();
    gamePinLayerGroup = window.L.layerGroup().addTo(map);

    data.forEach(game => {
        const popupContent = `
            <strong>🎮 Game Pin</strong><br/>
            ${game.name ?? ''}<br/>
            <button class="btn btn-sm btn-success mt-2 participate-btn" data-id="${game.id}">
                Participate
            </button>
        `;

        const marker = L.marker([game.latitude, game.longitude], {icon: treasureChestIcon})
            .addTo(gamePinLayerGroup)
            .bindPopup(popupContent);

        marker.on('popupopen', () => {
            const participateBtn = document.querySelector(`.participate-btn[data-id="${game.id}"]`);
            if (participateBtn) {
                participateBtn.addEventListener('click', async () => {
                    if (!currentPinMarker) {
                        toast.error("Please select a location on the map first.", {
                            position: POSITION.TOP_CENTER,
                            timeout: 4000
                        });
                        return;
                    }

                    const { lat, lng } = currentPinMarker.getLatLng();

                    try {
                        await axios.post(`/api/game-pins/${game.id}/participate`, {
                            latitude: lat,
                            longitude: lng,
                        });

                        toast.success("Participation successful!", {
                            position: POSITION.TOP_CENTER,
                            timeout: 4000
                        });
                    } catch (error) {
                        toast.error(error.response?.data?.message || "Failed to participate.", {
                            position: POSITION.TOP_CENTER,
                            timeout: 4000
                        });
                    }
                });
            }
        });
    });
}

onMounted(() => {
    map = window.L.map('map');
    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        minZoom: 10
    }).addTo(map);

    pinLayerGroup = window.L.layerGroup().addTo(map);
    gamePinLayerGroup = window.L.layerGroup().addTo(map);

    navigator.geolocation.getCurrentPosition(async position => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        map.setView([lat, lng], 18);

        currentPinMarker = L.marker([lat, lng], {icon: blueIcon})
            .addTo(map)
            .bindPopup('You are here');

        await fetchPinsWithinBounds();
        await fetchGamePins();
    });

    map.on('moveend', () => {
        fetchPinsWithinBounds();
        fetchGamePins();
    });

    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (currentPinMarker) {
            currentPinMarker.setLatLng([lat, lng]);
        } else {
            currentPinMarker = L.marker([lat, lng], {icon: blueIcon}).addTo(map);
        }

        currentPinMarker.bindPopup('New location').openPopup();
    });
});

async function pinMyLocation(status) {
    const now = Date.now();
    if (!currentPinMarker || now - lastSubmitTime < 4000 || loading.value) return;

    loading.value = true;
    const lat = currentPinMarker.getLatLng().lat;
    const lng = currentPinMarker.getLatLng().lng;
    const isAccepted = status === 'accepted' ? 1 : 0;
    let activeCampaign = window.campaign?.id;

    try {
        await axios.post('/api/pin', {
            latitude: lat,
            longitude: lng,
            notes: notes.value || '',
            is_accepted: isAccepted,
            campaign_id: activeCampaign
        });

        lastSubmitTime = now;
        notes.value = '';
        currentPinMarker.setLatLng([lat, lng]);
        await fetchPinsWithinBounds();

        toast.success('Location pinned successfully!', {
            position: POSITION.TOP_CENTER,
            timeout: 5000
        });
    } catch (error) {
        toast.error(error.response.data.message, {
            position: POSITION.TOP_CENTER,
            timeout: 5000
        });

        notes.value = '';
    } finally {
        setTimeout(() => {
            loading.value = false;
        }, 5000);
    }
}

async function deletePin(pinId) {
    if (confirm('Are you sure you want to delete this pin?')) {
        await axios.delete(`/api/pin/${pinId}`);
        await fetchPinsWithinBounds();
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
    gap: 16px;
    justify-content: space-between;
}

.btn {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    flex: 1;
}

.google-btn-pin {
    background-color: #4285f4;
    color: white;
    border: none;
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
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.google-btn:hover {
    background-color: #357ae8;
}

.google-btn i {
    font-size: 1.5rem;
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

.participate-btn {
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 6px;
}
</style>
