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
                            {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker individual-req-count-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'individual_req_count_vs_served_fromDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="individual-req-count-toDate">To: </label>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker individual-req-count-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'individual-req-count-toDate']) !!}
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
                                    {!! Form::text('', null, ['placeholder' => 'Please Select User', 'id' => 'individual_req_count']) !!}
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
                <div class="col-10"></div>
                <div class="col-2 text-right">
                    <div class="custom-control custom-switch custom-switch-off-default custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input isIndividualUniqueForRecReq" id="unique_individual_requirement_count_for_bdm">
                        <label class="custom-control-label" for="unique_individual_requirement_count_for_bdm">Only Uniq Requirements</label>
                    </div>
                </div>
            </div>
            <canvas id="individualRequirementCount" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
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
                    'toDate'        : $('#individual-req-count-toDate').val(),
                    'type'          : $(".individual-req-count-type:checked").attr("data-type"),
                    'selected_user' : instance.getSelectedIds(),
                    'isUniReq'      : isUniqueReq,
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var datasets = [];
                        Object.keys(response.requirementCount).forEach(function (legend) {
                            var color = getRandomColor();
                            datasets.push({
                                label: legend,
                                backgroundColor: 'rgba(' + color + ', 0.7)',
                                borderColor: 'rgba(' + color + ', 1)',
                                borderWidth: 1,
                                data: Object.values(response.requirementCount[legend]),
                            });
                        });
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
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            font: {
                                                family: 'Arial, sans-serif',
                                                size: 15,
                                                weight: 'bold',
                                                color: 'black'
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
            $('.individual-req-count-day-type').parent().removeClass('active');
            prepareindividualrequirementCount();
        });

        $(".individual-req-count-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#individual_req_count_vs_served_fromDate').val(formatDate(fromDate));
            $('#individual-req-count-toDate').val(formatDate(toDate));
            prepareindividualrequirementCount();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }

        $('.individual-req-count-type').on('change', function() {
            prepareindividualrequirementCount();
        });

        $("#individual_req_count").on('change', function () {
            prepareindividualrequirementCount();
        });

        $('.isIndividualUniqueForRecReq').change(function (){
            prepareindividualrequirementCount();
        });
    });
</script>
