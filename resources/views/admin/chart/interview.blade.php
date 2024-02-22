<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Interview Counts</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <div class="row">
                <div class="col-2"></div>
                <div class="col-3">
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
                            {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker interview-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'fromDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="control-label mr-3 mt-1 h5" for="date">To: </label>
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker interview-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'toDate']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-4 text-right">
                    <div class="row">
                        <div class="col-8">
                            <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="day-type" name="options" data-type="30" autocomplete="off">30 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="day-type" name="options" data-type="60" autocomplete="off">60 Days
                                </label>
                                <label class="btn btn-sm btn-outline-danger">
                                    <input type="radio" class="day-type" name="options" data-type="90" autocomplete="off">90 Days
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            @if(getLoggedInUserRole() == 'admin')
                                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                    <label class="btn btn-sm btn-outline-danger">
                                        <input type="radio" class="user_type" name="user_options" data-type="bdm" autocomplete="off" checked="">BDM
                                    </label>
                                    <label class="btn btn-sm btn-outline-danger active">
                                        <input type="radio" class="user_type" name="user_options" data-type="recruiter" autocomplete="off">Recruiter
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <canvas id="interviewChart" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            prepareInterviewChart('bdm');
        });

        function prepareInterviewChart(){
            var type = $(".user_type:checked").attr("data-type");
            @if(in_array(getLoggedInUserRole(), ['bdm', 'recruiter']))
                type = '{{getLoggedInUserRole()}}';
            @endif
            if(!type){
                return;
            }

            $.ajax({
                url: "{{ route('getInterviewChartData') }}",
                method: 'POST',
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'type'     : type,
                    'fromDate' : $('#fromDate').val(),
                    'toDate'   : $('#toDate').val(),
                },
                success: function(response) {
                    if(response.status == 1){
                        var ctx = document.getElementById('interviewChart').getContext('2d');
                        var chartInstance = Chart.getChart('interviewChart');
                        if (chartInstance) {
                            chartInstance.destroy(); // Destroy the chart instance if it exists
                        }
                        var barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                // labels: response.label,
                                datasets: [{
                                    label: 'Interview Count',
                                    data: response.interviewCounts,
                                    backgroundColor: '#688ade',
                                    borderColor: '#0013b0',
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
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        $('.user_type').change(function (){
            prepareInterviewChart();
        });

        $('.interview-datepicker').change(function (){
            $('.day-type').parent().removeClass('active');
            prepareInterviewChart();
        });

        $(".day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#fromDate').val(formatDate(fromDate));
            $('#toDate').val(formatDate(toDate));
            prepareInterviewChart();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }
    });
</script>
