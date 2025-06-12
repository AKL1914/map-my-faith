<template>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ title }}</h6>
            <select
                v-model="selectedPeriod"
                @change="onPeriodChange"
                class="form-control form-control-sm"
                style="width: auto;"
            >
                <option value="">All</option>
                <option value="7">Last 7 days</option>
                <option value="15">Last 15 days</option>
                <option value="30">Last 30 days</option>
            </select>
        </div>
        <div class="card-body">
            <div class="chart-pie pt-4 pb-2">
                <canvas ref="canvas" width="100%" height="100"></canvas>
            </div>
            <div class="mt-4 text-center small">
        <span v-for="(label, index) in labels" :key="index" class="mr-3">
          <i class="fas fa-circle" :style="{ color: backgroundColors[index] }"></i>
          {{ label }} - {{ data[index] }}
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
    data() {
        return {
            selectedPeriod: '', // '' means all
            chartInstance: null,
        };
    },
    mounted() {
        this.renderChart();
    },
    watch: {
        data() {
            this.updateChart();
        },
        labels() {
            this.updateChart();
        }
    },
    methods: {
        renderChart() {
            const ctx = this.$refs.canvas.getContext('2d');
            this.chartInstance = new Chart(ctx, {
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
                            display: false, // we use custom legend below chart
                        }
                    },
                    cutout: '80%',
                }
            });
        },
        updateChart() {
            if (this.chartInstance) {
                this.chartInstance.data.labels = this.labels;
                this.chartInstance.data.datasets[0].data = this.data;
                this.chartInstance.update();
            }
        },
        onPeriodChange() {
            // Emit the selected period to the parent component
            this.$emit('period-change', this.selectedPeriod);
        }
    }
};
</script>

<style scoped>
.chart-pie {
    position: relative;
    height: 300px;
}
</style>
