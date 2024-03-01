<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-chart-pie"></i> Interview Status</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="interview_status_fromDate">From: </label>
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
                        {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker interview-status-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'interview_status_fromDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="interview_status_toDate">To: </label>
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker interview-status-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'interview_status_toDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-4 text-right">
                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                    <label class="btn btn-sm btn-outline-danger">
                        <input type="radio" class="interview-status-day-type" name="interview-status-day-options" data-type="30" autocomplete="off">30 Days
                    </label>
                    <label class="btn btn-sm btn-outline-danger">
                        <input type="radio" class="interview-status-day-type" name="interview-status-day-options" data-type="60" autocomplete="off">60 Days
                    </label>
                    <label class="btn btn-sm btn-outline-danger">
                        <input type="radio" class="interview-status-day-type" name="interview-status-day-options" data-type="90" autocomplete="off">90 Days
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-9">
                <div class="interview-status-recruiter-list">
                    <div class="row">
                        <div class="col-2">
                            <label class="control-label mt-1 h5" style="font-weight: 400" for="interview_status_recruiter">Recruiter:</label>
                        </div>
                        <div class="col-10">
                            {!! Form::text('', null, ['placeholder' => 'Please Select user', 'width' => '100%', 'id' => 'interview_status_recruiter']) !!}
                        </div>
                    </div>
                </div>
                <div class="interview-status-bdm-list">
                    <div class="row">
                        <div class="col-2">
                            <label class="control-label mt-1 h5" style="font-weight: 400" for="interview_status_bdm">Bdm:</label>
                        </div>
                        <div class="col-10">
                            {!! Form::text('', null, ['placeholder' => 'Please Select user', 'id' => 'interview_status_bdm']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-right">
                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                    @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                        <label class="btn btn-sm btn-outline-danger">
                            <input type="radio" class="interview-status-user-type-bdm interview-status-user-type" name="interview-status-user-options" data-type="bdm" autocomplete="off">BDM
                        </label>
                    @endif
                    @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                        <label class="btn btn-sm btn-outline-danger">
                            <input type="radio" class="interview-status-user-type-recruiter interview-status-user-type" name="interview-status-user-options" data-type="recruiter" autocomplete="off">Recruiter
                    @endif
                </div>
            </div>
        </div>
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <canvas id="interviewStatus" width="400" height="400"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var bdmData = {!! $bdm_team_data !!};
        var instanceBdm = $('#interview_status_bdm').comboTree({
            source : bdmData,
            isMultiple:true,
            selectAll:true,
            cascadeSelect:true,
        });
        var recData = {!! $rec_team_data !!};
        var instanceRec = $('#interview_status_recruiter').comboTree({
            source : recData,
            isMultiple:true,
            selectAll:true,
            cascadeSelect:true,
        });
        $(document).ready(function () {
            instanceBdm.selectAll();
            instanceRec.selectAll();
            @if(in_array(getLoggedInUserRole(), ['admin','bdm']))
                $('.interview-status-user-type-bdm').click();
            @else
                $('.interview-status-user-type-recruiter').click();
            @endif
            prepareinterviewStatus();

            $("#interview_status_bdm, #interview_status_recruiter").on('change', function () {
                prepareinterviewStatus();
            });
        });

        function prepareinterviewStatus() {
            $.ajax({
                url: "{{ route('getInterviewStatusData') }}",
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'fromDate' : $('#interview_status_fromDate').val(),
                    'toDate'   : $('#interview_status_toDate').val(),
                    'bdmUser'  : instanceBdm.getSelectedIds(),
                    'recUser'  : instanceRec.getSelectedIds(),
                    'type'     : $(".interview-status-user-type:checked").attr("data-type"),
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var ctx = document.getElementById('interviewStatus').getContext('2d');
                        var chartInstance = Chart.getChart('interviewStatus');
                        if (chartInstance) {
                            chartInstance.destroy(); // Destroy the chart instance if it exists
                        }
                        var myPieChart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: response.labels,
                                datasets: [{
                                    data: response.counts,
                                    backgroundColor: [
                                        'rgb(255, 193, 7, 0.7)',
                                        'rgb(220, 53, 69, 0.7)',
                                        'rgb(0, 123, 255, 0.7)',
                                        'rgb(40, 167, 69, 0.7)',
                                        'rgb(52, 58, 64, 0.7)',
                                        'rgb(140, 1, 1, 0.7)',
                                        'rgb(108, 117, 125, 0.7)',

                                    ],
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                hoverOffset: 4,
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        $('.interview-status-datepicker').change(function (){
            $('.interview-status-day-type').parent().removeClass('active');
            prepareinterviewStatus();
        });

        $(".interview-status-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#interview_status_fromDate').val(formatDate(fromDate));
            $('#interview_status_toDate').val(formatDate(toDate));
            prepareinterviewStatus();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }

        $('.interview-status-user-type').change(function (){
            var dayType = $(this).attr('data-type');
            if(dayType == 'bdm'){
                $('.interview-status-bdm-list').show();
                $('.interview-status-recruiter-list').hide();
            } else {
                $('.interview-status-recruiter-list').show();
                $('.interview-status-bdm-list').hide();
            }
            prepareinterviewStatus();
        });

        $('#interview-status-recruiter, #interview-status-bdm').change(function (){
            prepareinterviewStatus();
        });
    });
</script>
