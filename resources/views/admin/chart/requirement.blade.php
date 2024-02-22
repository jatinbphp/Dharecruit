<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Requirement and Submission Counts</h3>
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
            };

            var ctx = document.getElementById('reqVsSubChart').getContext('2d');
            var barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Requirement Count',
                        data: data.requirementCounts,
                        backgroundColor: '#688ade',
                        borderColor: '#0013b0',
                        borderWidth: 1
                    }, {
                        label: 'Submission Count',
                        data: data.submissionCounts,
                        backgroundColor: '#dc7979',
                        borderColor: '#ff0000',
                        borderWidth: 1
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
                        legend: {
                            display: true,
                            position: 'bottom', // Place legend at the bottom
                            labels: {
                                font: {
                                    family: 'Arial, sans-serif', // Specify font family
                                    size: 15, // Specify font size
                                    weight: 'bold', // Make font bold
                                    color: 'black' // Specify font color
                                }
                            }
                        },
                    },
                    onClick: function(event, elements) {
                        if(elements.length > 0) {
                            var clickedDatasetIndex = elements[0].datasetIndex;
                            var chart = this;

                            // Iterate over each dataset and show/hide based on the clicked series
                            chart.data.datasets.forEach(function(dataset, datasetIndex) {
                                if (datasetIndex === clickedDatasetIndex) {
                                    dataset.hidden = false;
                                } else {
                                    dataset.hidden = true;
                                }
                            });
                            chart.update();

                            // Update yAxis if the clicked series is "Submission"
                            if (clickedDatasetIndex === 1) {
                                chart.options.scales.y.reversed = true;
                            } else {
                                chart.options.scales.y.reversed = false;
                            }
                        }
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
