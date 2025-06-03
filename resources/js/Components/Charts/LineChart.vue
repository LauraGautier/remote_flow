<template>
    <div :style="{ height: height }">
      <canvas ref="canvasRef"></canvas>
    </div>
  </template>

  <script>
  import { defineComponent, ref, onMounted, watch } from 'vue'
  import Chart from 'chart.js/auto'

  export default defineComponent({
    name: 'LineChart',
    props: {
      height: {
        type: String,
        default: '300px'
      },
      chartData: {
        type: Object,
        required: true
      },
      options: {
        type: Object,
        default: () => ({})
      }
    },
    setup(props) {
      const canvasRef = ref(null)
      let chart = null

      const createChart = () => {
        const ctx = canvasRef.value.getContext('2d')
        chart = new Chart(ctx, {
          type: 'line',
          data: props.chartData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            ...props.options
          }
        })
      }

      const updateChart = () => {
        if (chart) {
          chart.data = props.chartData
          chart.options = {
            responsive: true,
            maintainAspectRatio: false,
            ...props.options
          }
          chart.update()
        }
      }

      onMounted(() => {
        createChart()
      })

      watch(() => props.chartData, () => {
        updateChart()
      }, { deep: true })

      watch(() => props.options, () => {
        updateChart()
      }, { deep: true })

      return {
        canvasRef
      }
    }
  })
  </script>
