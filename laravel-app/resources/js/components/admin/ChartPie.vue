<template>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ title }}</h6>
        </div>
        <div class="card-body">
            <div class="chart-pie pt-4 pb-2">
                <canvas ref="canvas" width="100%" height="100"></canvas>
            </div>
            <div class="mt-4 text-center small">
        <span v-for="(label, index) in labels" :key="index" class="mr-2">
          <i class="fas fa-circle" :style="{ color: backgroundColors[index] }"></i> {{ label }}
        </span>
            </div>
        </div>
    </div>
</template>

<script>
import {
    Chart,
    DoughnutController,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js';

Chart.register(DoughnutController, ArcElement, Tooltip, Legend);

export default {
    name: 'ChartPie',
    props: {
        title: {
            type: String,
            default: 'Pie Chart',
        },
        labels: {
            type: Array,
            default: () => ['Red', 'Blue', 'Yellow'],
        },
        data: {
            type: Array,
            default: () => [30, 40, 30],
        },
        backgroundColors: {
            type: Array,
            default: () => ['#e74a3b', '#36b9cc', '#f6c23e'],
        },
    },
    mounted() {
        const ctx = this.$refs.canvas.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: this.labels,
                datasets: [{
                    data: this.data,
                    backgroundColor: this.backgroundColors,
                    hoverBackgroundColor: this.backgroundColors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                    },
                    legend: {
                        display: false,
                    }
                },
                cutout: '80%',
            }
        });
    }
};
</script>

<style scoped>
.chart-pie {
    position: relative;
    height: 300px;
}
</style>
