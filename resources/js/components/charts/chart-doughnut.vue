<template>
    <div style="height: 250px">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script>
  import { Doughnut } from "vue-chartjs"
  import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale } from "chart.js"
  import ChartDataLabels from "chartjs-plugin-datalabels";

  ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale)
  ChartJS.register(ChartDataLabels);
  
  export default {
    name: "ChartDoughnutComponent",
    components: { Doughnut },
    props: {
        data: {
            type: Object,
            required: false,
            default: () => [],
        },
        options: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    computed: {
        chartData() {
            return { ...this.data };
        },
        chartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        onClick: function(e, legendItem, legend) {
                            const chart = legend.chart;
                            const index = legendItem.index;
                            const meta = chart.getDatasetMeta(0); // Assuming you have only one dataset

                            // Toggle the visibility of the dataset item
                            meta.data[index].hidden = !meta.data[index].hidden;

                            // Update the chart to reflect changes
                            chart.update();
                        },
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, index) => {
                                    const meta = chart.getDatasetMeta(0); // Assuming you have only one dataset

                                    return {
                                        text: `${label} (${data.datasets[0].data[index]})`,
                                        fillStyle: data.datasets[0].backgroundColor[index],
                                        hidden: meta.data[index] && meta.data[index].hidden,
                                        index: index // This should be 'index', not 'datasetIndex'
                                    };
                                });
                            },
                        },
                    },
                    datalabels: {
                        color: "#fff",
                        textAlign: "center",
                        font: {
                            weight: "bold",
                            size: 14,
                        },
                        formatter: (value, context) => {
                            return value;
                        },
                        display: function(context) {
                            return !context.dataset.data[context.dataIndex].hidden;
                        },
                    }
                },
                ...this.options,
            };
        },
    },
  }
  </script>
  