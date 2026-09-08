<template>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ campaignName }}</h6>
            <button
                @click="refreshPage"
                class="btn btn-sm btn-secondary"
                title="Refresh Page">
                <i class="bi bi-arrow-clockwise"></i>Refresh
            </button>
        </div>
        <div class="card-body map-container">
            <div id="map" class="map mb-3"></div>

            <div class="button-group d-flex gap-3 mb-4">
                <button
                    :disabled="loading"
                    @click="pinMyLocation('accepted')"
                    class="btn btn-google google-btn-pin">
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

            <div class="contact-fields mt-3">
                <input
                    v-model="contactName"
                    type="text"
                    placeholder="Contact name (optional)"
                    maxlength="100"
                    class="form-control mb-2"
                />
                <input
                    v-model="contactPhone"
                    type="tel"
                    placeholder="Contact phone (optional)"
                    maxlength="20"
                    class="form-control mb-2"
                />
                <input
                    v-model="contactEmail"
                    type="email"
                    placeholder="Contact email (optional)"
                    maxlength="255"
                    class="form-control"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast, POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

let map;
const notes = ref('');
const contactName = ref('');
const contactPhone = ref('');
const contactEmail = ref('');
let currentPinMarker = null;
let lastSubmitTime = 0;
let pinLayerGroup = null;
let gamePinLayerGroup = null;
const loading = ref(false);
const campaignName = ref(window.campaign?.name || 'Map Manager');


const currentUserId = window.authUser?.id || null;

// Smaller icons for pins (from 25x41 to 15x25)
const smallIconSize = [15, 25];
const smallIconAnchor = [7, 25];
const smallPopupAnchor = [1, -20];
const smallShadowSize = [25, 25];

const redIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: smallIconSize,
    iconAnchor: smallIconAnchor,
    popupAnchor: smallPopupAnchor,
    shadowSize: smallShadowSize
});

const greenIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: smallIconSize,
    iconAnchor: smallIconAnchor,
    popupAnchor: smallPopupAnchor,
    shadowSize: smallShadowSize
});

const blueIcon = new L.Icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: smallIconSize,
    iconAnchor: smallIconAnchor,
    popupAnchor: smallPopupAnchor,
    shadowSize: smallShadowSize
});

const orangeIcon = new L.Icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    iconSize: smallIconSize,
    iconAnchor: smallIconAnchor,
    popupAnchor: smallPopupAnchor,
    shadowSize: smallShadowSize
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
    pinLayerGroup = window.L.markerClusterGroup({
        disableClusteringAtZoom: 18,
    });

    const markers = data.map(pin => {
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
            .bindPopup(popupContent);

        marker.on('popupopen', () => {
            const deleteButton = document.querySelector(`.delete-btn[data-id="${pin.id}"]`);
            if (deleteButton) {
                deleteButton.addEventListener('click', () => {
                    deletePin(pin.id);
                });
            }
        });

        return marker;
    });

    pinLayerGroup.addLayers(markers);
    pinLayerGroup.addTo(map);
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
                    if (!navigator.geolocation) {
                        toast.error("Geolocation is not supported by your browser.", {
                            position: POSITION.TOP_CENTER,
                            timeout: 4000
                        });
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(async (position) => {
                        const {latitude, longitude} = position.coords;

                        try {
                            await axios.post(`/api/game-pins/${game.id}/participate`, {
                                latitude,
                                longitude,
                            });

                            toast.success("Thank you for participating!", {
                                position: POSITION.TOP_CENTER,
                                timeout: 4000
                            });

                            // Close the popup immediately
                            marker.closePopup();

                            // Refetch game pins after 5 seconds
                            setTimeout(() => {
                                fetchGamePins();
                            }, 5000);

                        } catch (error) {
                            toast.error(error.response?.data?.message || "Failed to participate.", {
                                position: POSITION.TOP_CENTER,
                                timeout: 4000
                            });
                        }
                    }, (error) => {
                        toast.error("Unable to retrieve your location.", {
                            position: POSITION.TOP_CENTER,
                            timeout: 4000
                        });
                    });
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

    map.on('moveend', window.debounce(() => {
        fetchPinsWithinBounds();
        fetchGamePins();
    }, 300));

    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (currentPinMarker) {
            currentPinMarker.setLatLng([lat, lng]);
        } else {
            currentPinMarker = L.marker([lat, lng], {icon: blueIcon}).addTo(map);
        }
        //disable the popup for now
        // currentPinMarker.bindPopup('New').openPopup();
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
            campaign_id: activeCampaign,
            contact_name: contactName.value || null,
            contact_phone: contactPhone.value || null,
            contact_email: contactEmail.value || null,
        });

        lastSubmitTime = now;
        notes.value = '';
        contactName.value = '';
        contactPhone.value = '';
        contactEmail.value = '';
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

function refreshPage() {
    window.location.reload();
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

.btn-google {
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
}

.google-btn-pin i {
    margin-right: 0.75rem;
    font-size: 1.5rem;
}

.google-btn-pin:disabled {
    background-color: #99b3f9;
    cursor: not-allowed;
}

.notes-textarea {
    //font-size: 1.125rem;
    padding: 1rem;
    border-radius: 12px;
    border: 2px solid #eee;
    resize: vertical;
    width: 100%;
}

.map-container{
    padding: 0.25rem !important;
}


</style>
