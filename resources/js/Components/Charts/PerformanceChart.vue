<template>
    <div class="w-full h-full" ref="chartContainer"></div>
  </template>

  <script>
  export default {
    props: {
      data: {
        type: Object,
        required: true
      }
    },
    mounted() {
      this.renderChart();

      // Rendre le graphique responsive
      window.addEventListener('resize', this.renderChart);
    },
    beforeUnmount() {
      window.removeEventListener('resize', this.renderChart);
    },
    methods: {
      renderChart() {
        const container = this.$refs.chartContainer;

        if (!container) return;

        // Vider le conteneur
        container.innerHTML = '';

        // Définir les dimensions du graphique
        const width = container.clientWidth;
        const height = Math.min(container.clientHeight, 300);
        const margin = { top: 20, right: 30, bottom: 30, left: 40 };
        const innerWidth = width - margin.left - margin.right;
        const innerHeight = height - margin.top - margin.bottom;

        // Créer l'élément SVG
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', width);
        svg.setAttribute('height', height);
        container.appendChild(svg);

        // Groupe principal
        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', `translate(${margin.left},${margin.top})`);
        svg.appendChild(g);

        // Données
        const data = this.data.items || [];

        if (data.length === 0) {
          const textElement = document.createElementNS('http://www.w3.org/2000/svg', 'text');
          textElement.setAttribute('x', innerWidth / 2);
          textElement.setAttribute('y', innerHeight / 2);
          textElement.setAttribute('text-anchor', 'middle');
          textElement.setAttribute('fill', '#888');
          textElement.textContent = 'Aucune donnée disponible';
          g.appendChild(textElement);
          return;
        }
// Échelles
const xScale = this.createXScale(data, innerWidth);
      const yScale = this.createYScale(data, innerHeight);

      // Axe X
      this.createXAxis(g, xScale, innerHeight, innerWidth);

      // Axe Y
      this.createYAxis(g, yScale);

      // Zone de courbe remplie
      this.createArea(g, data, xScale, yScale, innerHeight);

      // Lignes
      this.createLines(g, data, xScale, yScale);

      // Points
      this.createPoints(g, data, xScale, yScale);
    },

    createXScale(data, width) {
      const xValues = data.map(d => d.x);
      const xMin = Math.min(...xValues);
      const xMax = Math.max(...xValues);

      return {
        domain: [xMin, xMax],
        range: [0, width],
        convert: function(x) {
          return ((x - this.domain[0]) / (this.domain[1] - this.domain[0])) * this.range[1];
        }
      };
    },

    createYScale(data, height) {
      const yValues = data.map(d => d.y);
      const yMin = 0; // Commencer à zéro pour le temps
      const yMax = Math.max(...yValues) * 1.1; // Ajouter 10% d'espace

      return {
        domain: [yMin, yMax],
        range: [height, 0],
        convert: function(y) {
          return this.range[0] - ((y - this.domain[0]) / (this.domain[1] - this.domain[0])) * (this.range[0] - this.range[1]);
        }
      };
    },

    createXAxis(g, xScale, height, width) {
      // Ligne de l'axe X
      const xAxisLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
      xAxisLine.setAttribute('x1', 0);
      xAxisLine.setAttribute('y1', height);
      xAxisLine.setAttribute('x2', width);
      xAxisLine.setAttribute('y2', height);
      xAxisLine.setAttribute('stroke', '#ccc');
      g.appendChild(xAxisLine);

      // Graduations
      const tickCount = Math.min(this.data.items.length, 7); // Limiter le nombre de graduations
      const tickStep = (xScale.domain[1] - xScale.domain[0]) / (tickCount - 1);

      for (let i = 0; i < tickCount; i++) {
        const tickValue = xScale.domain[0] + i * tickStep;
        const tickX = xScale.convert(tickValue);

        // Ligne de graduation
        const tickLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        tickLine.setAttribute('x1', tickX);
        tickLine.setAttribute('y1', height);
        tickLine.setAttribute('x2', tickX);
        tickLine.setAttribute('y2', height + 5);
        tickLine.setAttribute('stroke', '#ccc');
        g.appendChild(tickLine);

        // Étiquette de graduation
        const tickLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        tickLabel.setAttribute('x', tickX);
        tickLabel.setAttribute('y', height + 20);
        tickLabel.setAttribute('text-anchor', 'middle');
        tickLabel.setAttribute('font-size', '10px');
        tickLabel.setAttribute('fill', '#666');

        // Formater la valeur (par exemple pour les dates)
        let formattedValue = tickValue;
        if (this.data.xType === 'date') {
          formattedValue = new Date(tickValue).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
        }
        tickLabel.textContent = formattedValue;

        g.appendChild(tickLabel);
      }
    },

    createYAxis(g, yScale) {
      // Ligne de l'axe Y
      const yAxisLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
      yAxisLine.setAttribute('x1', 0);
      yAxisLine.setAttribute('y1', 0);
      yAxisLine.setAttribute('x2', 0);
      yAxisLine.setAttribute('y2', yScale.range[0]);
      yAxisLine.setAttribute('stroke', '#ccc');
      g.appendChild(yAxisLine);

      // Graduations
      const tickCount = 5;
      const tickStep = (yScale.domain[1] - yScale.domain[0]) / (tickCount - 1);

      for (let i = 0; i < tickCount; i++) {
        const tickValue = yScale.domain[0] + i * tickStep;
        const tickY = yScale.convert(tickValue);

        // Ligne de graduation
        const tickLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        tickLine.setAttribute('x1', -5);
        tickLine.setAttribute('y1', tickY);
        tickLine.setAttribute('x2', 0);
        tickLine.setAttribute('y2', tickY);
        tickLine.setAttribute('stroke', '#ccc');
        g.appendChild(tickLine);

        // Ligne horizontale de grille
        const gridLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        gridLine.setAttribute('x1', 0);
        gridLine.setAttribute('y1', tickY);
        gridLine.setAttribute('x2', yScale.range[0]);
        gridLine.setAttribute('y2', tickY);
        gridLine.setAttribute('stroke', '#eee');
        gridLine.setAttribute('stroke-dasharray', '2,2');
        g.appendChild(gridLine);

        // Étiquette de graduation
        const tickLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        tickLabel.setAttribute('x', -10);
        tickLabel.setAttribute('y', tickY + 4);
        tickLabel.setAttribute('text-anchor', 'end');
        tickLabel.setAttribute('font-size', '10px');
        tickLabel.setAttribute('fill', '#666');
        tickLabel.textContent = Math.round(tickValue);
        g.appendChild(tickLabel);
      }
    },

    createArea(g, data, xScale, yScale, height) {
      // Créer le chemin de la zone
      let areaPath = '';

      // Commencer en bas à gauche
      const firstX = xScale.convert(data[0].x);
      areaPath += `M ${firstX},${height}`;

      // Dessiner jusqu'au premier point
      areaPath += ` L ${firstX},${yScale.convert(data[0].y)}`;

      // Dessiner les points suivants
      for (let i = 1; i < data.length; i++) {
        const x = xScale.convert(data[i].x);
        const y = yScale.convert(data[i].y);
        areaPath += ` L ${x},${y}`;
      }

      // Retourner en bas
      const lastX = xScale.convert(data[data.length - 1].x);
      areaPath += ` L ${lastX},${height} Z`;

      const area = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      area.setAttribute('d', areaPath);
      area.setAttribute('fill', 'url(#areaGradient)');
      area.setAttribute('opacity', '0.3');

      // Définir le gradient
      const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
      g.appendChild(defs);

      const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
      gradient.setAttribute('id', 'areaGradient');
      gradient.setAttribute('x1', '0');
      gradient.setAttribute('y1', '0');
      gradient.setAttribute('x2', '0');
      gradient.setAttribute('y2', '1');
      defs.appendChild(gradient);

      const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
      stop1.setAttribute('offset', '0%');
      stop1.setAttribute('stop-color', '#6366f1');
      gradient.appendChild(stop1);

      const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
      stop2.setAttribute('offset', '100%');
      stop2.setAttribute('stop-color', '#6366f1');
      stop2.setAttribute('stop-opacity', '0.1');
      gradient.appendChild(stop2);

      g.appendChild(area);
    },

    createLines(g, data, xScale, yScale) {
      // Créer le chemin de la ligne
      let pathData = '';

      data.forEach((d, i) => {
        const x = xScale.convert(d.x);
        const y = yScale.convert(d.y);

        if (i === 0) {
          pathData += `M ${x},${y}`;
        } else {
          pathData += ` L ${x},${y}`;
        }
      });

      const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      path.setAttribute('d', pathData);
      path.setAttribute('fill', 'none');
      path.setAttribute('stroke', '#6366f1');
      path.setAttribute('stroke-width', '2');
      g.appendChild(path);
    },

    createPoints(g, data, xScale, yScale) {
      data.forEach(d => {
        const x = xScale.convert(d.x);
        const y = yScale.convert(d.y);

        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', x);
        circle.setAttribute('cy', y);
        circle.setAttribute('r', '4');
        circle.setAttribute('fill', '#6366f1');
        circle.setAttribute('stroke', '#fff');
        circle.setAttribute('stroke-width', '2');
        g.appendChild(circle);
      });
    }
  }
};
</script>
