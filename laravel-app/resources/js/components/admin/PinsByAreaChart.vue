<template>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Pins by Area</h6>
            <select v-model="selectedDays" @change="onDaysChange" class="form-control form-control-sm w-auto">
                <option value="7">Last 7 Days</option>
                <option value="15">Last 15 Days</option>
                <option value="30">Last 30 Days</option>
            </select>
        </div>
        <div class="card-body">
            <canvas ref="pinsChart"></canvas>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

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
        pinsByAreaData: {
            type: Object,
            required: true
        },
        days: {
            type: Number,
            default: 7
        }
    },
    data() {
        return {
            chart: null,
            selectedDays: this.days
        };
    },
    watch: {
        pinsByAreaData: {
            handler() {
                this.$nextTick(() => {
                    if (this.chart) {
                        this.chart.destroy();
                        this.chart = null;
                    }
                    this.renderChart();
                });
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        onDaysChange() {
            this.$emit('update:days', parseInt(this.selectedDays));
        },
        renderChart() {
            const canvas = this.$refs.pinsChart;
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const labels = this.pinsByAreaData.labels || [];
            const areas = this.pinsByAreaData.areas || {};

            const datasets = Object.keys(areas).map((areaId, index) => ({
                label: `Area ${areaId}`,
                data: areas[areaId],
                fill: true,
                backgroundColor: areaColors[index % areaColors.length].replace('1)', '0.1)'),
                borderColor: areaColors[index % areaColors.length],
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: areaColors[index % areaColors.length],
                pointBorderColor: areaColors[index % areaColors.length],
                borderWidth: 2,
            }));

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                callback: function(value, index) {
                                    const rawLabel = labels[index];
                                    const date = new Date(rawLabel);
                                    if (isNaN(date)) return rawLabel; // fallback
                                    const options = { month: 'short', day: '2-digit', weekday: 'short' };
                                    const parts = date.toLocaleDateString('en-US', options).split(', ');
                                    return `${parts[1]} ${parts[0]}`; // e.g., "12 Jan Mon"
                                }
                            },
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
};
</script>

<style scoped>
.card-body {
    height: 350px;
}
</style>
