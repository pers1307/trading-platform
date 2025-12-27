$(function () {
  'use strict'

  // Make the dashboard widgets sortable Using jquery UI
  $('.connectedSortable').sortable({
    placeholder: 'sort-highlight',
    connectWith: '.connectedSortable',
    handle: '.card-header, .nav-tabs',
    forcePlaceholderSize: true,
    zIndex: 999999
  })
  $('.connectedSortable .card-header').css('cursor', 'move')

  var salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d')

  const data = $('#line-chart').data('graph-data-encode')

  var salesGraphChartData = {
    labels: data.labels,
    datasets: [
      {
        label: 'Вклад',
        fill: false,
        borderWidth: 2,
        lineTension: 0,
        spanGaps: true,
        borderColor: '#d4af37',
        pointRadius: 3,
        pointHoverRadius: 7,
        pointColor: '#d4af37',
        pointBackgroundColor: '#d4af37',
        data: data.depositValues
      },
      {
        label: 'Счет',
        fill: false,
        borderWidth: 2,
        lineTension: 0,
        spanGaps: true,
        borderColor: '#ffffff',
        pointRadius: 3,
        pointHoverRadius: 7,
        pointColor: '#ffffff',
        pointBackgroundColor: '#ffffff',
        data: data.balanceValues
      },
      {
        label: 'Инфляция',
        fill: false,
        borderWidth: 2,
        lineTension: 0,
        spanGaps: true,
        borderColor: '#ff0000',
        pointRadius: 3,
        pointHoverRadius: 7,
        pointColor: '#ff0000',
        pointBackgroundColor: '#ff0000',
        data: data.inflationValues
      }
    ]
  }

  var salesGraphChartOptions = {
    maintainAspectRatio: false,
    responsive: true,
    legend: {
      display: true,
      position: 'top'
    },
    scales: {
      xAxes: [{
        ticks: {
          fontColor: '#efefef'
        },
        gridLines: {
          display: false,
          color: '#efefef',
          drawBorder: false
        }
      }],
      yAxes: [{
        ticks: {
          stepSize: 5000,
          fontColor: '#efefef'
        },
        gridLines: {
          display: true,
          color: '#efefef',
          drawBorder: false
        }
      }]
    }
  }

  // eslint-disable-next-line no-unused-vars
  var salesGraphChart = new Chart(salesGraphChartCanvas, {
    type: 'line',
    data: salesGraphChartData,
    options: salesGraphChartOptions
  })
})
