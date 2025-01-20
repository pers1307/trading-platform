$(function() {
    'use strict';

    // Make the dashboard widgets sortable Using jquery UI
    $('.connectedSortable').sortable({
        placeholder: 'sort-highlight',
        connectWith: '.connectedSortable',
        handle: '.card-header, .nav-tabs',
        forcePlaceholderSize: true,
        zIndex: 999999,
    });
    $('.connectedSortable .card-header').css('cursor', 'move');

    var salesGraphChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false,
        },
        scales: {
            xAxes: [
                {
                    ticks: {
                        fontColor: '#efefef',
                    },
                    gridLines: {
                        display: false,
                        color: '#efefef',
                        drawBorder: false,
                    },
                },
            ],
            yAxes: [
                {
                    ticks: {
                        stepSize: 5000,
                        fontColor: '#efefef',
                    },
                    gridLines: {
                        display: true,
                        color: '#efefef',
                        drawBorder: false,
                    },
                },
            ],
        },
    };
    const salesGraphChartCanvas = $('#line-chart-accaunt-1').get(0).getContext('2d');
    const data = $('#line-chart-accaunt-1').data('graph-data-encode');
    const label = $('#line-chart-accaunt-1').data('accaunt-name');
    const salesGraphChartData = {
        labels: data.labels,
        datasets: [
            {
                label: label,
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: true,
                borderColor: '#efefef',
                pointRadius: 3,
                pointHoverRadius: 7,
                pointColor: '#efefef',
                pointBackgroundColor: '#efefef',
                data: data.values,
            },
        ],
    };

    const salesGraphChart = new Chart(salesGraphChartCanvas, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: salesGraphChartData,
        options: salesGraphChartOptions,
    });

    const salesGraphChartCanvas2 = $('#line-chart-accaunt-2').get(0).getContext('2d');
    const data2 = $('#line-chart-accaunt-2').data('graph-data-encode');
    const label2 = $('#line-chart-accaunt-2').data('accaunt-name');
    const salesGraphChartData2 = {
        labels: data2.labels,
        datasets: [
            {
                label: label2,
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: true,
                borderColor: '#efefef',
                pointRadius: 3,
                pointHoverRadius: 7,
                pointColor: '#efefef',
                pointBackgroundColor: '#efefef',
                data: data2.values,
            },
        ],
    };

    const salesGraphChart2 = new Chart(salesGraphChartCanvas2, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: salesGraphChartData2,
        options: salesGraphChartOptions,
    });

    const salesGraphChartCanvas3 = $('#line-chart-accaunt-3').get(0).getContext('2d');
    const data3 = $('#line-chart-accaunt-3').data('graph-data-encode');
    const label3 = $('#line-chart-accaunt-3').data('accaunt-name');
    const salesGraphChartData3 = {
        labels: data3.labels,
        datasets: [
            {
                label: label3,
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: true,
                borderColor: '#efefef',
                pointRadius: 3,
                pointHoverRadius: 7,
                pointColor: '#efefef',
                pointBackgroundColor: '#efefef',
                data: data3.values,
            },
        ],
    };

    const salesGraphChart3 = new Chart(salesGraphChartCanvas3, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: salesGraphChartData3,
        options: salesGraphChartOptions,
    });

    const salesGraphChartCanvas4 = $('#line-chart-accaunt-4').get(0).getContext('2d');
    const data4 = $('#line-chart-accaunt-4').data('graph-data-encode');
    const label4 = $('#line-chart-accaunt-4').data('accaunt-name');
    const salesGraphChartData4 = {
        labels: data4.labels,
        datasets: [
            {
                label: label4,
                fill: false,
                borderWidth: 2,
                lineTension: 0,
                spanGaps: true,
                borderColor: '#efefef',
                pointRadius: 3,
                pointHoverRadius: 7,
                pointColor: '#efefef',
                pointBackgroundColor: '#efefef',
                data: data4.values,
            },
        ],
    };

    const salesGraphChart4 = new Chart(salesGraphChartCanvas4, { // lgtm[js/unused-local-variable]
        type: 'line',
        data: salesGraphChartData4,
        options: salesGraphChartOptions,
    });
});
