App = {
    initHighcharts: function() {
        $('.stats-chart').each(function () {
            var dataKills = $(this).attr('data-kills');
            var dataScore = $(this).attr('data-score');
            var dataDate  = $(this).attr('data-dates');
            var dataRankScore = $(this).attr('data-rank-score');
            var dataRankWin   = $(this).attr('data-rank-win');
            var dataRankKills = $(this).attr('data-rank-kills');

            Highcharts.chart(this, {
                chart: {
                    zoomType: 'x'
                },
                title: {
                    text: ''
                },
                credits: {
                    enabled: false
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

                }],
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    area: {
                        lineColor: Highcharts.getOptions().colors[0],
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, Highcharts.getOptions().colors[0]],
                                [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2,
                            fillColor: Highcharts.getOptions().colors[0]
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    }
                },
                series: [{
                    name: 'Kills',
                    type: 'area',
                    yAxis: 1,
                    data: JSON.parse(dataKills),

                }, {
                    name: 'Score',
                    type: 'spline',
                    data: JSON.parse(dataScore),
                }]
            });
        });
    },

    initRankCharts: function() {
        $('.rank-chart').each(function () {
            var dataRankScore = $(this).attr('data-rank');
            var title = $(this).attr('data-title');
            var color = $(this).attr('data-color');

            Highcharts.chart(this, {
                chart: {
                    zoomType: 'x'
                },
                title: {
                    text: ''
                },
                yAxis: {
                    title: {
                        text: title
                    }
                },
                xAxis: [{
                    // categories: JSON.parse(dataDate),
                    // crosshair: true,
                    labels: {
                      enabled: false
                    }
                }],
                legend: {
                    enabled: false
                },
                plotOptions: {
                    area: {
                        lineColor: Highcharts.getOptions().colors[color],
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, Highcharts.getOptions().colors[color]],
                                [1, Highcharts.Color(Highcharts.getOptions().colors[color]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2,
                            fillColor: Highcharts.getOptions().colors[color]
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    type: 'area',
                    data: JSON.parse(dataRankScore)
                }]
            });
        });
    }
}