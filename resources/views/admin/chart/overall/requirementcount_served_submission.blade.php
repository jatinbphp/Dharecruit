<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-chart-line"></i> Requirement Count, Submission And Served</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="req_count_served_submission_fromDate">From: </label>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            @php
                                $defaultDays = 60;
                                $settingRow =  \App\Models\Setting::where('name', 'interview_date_default_filter_for_chart')->first();

                                if(!empty($settingRow) && $settingRow->value){
                                    $defaultDays = $settingRow->value;
                                }
                            @endphp
                            {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker req-count-served-submission-datepicker form-control float-right chart-from-datepicker char-datepick', 'placeholder' => 'Select From Date', 'id' => 'req_count_served_submission_fromDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="req_count_served_submission_toDate">To: </label>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker req-count-served-submission-datepicker form-control float-right chart-to-datepicker char-datepick', 'placeholder' => 'Select To Date', 'id' => 'req_count_served_submission_toDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-5">
                            <div class="row">
                                <div class="col-3">
                                    <label class="control-label mt-1 h5" style="font-weight: 400" for="req_count_served_submission">Bdm:</label>
                                </div>
                                <div class="col-9">
                                    {!! Form::text('', null, ['placeholder' => 'Please Select user', 'id' => 'req_count_served_submission', 'class' => 'chart-bdm-user']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger active">
                                    <input type="radio" class="req-count-served-submission-type" name="req-count-served-submission-options" data-type="monthly" autocomplete="off" checked="">Monthly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="req-count-served-submission-type" name="req-count-served-submission-options" data-type="weekly" autocomplete="off">Weekly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="req-count-served-submission-type" name="req-count-served-submission-options" data-type="daily" autocomplete="off">Daily
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="req-count-served-submission-type" name="req-count-served-submission-options" data-type="time_frame" autocomplete="off">Time Frame
                                </label>
                            </div>
                        </div>
                        <div class="col-3 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="req-count-served-submission-day-type" name="req-count-served-submission-day-options" data-type="30" autocomplete="off">30 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="req-count-served-submission-day-type" name="req-count-served-submission-day-options" data-type="60" autocomplete="off">60 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="req-count-served-submission-day-type" name="req-count-served-submission-day-options" data-type="90" autocomplete="off">90 Days
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8"></div>
                <div class="col-2 text-right">
                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input isUniqueForBdmReq" id="unique_requirement_for_bdm">
                        <label class="custom-control-label" for="unique_requirement_for_bdm">Only Uniq Requirements</label>
                    </div>
                </div>
                <div class="col-2 text-right">
                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input isUniqueForBdmReq" id="unique_submission_for_bdm">
                        <label class="custom-control-label" for="unique_submission_for_bdm">Only Uniq Submissions</label>
                    </div>
                </div>
            </div>
            <canvas id="requirementCountServedSubmission" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var myData = {!! $bdm_team_data !!};
        var instance = $('#req_count_served_submission').comboTree({
            source : myData,
            isMultiple:true,
            selectAll:true,
            cascadeSelect:true,
        });
        $(document).ready(function () {
            instance.selectAll();
            prepareReqCountServedSubmission();
        });

        function prepareReqCountServedSubmission() {
            var isUniqueReq = 0;
            var isUniqueSub = 0;
            if($('#unique_requirement_for_bdm').is(':checked')){
                isUniqueReq = 1;
            }
            if($('#unique_submission_for_bdm').is(':checked')){
                isUniqueSub = 1;
            }
            $.ajax({
                url: "{{ route('getRequirementCountServedSubmission') }}",
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'fromDate' : $('#req_count_served_submission_fromDate').val(),
                    'toDate'   : $('#req_count_served_submission_toDate').val(),
                    'type'    : $(".req-count-served-submission-type:checked").attr("data-type"),
                    'selected_user': instance.getSelectedIds(),
                    'isUniSub' : isUniqueSub,
                    'isUniReq' : isUniqueReq,
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        const defaultLegendClickHandler = Chart.defaults.plugins.legend.onClick;
                        Chart.register(ChartDataLabels);
                        var ctx = document.getElementById('requirementCountServedSubmission').getContext('2d');
                        var chartInstance = Chart.getChart('requirementCountServedSubmission');
                        if (chartInstance) {
                            chartInstance.destroy();
                        }
                        var barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.label,
                                datasets: [{
                                    label: 'Requirement Count',
                                    data: response.requiremenrtCount,
                                    backgroundColor: '#7eb0d5',
                                    borderColor: '#375975',
                                    borderWidth: 1,
                                    hidden: true,
                                },{
                                    label: 'Submission Count',
                                    data: response.submissionCount,
                                    backgroundColor: '#fd7f6f',
                                    borderColor: '#ee3e28',
                                    borderWidth: 1
                                },{
                                    label: 'Served Count',
                                    data: response.servedCounts,
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
                                        beginAtZero: true,
                                        ticks: {
                                            font: {
                                                weight: 'bold',
                                                size: 14,
                                            },
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
                                                    fillStyle: '#000',
                                                    strokeStyle: '#000',
                                                });

                                                return legendItems;
                                            }
                                        }
                                    },
                                },
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        $('.req-count-served-submission-datepicker').change(function (){
            $('.req-count-served-submission-day-type').parent().removeClass('active');
            prepareReqCountServedSubmission();
        });

        $(".req-count-served-submission-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#req_count_served_submission_fromDate').val(formatDate(fromDate));
            $('#req_count_served_submission_toDate').val(formatDate(toDate));
            prepareReqCountServedSubmission();
        });

        $('.req-count-served-submission-type').on('change', function() {
            prepareReqCountServedSubmission();
        });

        $('#recruiter').on('change', function(){
            prepareReqCountServedSubmission();
        });

        $("#req_count_served_submission").on('change', function () {
            if (window.globalSelectedBdmCheck && window.globalSelectedBdmCheck.includes('req_count_served_submission')) {
                window.globalSelectedBdmCheck = window.globalSelectedBdmCheck.filter(item => item !== 'req_count_served_submission');
                instance.destroy();
                instance = $('#req_count_served_submission').comboTree({
                    source : myData,
                    isMultiple:true,
                    selectAll:true,
                    cascadeSelect:true,
                    selected: (window.globalSelectedBdm) ? window.globalSelectedBdm : [],
                });
            }
            prepareReqCountServedSubmission();
        });

        $('.isUniqueForBdmReq').change(function (){
            prepareReqCountServedSubmission();
        });
    });
</script>
