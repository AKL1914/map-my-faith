
<template>
    <div>
      <div id="map" style="height: 500px;"></div>
      <button @click="pinMyLocation">Pin My Location1</button>
    </div>
  </template>

  <script setup>
  import { onMounted } from 'vue'
  import L from 'leaflet'
  import 'leaflet/dist/leaflet.css'
  import axios from 'axios'

  let map

  onMounted(async () => {
    map = L.map('map').setView([-36.8485, 174.7633], 13)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map)

    const { data } = await axios.get('/api/pins')
    data.forEach(pin => {
      L.marker([pin.latitude, pin.longitude]).addTo(map)
    })
  })

  function pinMyLocation() {
    console.log('Pinning my location...')
    navigator.geolocation.getCurrentPosition(async position => {
      const lat = position.coords.latitude
      const lng = position.coords.longitude
      var campaign = 1
      var notes = "This is a test pin"
      var isAcctepted = 0
      await axios.post('/api/pin', { latitude: lat, longitude: lng , campaign_id: campaign, notes: notes, is_accteted: isAcctepted })
      L.marker([lat, lng]).addTo(map)
    })
  }
  </script>
