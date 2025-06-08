<template>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pins Over Last 7 Days by Area</h6>
        </div>
        <div class="card-body">
            <canvas ref="pinsChart"></canvas>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

// Colors for each area, SB Admin 2 theme inspired
const areaColors = [
    'rgba(78, 115, 223, 1)',   // Area 1 - blue
    'rgba(28, 200, 138, 1)',   // Area 2 - green
    'rgba(54, 185, 204, 1)',   // Area 3 - teal
    'rgba(246, 194, 62, 1)',   // Area 4 - yellow
    'rgba(231, 74, 59, 1)',    // Area 5 - red
    'rgba(133, 135, 150, 1)'   // Area 6 - gray
];

export default {
    name: 'PinsByAreaChart',
    props: {
        // Expecting data like:
        // {
        //   labels: ['2025-06-01', '2025-06-02', ..., '2025-06-07'],
        //   areas: {
        //     1: [5, 3, 6, 7, 2, 4, 3],
        //     2: [2, 1, 4, 6, 3, 5, 2],
        //     ...
        //     6: [1, 2, 3, 1, 4, 2, 3]
        //   }
        // }
        pinsByAreaData: {
            type: Object,
            required: true
        }
    },
    mounted() {
        this.renderChart();
    },
    methods: {
        renderChart() {
            const ctx = this.$refs.pinsChart.getContext('2d');
            const labels = this.pinsByAreaData.labels;
            const areas = this.pinsByAreaData.areas;

            // Prepare datasets for each area
            const datasets = Object.keys(areas).map((areaId, index) => ({
                label: `Area ${areaId}`,
                data: areas[areaId],
                fill: true,
                backgroundColor: areaColors[index].replace('1)', '0.1)'), // translucent fill
                borderColor: areaColors[index],
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: areaColors[index],
                pointBorderColor: areaColors[index],
                borderWidth: 2,
            }));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(234, 236, 244, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#4e73df',
                                font: { weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>

<style scoped>
.card-body {
    height: 350px;
}
</style>
