<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Requirement Assigned And Served</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
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
                        {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker req-assign-vs-served-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'req_assign_vs_served_fromDate']) !!}
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
                        {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker req-assign-vs-served-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'req_assign_vs_served_toDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-2">
                                <label class="control-label mt-1" for="recruiter">Recruiter</label>
                            </div>
                            <div class="col-10">
                                @if(getLoggedInUserRole() == 'admin')
                                    {!! Form::select('recruiter[]', \App\Models\Admin::getActiveRecruiter(), true, ['class' => 'form-control select2', 'id'=>'recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                @elseif(getLoggedInUserRole() == 'recruiter')
                                    @if((isManager() || isLeadUser()))
                                        @if(isManager() && isLeadUser())
                                            @php
                                                $allRec  = \App\Models\Admin::getActiveRecruiter();
                                                $teamRec = array_intersect_key($allRec, array_flip(array_merge(getManagerAllUsers(), getTeamMembers())));
                                            @endphp
                                        @elseif(isManager())
                                            @php
                                                $allRec  = \App\Models\Admin::getActiveRecruiter();
                                                $teamRec = array_intersect_key($allRec, array_flip(getManagerAllUsers()));
                                            @endphp
                                        @elseif(isLeadUser())
                                            @php
                                                $allRec  = \App\Models\Admin::getActiveRecruiter();
                                                $teamRec = array_intersect_key($allRec, array_flip(getTeamMembers()));
                                            @endphp
                                        @endif
                                        @php
                                            $teamRec[getLoggedInUserId()] = Auth::user()->name;
                                        @endphp
                                        {!! Form::select('recruiter[]', $teamRec, true, ['class' => 'form-control select2', 'id'=>'recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                    @else
                                        @php
                                            $recter[getLoggedInUserId()] = Auth::user()->name;
                                         @endphp
                                        {!! Form::select('recruiter[]', $recter, true, ['class' => 'form-control select2', 'id'=>'recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-sm btn-outline-danger active">
                                <input type="radio" class="req-assign-vs-served-type" name="req-assign-vs-served-options" data-type="monthly" autocomplete="off" checked="">Monthly
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="req-assign-vs-served-type" name="req-assign-vs-served-options" data-type="weekly" autocomplete="off">Weekly
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="req-assign-vs-served-type" name="req-assign-vs-served-options" data-type="daily" autocomplete="off">Daily
                            </label>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="req-assign-vs-served-day-type" name="interview-status-day-options" data-type="30" autocomplete="off">30 Days
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="req-assign-vs-served-day-type" name="interview-status-day-options" data-type="60" autocomplete="off">60 Days
                            </label>
                            <label class="btn btn-sm btn-outline-danger">
                                <input type="radio" class="req-assign-vs-served-day-type" name="interview-status-day-options" data-type="90" autocomplete="off">90 Days
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <canvas id="requirementAssignVsServed" style="min-height: 250px; height: 250px; max-height: 360px; max-width: 100%; display: block; width: 570px;" width="570" height="250" class="chartjs-render-monitor"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            $('#recruiter').select2({
                // Customize the display of selected elements
                templateSelection: function(selection) {
                    var selectedOptions = $('#recruiter').val();
                    var text = selection.text.trim();
                    if (selectedOptions.length > 2) {
                        return '<span class="ellipsis">' + text.substring(0, 2) + '...</span>';
                    }
                    return selection.text;
                },
                escapeMarkup: function(markup) {
                    return markup; // Allow HTML to be rendered
                }
            });
            prepareReqAssignAsServed();
        });

        function prepareReqAssignAsServed() {
            $.ajax({
                url: "{{ route('getRequirementAssignedVsServed') }}",
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'fromDate' : $('#req_assign_vs_served_fromDate').val(),
                    'toDate'   : $('#req_assign_vs_served_toDate').val(),
                    'type'    : $(".req-assign-vs-served-type:checked").attr("data-type"),
                    'selected_user': ($('#recruiter').val() ? $('#recruiter').val() : [])
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var ctx = document.getElementById('requirementAssignVsServed').getContext('2d');
                        var chartInstance = Chart.getChart('requirementAssignVsServed');
                        if (chartInstance) {
                            chartInstance.destroy(); // Destroy the chart instance if it exists
                        }
                        var barChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.label,
                                datasets: [{
                                    label: 'Assign Count',
                                    data: response.assignedRequiremenrtCount,
                                    backgroundColor: '#688ade',
                                    borderColor: '#0013b0',
                                    borderWidth: 1
                                }, {
                                    label: 'Served Count',
                                    data: response.recruiterservedCounts,
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

        $('.req-assign-vs-served-wise-datepicker').change(function (){
            $('.req-assign-vs-served-day-type').parent().removeClass('active');
            prepareReqAssignAsServed();
        });

        $(".req-assign-vs-served-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#req_assign_vs_served_fromDate').val(formatDate(fromDate));
            $('#req_assign_vs_served_toDate').val(formatDate(toDate));
            prepareReqAssignAsServed();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }

        $('.req-assign-vs-served-type').on('change', function() {
            prepareReqAssignAsServed();
        });

        $('#recruiter').on('change', function(){
            prepareReqAssignAsServed();
        });
    });
</script>
