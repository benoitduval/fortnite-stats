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
        /**
         * Override the reset function to avoid pointer reset if it hab been cleary disabled
         * (Required for synchronized charts only)
         */
        var pointerReset = Highcharts.Pointer.prototype.reset;
        Highcharts.Pointer.prototype.reset = function () {
            if (this.chart.disablePointerReset) {
                return undefined;
            }

            // Keep native bahavior
            return pointerReset.apply(this, arguments);
        };

        /**
         * Highlight a point by calling onMouseOver (which handle tooltip refresh) and drawing crosshair
         */
        Highcharts.Point.prototype.highlight = function (event) {
            this.onMouseOver(); // Show the hover marker
            this.series.chart.xAxis[0].drawCrosshair(event, this); // Show the crosshair
        };

        /**
         * Synchronize zooming through the setExtremes event handler.
         */
        function syncExtremes(e) {
            var thisChart = this.chart;

            // Extract chart index which are synchronize with thisChart
            var syncedIndex = $(thisChart.container).parents('.card-body').find('.rank-chart').map(function(i, div) {
                return $(div).attr('data-highcharts-chart') * 1;
            }).toArray();

            if (e.trigger !== 'syncExtremes') { // Prevent feedback loop
                Highcharts.each(Highcharts.charts, function (chart, i) {
                    // Affect only chart synchronized with thisChart
                    if (chart !== thisChart && syncedIndex.indexOf(i) >= 0) {
                        if (chart.xAxis[0].setExtremes) { // It is null while updating
                            chart.xAxis[0].setExtremes(e.min, e.max, undefined, false, { trigger: 'syncExtremes' });
                        }
                    }
                });
            }
        }

        $('.rank-chart').each(function () {
            var dataRankScore = $(this).attr('data-rank');
            var title = $(this).attr('data-title');
            var color = $(this).attr('data-color');

            var chart = Highcharts.chart(this, {
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
                    crosshair: true,
                    labels: {
                      enabled: false
                    },
                    events: {
                        setExtremes: syncExtremes
                    },
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
                tooltip: {
                    positioner: function () {
                        return {
                            x: this.chart.chartWidth - this.label.width, // right aligned
                            y: -10
                        };
                    },
                    borderWidth: 0,
                    backgroundColor: 'none',
                    pointFormat: '{point.y}',
                    headerFormat: '',
                    shadow: false,
                    style: {
                        fontSize: '12px'
                    }
                },
                series: [{
                    type: 'area',
                    data: JSON.parse(dataRankScore),
                    tooltip: {
                        valueSuffix: 'th' // Will be incoherent with first, second & third places
                    }
                }]
            });

            // Custom attribute to disable the pointer reset behavior
            chart.disablePointerReset = true;
        });

        var syncedGroups = ['solo-rank-charts', 'duo-rank-charts', 'squad-rank-charts'];
        syncedGroups.forEach(function(id) {
            $('#' + id).bind('mousemove touchmove touchstart', function (e) {
                var chart,
                    point,
                    event,
                    chartIndex;

                //Foreach chart if the synced group
                $('#' + id + ' .rank-chart').each(function(i, div) {
                    chartIndex = $(div).attr('data-highcharts-chart');

                    chart = Highcharts.charts[chartIndex];
                    event = chart.pointer.normalize(e.originalEvent); // Find coordinates within the chart
                    point = chart.series[0].searchPoint(event, true); // Get the hovered point

                    if (point) {
                        point.highlight(e);
                    }
                });
            });
        });
    }
}