<template>
    <div class="team-distribution-chart" style="height: 300px;">
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

      // Générer des couleurs différentes pour chaque équipe
      const generateColors = (count) => {
        const colors = [
          'rgba(54, 162, 235, 0.7)', // Bleu
          'rgba(255, 99, 132, 0.7)', // Rose
          'rgba(75, 192, 192, 0.7)', // Turquoise
          'rgba(255, 159, 64, 0.7)', // Orange
          'rgba(153, 102, 255, 0.7)', // Violet
          'rgba(255, 205, 86, 0.7)', // Jaune
          'rgba(201, 203, 207, 0.7)', // Gris
          'rgba(22, 160, 133, 0.7)', // Vert foncé
          'rgba(142, 68, 173, 0.7)', // Pourpre
          'rgba(211, 84, 0, 0.7)'    // Orange foncé
        ];

        // Si plus de couleurs sont nécessaires, en générer aléatoirement
        if (count > colors.length) {
          for (let i = colors.length; i < count; i++) {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            colors.push(`rgba(${r}, ${g}, ${b}, 0.7)`);
          }
        }

        return colors.slice(0, count);
      };

      const createChart = () => {
        if (!chartCanvas.value) return;

        const ctx = chartCanvas.value.getContext('2d');

        // Détruire l'instance précédente si elle existe
        if (chartInstance) {
          chartInstance.destroy();
        }

        const bgColors = generateColors(props.data.labels.length);
        const borderColors = bgColors.map(color => color.replace('0.7', '1'));

        // Créer une nouvelle instance de graphique
        chartInstance = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: props.data.labels,
            datasets: [{
              data: props.data.values,
              backgroundColor: bgColors,
              borderColor: borderColors,
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'right',
                labels: {
                  boxWidth: 15,
                  padding: 15
                }
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
                callbacks: {
                  label: function(context) {
                    const label = context.label || '';
                    const value = context.formattedValue;
                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                    const percentage = Math.round((context.raw / total) * 100);
                    return `${label}: ${value} (${percentage}%)`;
                  }
                }
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
