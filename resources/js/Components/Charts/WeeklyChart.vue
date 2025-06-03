<template>
    <div class="weekly-chart" style="height: 300px;">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </template>

  <script>
  import { defineComponent, onMounted, onUnmounted, watch, ref } from 'vue';
  import Chart from 'chart.js/auto';

  export default defineComponent({
    props: {
      data: {
        type: Object,
        required: true,
        validator: (value) => {
          return value.hasOwnProperty('labels') && value.hasOwnProperty('values');
        }
      }
    },

    setup(props) {
      const chartCanvas = ref(null);
      let chartInstance = null;

      const createChart = () => {
        if (!chartCanvas.value) return;

        const ctx = chartCanvas.value.getContext('2d');

        // Détruire l'instance précédente si elle existe
        if (chartInstance) {
          chartInstance.destroy();
        }

        // Créer une nouvelle instance de graphique
        chartInstance = new Chart(ctx, {
          type: 'line',
          data: {
            labels: props.data.labels,
            datasets: [{
              label: 'Tâches terminées',
              data: props.data.values,
              backgroundColor: 'rgba(59, 130, 246, 0.5)',
              borderColor: 'rgb(59, 130, 246)',
              borderWidth: 2,
              tension: 0.3,
              pointRadius: 4,
              pointBackgroundColor: 'white',
              pointBorderColor: 'rgb(59, 130, 246)',
              pointBorderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  precision: 0
                }
              }
            },
            plugins: {
              legend: {
                position: 'top',
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                titleFont: {
                  size: 14
                },
                bodyFont: {
                  size: 14
                },
                padding: 10,
                displayColors: false
              }
            }
          }
        });
      };

      // Créer le graphique lors du montage du composant
      onMounted(() => {
        createChart();
      });

      // Nettoyer l'instance du graphique lors du démontage du composant
      onUnmounted(() => {
        if (chartInstance) {
          chartInstance.destroy();
        }
      });

      // Recréer le graphique lorsque les données changent
      watch(() => props.data, () => {
        createChart();
      }, { deep: true });

      return {
        chartCanvas
      };
    }
  });
  </script>
