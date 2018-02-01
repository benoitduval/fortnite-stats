App = {
    initHighcharts: function() {

        $('.stats-chart').each(function () {
            var dataKills     = $(this).attr('data-kills');
            var dataDate      = $(this).attr('data-dates');
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
                    type: 'datetime',
                    crosshair: true,
                    labels: {
                      enabled: false
                    }
                }],
                yAxis: {
                    title: {
                        text: ''
                    },
                    min: 0,
                    allowDecimals: false,
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    area: {
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
                            radius: 2
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
                    type: 'area',
                    name: 'Kills',
                    data: JSON.parse(dataKills)
                }]
            });
        });

         $('.top-one-only').on('click', function(ev) {
            var target = $(ev.target);
            var isChecked = target.prop('checked');
            var category = target.attr('data-category');
            var statsChart = $(target.parents('.kills-per-match').find('.stats-chart'));
            var chartIndex = statsChart.attr('data-highcharts-chart');

            // Parse original data (full)
            var dataDates = JSON.parse(statsChart.attr('data-dates'));
            var dataKills = JSON.parse(statsChart.attr('data-kills'));

            if (isChecked) {
                // Filter kills & date by keeping only point with a marker (ie: top1)
                var filteredDates = [];
                var filteredKills = dataKills.filter(function(value, index) {
                    var toKeep = value.marker && value.y;

                    if (toKeep) {
                        filteredDates.push(dataDates[index]);
                    }

                    return toKeep;
                }).map(function(value) {
                    // Marker is not more required, only keep the primitive value
                    return value.y;
                });

                dataDates = filteredDates;
                dataKills = filteredKills;
            }

            // Update the chart using dates & kills (filtered or not)
            Highcharts.charts[chartIndex].update({
                xAxis: [{
                    categories: dataDates,
                }],
                series: [{
                    data: dataKills
                }]
            });
        });
    },

    initPieCharts: function () {
        $('.repartition-chart').each(function () {
            var data = $(this).attr('data-stats');
            Highcharts.chart(this, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: ' '
                },
                tooltip: {
                    pointFormat: 'Games: <b>{point.y}</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>[{point.name}] Kills</b> ({point.percentage:.1f}%)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Kills',
                    colorByPoint: true,
                    data: JSON.parse(data)
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
                    zoomType: 'x',
                    marginTop: 15
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
                        fontSize: '10px',
                        fontWeight: 'bold'
                    }
                },
                series: [{
                    type: 'area',
                    data: JSON.parse(dataRankScore),
                    tooltip: {
                        valuePrefix: '#'
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
    },

    initDatePicker: function() {
        $('.datepicker-from').datetimepicker({
            format: 'YYYY-MM-DD',
            maxDate: 'now',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            },
        });

        $('.datepicker-to').datetimepicker({
            format: 'YYYY-MM-DD',
            maxDate: 'now',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            },
        });

        $(".datepicker-from").on("dp.change", function (e) {
            $('.datepicker-to').data("DateTimePicker").minDate(e.date);
        });
        $(".datepicker-to").on("dp.change", function (e) {
            $('.datepicker-from').data("DateTimePicker").maxDate(e.date);
        });
    }
}
