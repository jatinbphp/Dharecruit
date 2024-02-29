<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title">Interview</h3>
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
                            <label class="control-label mr-3 mt-1 h5" for="date">From: </label>
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
                            {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker individual-interview-count-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'individual_interview_count_fromDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label mr-3 mt-1 h5" for="date">To: </label>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker individual-interview-count-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'individual_interview_count_toDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-5">
                            <div class="individual-interview-count-recruiter-type">
                                <div class="row">
                                    <div class="col-3 text-right">
                                        <label class="control-label mt-1 h5" for="individual_interview_count_recruiter">Recruiter:</label>
                                    </div>
                                    <div class="col-9">
                                        {!! Form::text('', null, ['placeholder' => 'Please Select user', 'width' => '100%', 'id' => 'individual_interview_count_recruiter']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="individual-interview-count-bdm-type">
                                <div class="row">
                                    <div class="col-3 text-right">
                                        <label class="control-label mt-1 h5" for="individual_interview_count_bdm">Bdm:</label>
                                    </div>
                                    <div class="col-9">
                                        {!! Form::text('', null, ['placeholder' => 'Please Select user', 'id' => 'individual_interview_count_bdm']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger active">
                                    <input type="radio" class="individual-interview-count-served-submission-type" name="individual-interview-count-options" data-type="monthly" autocomplete="off" checked="">Monthly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-interview-count-served-submission-type" name="individual-interview-count-options" data-type="weekly" autocomplete="off">Weekly
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-interview-count-served-submission-type" name="individual-interview-count-options" data-type="daily" autocomplete="off">Daily
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-interview-count-served-submission-type" name="individual-interview-count-options" data-type="time_frame" autocomplete="off">Time Frame
                                </label>
                            </div>
                        </div>
                        <div class="col-3 text-right">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-interview-count-day-type" name="individual-interview-count-day-options" data-type="30" autocomplete="off">30 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-interview-count-day-type" name="individual-interview-count-day-options" data-type="60" autocomplete="off">60 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="individual-interview-count-day-type" name="individual-interview-count-day-options" data-type="90" autocomplete="off">90 Days
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-right">
                    <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                        @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="individual-interview-count-user-type-bdm individual-interview-count-user-type" name="individual-interview-count-user-options" data-type="bdm" autocomplete="off">BDM
                            </label>
                        @endif
                        @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="individual-interview-count-user-type-recruiter individual-interview-count-user-type" name="individual-interview-count-user-options" data-type="recruiter" autocomplete="off">Recruiter
                        @endif
                    </div>
                </div>
            </div>
            <canvas id="individualinterviewCount" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var bdmData = {!! $bdm_team_data !!};
        var instanceBdm = $('#individual_interview_count_bdm').comboTree({
            source : bdmData,
            isMultiple:true,
            selectAll:true,
            cascadeSelect:true,
        });
        var recData = {!! $rec_team_data !!};
        var instanceRec = $('#individual_interview_count_recruiter').comboTree({
            source : recData,
            isMultiple:true,
            selectAll:true,
            cascadeSelect:true,
        });

        $(document).ready(function () {
            instanceBdm.selectAll();
            instanceRec.selectAll();
            @if(in_array(getLoggedInUserRole(), ['admin','bdm']))
                console.log('call');
                $('.individual-interview-count-user-type-bdm').click();
            @else
                $('.individual-interview-count-user-type-recruiter').click();
            @endif
            prepareIndividualInterviewCounts();

            $("#individual_interview_count_bdm, #individual_interview_count_recruiter").on('change', function () {
                prepareIndividualInterviewCounts();
            });
        });

        function prepareIndividualInterviewCounts() {
            $.ajax({
                url: "{{ route('getInterviewChartData') }}",
                data: {
                    '_token'    : '{{ csrf_token() }}',
                    'fromDate'  : $('#individual_interview_count_fromDate').val(),
                    'toDate'    : $('#individual_interview_count_toDate').val(),
                    'type'      : $(".individual-interview-count-served-submission-type:checked").attr("data-type"),
                    'bdmUser'   : instanceBdm.getSelectedIds(),
                    'recUser'   : instanceRec.getSelectedIds(),
                    'user_type' : $(".individual-interview-count-user-type:checked").attr("data-type"),
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var datasets = [];
                        Object.keys(response.interviewCounts).forEach(function (legend) {
                            var color = getRandomColor();
                            datasets.push({
                                label: legend,
                                backgroundColor: 'rgba(' + color + ', 0.7)',
                                borderColor: 'rgba(' + color + ', 1)',
                                borderWidth: 1,
                                data: Object.values(response.interviewCounts[legend]),
                            });
                        });
                        var ctx = document.getElementById('individualinterviewCount').getContext('2d');
                        var chartInstance = Chart.getChart('individualinterviewCount');
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

        $('.individual-interview-count-datepicker').change(function (){
            $('.individual-interview-count-day-type').parent().removeClass('active');
            prepareIndividualInterviewCounts();
        });

        $(".individual-interview-count-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#individual_interview_count_fromDate').val(formatDate(fromDate));
            $('#individual_interview_count_toDate').val(formatDate(toDate));
            prepareIndividualInterviewCounts();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }

        $('.individual-interview-count-served-submission-type').on('change', function() {
            prepareIndividualInterviewCounts();
        });

        $('.individual-interview-count-user-type').change(function (){
            var dayType = $(this).attr('data-type');
            if(dayType == 'bdm'){
                $('.individual-interview-count-bdm-type').show();
                $('.individual-interview-count-recruiter-type').hide();
            } else {
                $('.individual-interview-count-recruiter-type').show();
                $('.individual-interview-count-bdm-type').hide();
            }
            prepareIndividualInterviewCounts();
        });
    });
</script>
