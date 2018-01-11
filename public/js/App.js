App = {
    initHighcharts: function() {
        $(document).ready(function () {
            $('.kills-chart').each(function () {
                var dataKills = $(this).attr('data-kills');

                Highcharts.chart(this, {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: ''
                    },
                    // tooltip: { enabled: false },
                    xAxis: {
                        labels: {
                          enabled: false
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            align: 'left'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Kills',
                        data: JSON.parse(dataKills),
                    }],
                    dataLabels: {
                        useHTML: true
                    }
                });
            });
        });
    }
}