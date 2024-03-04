<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-chart-line"></i> Requirement, Submission And Served Counts</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-4 text-right">
                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input isUnique" id="unique_requirement">
                                <label class="custom-control-label" for="unique_requirement">Only Uniq Requirements</label>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input isUnique" id="unique_submission">
                                <label class="custom-control-label" for="unique_submission">Only Uniq Submissions</label>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger active">
                                    <input type="radio" class="type" name="options" data-type="monthly" autocomplete="off" checked="">Monthly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="type" name="options" data-type="weekly" autocomplete="off">Weekly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="type" name="options" data-type="daily" autocomplete="off">Daily
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="type" name="options" data-type="time_frame" autocomplete="off">Time Frame
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <canvas id="reqVsSubChart" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            var data = {
                labels: {!! json_encode($monthlyLabels) !!},
                requirementCounts: {!! json_encode($monthlyRequiremtCounts) !!},
                submissionCounts: {!! json_encode($monthlySubmissionCounts) !!},
                servedCounts: {!! json_encode($monthlyServedCounts) !!},
            };
            Chart.register(ChartDataLabels);
            const defaultLegendClickHandler = Chart.defaults.plugins.legend.onClick;
            var ctx = document.getElementById('reqVsSubChart').getContext('2d');
            var barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Requirement Count',
                        data: data.requirementCounts,
                        backgroundColor: '#7eb0d5',
                        borderColor: '#375975',
                        borderWidth: 1,
                        hidden: true,
                    }, {
                        label: 'Submission Count',
                        data: data.submissionCounts,
                        backgroundColor: '#fd7f6f',
                        borderColor: '#ee3e28',
                        borderWidth: 1
                    }, {
                        label: 'Served Count',
                        data: data.servedCounts,
                        backgroundColor: '#8bd3c7',
                        borderColor: '#2b9383',
                        borderWidth: 1,
                        hidden: true,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    weight: 'bold',
                                    size: 14,
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    weight: 'bold',
                                    size: 14,
                                }
                            }
                        }
                    },
                    plugins: {
                        datalabels: {
                            color: 'black',
                            anchor: 'center',
                            align: 'center',
                            font: {
                                weight: 'bold'
                            },
                            formatter: (value) => {
                                return value > 0 ? value : '';
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            onClick: (evt, legendItem, legend) => {
                                const type = legend.chart.config.type;
                                let allLegendItemsState = [];

                                if (legendItem.text === 'Hide All' || legendItem.text === 'Show All') {
                                    if (typeof legend.hideAll === 'undefined') {
                                        legend.hideAll = false;
                                    }

                                    legend.chart.data.datasets.forEach((dataset, i) => {
                                        legend.chart.setDatasetVisibility(i, legend.hideAll)
                                    });

                                    legend.hideAll = !legend.hideAll;
                                    legend.chart.update();

                                    return;
                                }

                                defaultLegendClickHandler(evt, legendItem, legend);
                                allLegendItemsState = legend.chart.data.datasets.map((e, i) => (legend.chart.getDatasetMeta(i).hidden));

                                if (allLegendItemsState.every(el => !el)) {
                                    legend.hideAll = false;
                                    legend.chart.update();
                                } else if (allLegendItemsState.every(el => el)) {
                                    legend.hideAll = true;
                                    legend.chart.update();
                                }
                            },
                            labels: {
                                font: {
                                    family: 'Arial, sans-serif',
                                    size: 15,
                                    weight: 'bold',
                                    color: 'black'
                                },
                                generateLabels: (chart) => {
                                    const datasets = chart.data.datasets;
                                    const {
                                        labels: {
                                            usePointStyle,
                                            pointStyle,
                                            textAlign,
                                            color
                                        }
                                    } = chart.legend.options;

                                    const legendItems = chart._getSortedDatasetMetas().map((meta) => {
                                        const style = meta.controller.getStyle(usePointStyle ? 0 : undefined);
                                        return {
                                            text: datasets[meta.index].label,
                                            fillStyle: style.backgroundColor,
                                            fontColor: color,
                                            hidden: !meta.visible,
                                            lineCap: style.borderCapStyle,
                                            lineDash: style.borderDash,
                                            lineDashOffset: style.borderDashOffset,
                                            lineJoin: style.borderJoinStyle,
                                            strokeStyle: style.borderColor,
                                            pointStyle: pointStyle || style.pointStyle,
                                            rotation: style.rotation,
                                            textAlign: textAlign || style.textAlign,
                                            datasetIndex: meta.index
                                        };
                                    });

                                    legendItems.push({
                                        text: (!chart.legend.hideAll || typeof chart.legend.hideAll === 'undefined') ? 'Hide All' : 'Show All',
                                        fontColor: '#000',
                                        fillStyle: '#000',// Box color
                                        strokeStyle: '#000', // LineCollor around box
                                    });

                                    return legendItems;
                                }
                            }
                        },
                    }
                }
            });

            $('.type').on('change', function() {
                var type = $(this).attr('data-type');
                var isUniqueReq = 0;
                var isUniqueSub = 0;
                if($('#unique_requirement').is(':checked')){
                    isUniqueReq = 1;
                }
                if($('#unique_submission').is(':checked')){
                    isUniqueSub = 1;
                }
                prepareChartData(type, isUniqueReq, isUniqueSub);
            });

            $('.isUnique').change(function (){
                var type = $(".type:checked").attr("data-type");
                var isUniqueReq = 0;
                var isUniqueSub = 0;
                if($('#unique_requirement').is(':checked')){
                    isUniqueReq = 1;
                }
                if($('#unique_submission').is(':checked')){
                    isUniqueSub = 1;
                }
                prepareChartData(type, isUniqueReq, isUniqueSub);
            });

            function prepareChartData(type, isUniqueReq, isUniqueSub){
                if(!type){
                    return;
                }

                $.ajax({
                    url: "{{ route('getChartData') }}",
                    method: 'POST',
                    data: {
                        '_token' : '{{ csrf_token() }}',
                        'type'       : type,
                        'is_uni_req' : isUniqueReq,
                        'is_uni_sub' : isUniqueSub,
                    },
                    success: function(response) {
                        if(response.status == 1){
                            barChart.data.labels = response.label;
                            barChart.data.datasets[0].data = response.requiremtCounts;
                            barChart.data.datasets[1].data = response.submissionCounts;
                            barChart.data.datasets[2].data = response.servedCounts;
                            barChart.update();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
        });
    });
</script>
