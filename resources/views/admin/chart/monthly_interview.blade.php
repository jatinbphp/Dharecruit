<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Interview Counts <span class="font-weight-bold">({{Auth::user()->name}})</span></h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-2 text-right">
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
                        {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker interview-user-wise-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'interview_user_wise_fromDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-2 text-right">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label mr-3 mt-1 h5" for="date">To: </label>
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker interview-user-wise-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'interview_user_wise_toDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="row">
                    <div class="col-6 text-right">
                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-sm btn-outline-danger active">
                                <input type="radio" class="interview-user-type" name="interview-user-options" data-type="monthly" autocomplete="off" checked="">Monthly
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="interview-user-type" name="interview-user-options" data-type="weekly" autocomplete="off">Weekly
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="interview-user-type" name="interview-user-options" data-type="daily" autocomplete="off">Daily
                            </label>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="interview-user-day-type" name="interview-status-day-options" data-type="30" autocomplete="off">30 Days
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="interview-user-day-type" name="interview-status-day-options" data-type="60" autocomplete="off">60 Days
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="interview-user-day-type" name="interview-status-day-options" data-type="90" autocomplete="off">90 Days
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <canvas id="monthlyInterviewChart" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            prepareMonthlyinterview();
        });

        function prepareMonthlyinterview() {
            $.ajax({
                url: "{{ route('getMonthlyInterviewChartData') }}",
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'fromDate' : $('#interview_user_wise_fromDate').val(),
                    'toDate'   : $('#interview_user_wise_toDate').val(),
                    'type'    : $(".interview-user-type:checked").attr("data-type"),
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var ctx = document.getElementById('monthlyInterviewChart').getContext('2d');
                        var chartInstance = Chart.getChart('monthlyInterviewChart');
                        if (chartInstance) {
                            chartInstance.destroy(); // Destroy the chart instance if it exists
                        }
                        var barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.label,
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

        $('.interview-user-wise-datepicker').change(function (){
            $('.interview-user-day-type').parent().removeClass('active');
            prepareMonthlyinterview();
        });

        $(".interview-user-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#interview_user_wise_fromDate').val(formatDate(fromDate));
            $('#interview_user_wise_toDate').val(formatDate(toDate));
            prepareMonthlyinterview();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }

        $('.interview-user-type').on('change', function() {
            prepareMonthlyinterview();
        });
    });
</script>
