App = {
    initHighcharts: function() {
        $(document).ready(function () {
            $('.stats-chart').each(function () {
                var dataKills = $(this).attr('data-kills');
                var dataScore = $(this).attr('data-score');
                var dataDate  = $(this).attr('data-dates');
                var dataRankScore = $(this).attr('data-rank-score');
                var dataRankWin   = $(this).attr('data-rank-win');
                var dataRankKills = $(this).attr('data-rank-kills');

                console.log(JSON.parse(dataRankWin));

                Highcharts.chart(this, {
                    chart: {
                        zoomType: 'x'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: [{
                        categories: JSON.parse(dataDate),
                        crosshair: true,
                        labels: {
                          enabled: false
                        }
                    }],
                    yAxis: [{ // Primary yAxis
                        labels: {
                            format: '{value}',
                            style: {
                                color: Highcharts.getOptions().colors[1]
                            }
                        },
                        title: {
                            text: 'Score',
                            style: {
                                color: Highcharts.getOptions().colors[1]
                            }
                        },
                        opposite: true

                    }, { // Secondary yAxis
                        gridLineWidth: 0,
                        title: {
                            text: 'Kills',
                            style: {
                                color: Highcharts.getOptions().colors[0]
                            }
                        },
                        labels: {
                            format: '{value}',
                            style: {
                                color: Highcharts.getOptions().colors[0]
                            }
                        }

                    }, { // Tertiary yAxis
                        gridLineWidth: 0,
                        title: {
                            text: 'Ranking',
                            style: {
                                color: Highcharts.getOptions().colors[2]
                            }
                        },
                        labels: {
                            format: '#{value}',
                            style: {
                                color: Highcharts.getOptions().colors[2]
                            }
                        },
                        opposite: true
                    }],
                    tooltip: {
                        shared: true
                    },
                    series: [{
                        name: 'Kills',
                        type: 'column',
                        yAxis: 1,
                        data: JSON.parse(dataKills),

                    }, {
                        name: 'Score',
                        type: 'spline',
                        data: JSON.parse(dataScore),
                    }, {
                        name: 'Ranks Wins',
                        type: 'spline',
                        yAxis: 2,
                        data: JSON.parse(dataRankWin),
                    }, {
                        name: 'Ranks Kills',
                        type: 'spline',
                        yAxis: 2,
                        data: JSON.parse(dataRankKills),
                    }, {
                        name: 'Ranks Score',
                        type: 'spline',
                        yAxis: 2,
                        data: JSON.parse(dataRankScore),
                    }]
                });

                // Highcharts.chart(this, {
                //     chart: {
                //         type: 'line'
                //     },
                //     title: {
                //         text: ''
                //     },
                //     // tooltip: { enabled: false },
                //     xAxis: {
                //         labels: {
                //           enabled: false
                //         }
                //     },
                //     yAxis: {
                //         min: 0,
                //         title: {
                //             text: '',
                //             align: 'left'
                //         },
                //         labels: {
                //             overflow: 'justify'
                //         }
                //     },
                //     plotOptions: {
                //         bar: {
                //             dataLabels: {
                //                 enabled: true
                //             }
                //         }
                //     },
                //     credits: {
                //         enabled: false
                //     },
                //     series: [{
                //         name: 'Kills',
                //         data: JSON.parse(dataKills),
                //     }],
                //     dataLabels: {
                //         useHTML: true
                //     }
                // });
            });
        });
    }
}