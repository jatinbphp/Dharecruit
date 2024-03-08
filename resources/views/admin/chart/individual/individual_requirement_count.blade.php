<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-chart-line"></i> Requirement Count</h3>
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
                            <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="individual_req_count_vs_served_fromDate">From: </label>
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
                            {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker individual-req-count-datepicker form-control float-right chart-from-datepicker char-datepick', 'placeholder' => 'Select From Date', 'id' => 'individual_req_count_vs_served_fromDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="individual_req_count_vs_served_toDate">To: </label>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker individual-req-count-datepicker form-control float-right chart-to-datepicker char-datepick', 'placeholder' => 'Select To Date', 'id' => 'individual_req_count_vs_served_toDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-5">
                            <div class="row">
                                <div class="col-3">
                                    <label class="control-label mt-1 h5" style="font-weight: 400" for="individual_req_count">Bdm:</label>
                                </div>
                                <div class="col-9">
                                    {!! Form::text('', null, ['placeholder' => 'Please Select User', 'id' => 'individual_req_count', 'class' => 'chart-bdm-user']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger active">
                                    <input type="radio" class="individual-req-count-type" name="individual-req-count-options" data-type="monthly" autocomplete="off" checked="">Monthly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-req-count-type" name="individual-req-count-options" data-type="weekly" autocomplete="off">Weekly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-req-count-type" name="individual-req-count-options" data-type="daily" autocomplete="off">Daily
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-req-count-type" name="individual-req-count-options" data-type="time_frame" autocomplete="off">Time Frame
                                </label>
                            </div>
                        </div>
                        <div class="col-3 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-req-count-day-type" name="individual-req-count-cvs-served-status-day-options" data-type="30" autocomplete="off">30 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-req-count-day-type" name="individual-req-count-cvs-served-status-day-options" data-type="60" autocomplete="off">60 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-req-count-day-type" name="individual-req-count-cvs-served-status-day-options" data-type="90" autocomplete="off">90 Days
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-7"></div>
                <div class="col-5 text-right">
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col-1 text-right">
                            <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="individual_requirement_count_step_size">Step:</label>
                        </div>
                        <div class="col-3 text-right">
                            <select style="width: 100%" class="select2" id="individual_requirement_count_step_size">
                                <option value="0">Please Select</option>
                                @for ($i = 1; $i <= 10; $i++) {
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-2 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-requirement-count-next-prev-button" data-type="-1" name="individual-requirement-assign-next-prev-options" autocomplete="off"><i class="fa fa-arrow-circle-left" data-toggle="tooltip" title="Previous" data-trigger="hover"></i>
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-requirement-count-next-prev-button" data-type="1" name="individual-requirement-assign-next-prev-options" autocomplete="off"><i class="fa fa-arrow-circle-right" data-toggle="tooltip" title="Next" data-trigger="hover"></i>
                                </label>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input isIndividualUniqueForRecReq" id="unique_individual_requirement_count_for_bdm">
                                <label class="custom-control-label" for="unique_individual_requirement_count_for_bdm">Only Uniq Requirements</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <canvas id="individualRequirementCount" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var userColor = {!! $user_color !!};
        var myData = {!! $bdm_team_data !!};
        var instance = $('#individual_req_count').comboTree({
            source : myData,
            isMultiple:true,
            selectAll:true,
            cascadeSelect:true,
        });
        $(document).ready(function () {
            instance.selectAll();
            prepareindividualrequirementCount();

            $("#individual_req_count").on('change', function () {
                if (window.globalSelectedBdmCheck && window.globalSelectedBdmCheck.includes('individual_req_count')) {
                    window.globalSelectedBdmCheck = window.globalSelectedBdmCheck.filter(item => item !== 'individual_req_count');
                    instance.destroy();
                    instance = $('#individual_req_count').comboTree({
                        source : myData,
                        isMultiple:true,
                        selectAll:true,
                        cascadeSelect:true,
                        selected: (window.globalSelectedBdm) ? window.globalSelectedBdm : [],
                    });
                }
                prepareindividualrequirementCount();
            });
        });

        function prepareindividualrequirementCount() {
            var isUniqueReq = 0;
            if($('#unique_individual_requirement_count_for_bdm').is(':checked')){
                isUniqueReq = 1;
            }

            $.ajax({
                url: "{{ route('getIndividualRequirementCount') }}",
                data: {
                    '_token'        : '{{ csrf_token() }}',
                    'fromDate'      : $('#individual_req_count_vs_served_fromDate').val(),
                    'toDate'        : $('#individual_req_count_vs_served_toDate').val(),
                    'type'          : $(".individual-req-count-type:checked").attr("data-type"),
                    'selected_user' : instance.getSelectedIds(),
                    'isUniReq'      : isUniqueReq,
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var datasets = [];
                        Object.keys(response.requirementCount).forEach(function (legend) {
                            var color = (userColor.hasOwnProperty(legend) && userColor[legend]) ? userColor[legend] : getRandomColor();
                            datasets.push({
                                label: legend,
                                backgroundColor: color,
                                borderColor: color,
                                borderWidth: 1,
                                data: Object.values(response.requirementCount[legend]),
                            });
                        });
                        Chart.register(ChartDataLabels);
                        const defaultLegendClickHandler = Chart.defaults.plugins.legend.onClick;
                        var ctx = document.getElementById('individualRequirementCount').getContext('2d');
                        var chartInstance = Chart.getChart('individualRequirementCount');
                        if (chartInstance) {
                            chartInstance.destroy();
                        }
                        var barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.labels,
                                datasets: datasets,
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
                                        formatter: (value, context) => {
                                            return value > 0 ? [value, context.dataset.label.substring(0, 3)] : '';
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

        $('.individual-req-count-datepicker').change(function (){
            $('#individual_requirement_count_step_size').val("0").trigger("change");
            $('.individual-req-count-day-type').parent().removeClass('active');
            prepareindividualrequirementCount();
        });

        $(".individual-req-count-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            $('#individual_requirement_count_step_size').val("0").trigger("change");
            $(".individual-requirement-count-next-prev-button").prop("checked", false).parent().removeClass('active');
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#individual_req_count_vs_served_fromDate').val(formatDate(fromDate));
            $('#individual_req_count_vs_served_toDate').val(formatDate(toDate));
            prepareindividualrequirementCount();
        });

        $('.individual-req-count-type').on('change', function() {
            $("#individual_requirement_count_step_size").trigger("change");
            prepareindividualrequirementCount();
        });

        $('.isIndividualUniqueForRecReq').change(function (){
            prepareindividualrequirementCount();
        });
        $('.individual-requirement-count-next-prev-button').click(function (){
            const stepValue = parseInt($('#individual_requirement_count_step_size').val());
            if(!stepValue){
                swal('Error', 'Please Select Step Size', 'error');
                return;
            }
            const fromDateInput = $('#individual_req_count_vs_served_fromDate');
            const toDateInput   = $('#individual_req_count_vs_served_toDate');
            const stepType      = $(".individual-req-count-type:checked").attr("data-type");
            const step          = $(this).attr('data-type');
            setDateForNextPrevButtons(step, fromDateInput, toDateInput, stepValue, stepType);
            prepareindividualrequirementCount();
        })

        $('#individual_requirement_count_step_size').change(function(){
            const stepValue = parseInt($(this).val());
            if(!stepValue){
                return;
            }
            const fromDateInput = $('#individual_req_count_vs_served_fromDate');
            const toDateInput   = $('#individual_req_count_vs_served_toDate');

            $(".individual-req-count-day-type").prop("checked", false).parent().removeClass('active');
            const stepType = $(".individual-req-count-type:checked").attr("data-type");
            prepareDatesBasedOnStepSize(fromDateInput, toDateInput, stepValue, stepType);
            prepareindividualrequirementCount();
        });
    });
</script>
