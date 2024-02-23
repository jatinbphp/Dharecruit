<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Pv Status</h3>
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
                        {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker pv-status-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'pv_status_fromDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <div class="input-group">
                        <label class="control-label mr-3 mt-1 h5" for="date">To: </label>
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                        </div>
                        {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker pv-status-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'pv_status_toDate']) !!}
                    </div>
                </div>
            </div>
            <div class="col-4 text-right">
                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                    <label class="btn btn-sm btn-outline-danger">
                        <input type="radio" class="pv-status-day-type" name="pv-status-day-options" data-type="30" autocomplete="off">30 Days
                    </label>
                    <label class="btn btn-sm btn-outline-danger">
                        <input type="radio" class="pv-status-day-type" name="pv-status-day-options" data-type="60" autocomplete="off">60 Days
                    </label>
                    <label class="btn btn-sm btn-outline-danger">
                        <input type="radio" class="pv-status-day-type" name="pv-status-day-options" data-type="90" autocomplete="off">90 Days
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-9">
                <div class="pv-status-recruiter-list">
                    <div class="row">
                        <div class="col-2">
                            <label class="control-label mt-1" for="recruiter">Recruiter</label>
                        </div>
                        <div class="col-10">
                            @if(getLoggedInUserRole() == 'admin')
                                {!! Form::select('recruiter[]', \App\Models\Admin::getActiveRecruiter(), true, ['class' => 'form-control select2', 'id'=>'pv-status-recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
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
                                    {!! Form::select('recruiter[]', $teamRec, true, ['class' => 'form-control select2', 'id'=>'pv-status-recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                @else
                                    @php
                                        $recter[getLoggedInUserId()] = Auth::user()->name;
                                    @endphp
                                    {!! Form::select('recruiter[]', $recter, true, ['class' => 'form-control select2', 'id'=>'pv-status-recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="pv-status-bdm-list">
                    <div class="row">
                        <div class="col-2">
                            <label class="control-label mt-1" for="bdm">Bdm</label>
                        </div>
                        <div class="col-10">
                            @if(getLoggedInUserRole() == 'admin')
                                {!! Form::select('bdm[]', \App\Models\Admin::getActiveBDM(), true, ['class' => 'form-control select2', 'id'=>'pv-status-bdm', 'multiple' => true, 'data-placeholder' => 'Select Bdm Users']) !!}
                            @elseif(getLoggedInUserRole() == 'bdm')
                                @if((isManager() || isLeadUser()))
                                    @if(isManager() && isLeadUser())
                                        @php
                                            $allRec  = \App\Models\Admin::getActiveBdm();
                                            $teamRec = array_intersect_key($allRec, array_flip(array_merge(getManagerAllUsers(), getTeamMembers())));
                                        @endphp
                                    @elseif(isManager())
                                        @php
                                            $allRec  = \App\Models\Admin::getActiveBdm();
                                            $teamRec = array_intersect_key($allRec, array_flip(getManagerAllUsers()));
                                        @endphp
                                    @elseif(isLeadUser())
                                        @php
                                            $allRec  = \App\Models\Admin::getActiveBdm();
                                            $teamRec = array_intersect_key($allRec, array_flip(getTeamMembers()));
                                        @endphp
                                    @endif
                                    @php
                                        $teamRec[getLoggedInUserId()] = Auth::user()->name;
                                    @endphp
                                    {!! Form::select('bdm[]', $teamRec, true, ['class' => 'form-control select2', 'id'=>'pv-status-bdm', 'multiple' => true, 'data-placeholder' => 'Select Bdm Users']) !!}
                                @else
                                    @php
                                        $recter[getLoggedInUserId()] = Auth::user()->name;
                                    @endphp
                                    {!! Form::select('bdm[]', $recter, true, ['class' => 'form-control select2', 'id'=>'pv-status-bdm', 'multiple' => true, 'data-placeholder' => 'Select Bdmz Users']) !!}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 text-right">
                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                    @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                        <label class="btn btn-sm btn-outline-danger">
                            <input type="radio" class="pv-status-user-type-bdm pv-status-user-type" name="pv-status-user-options" data-type="bdm" autocomplete="off">BDM
                        </label>
                    @endif
                    @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                        <label class="btn btn-sm btn-outline-danger">
                            <input type="radio" class="pv-status-user-type-recruiter pv-status-user-type" name="pv-status-user-options" data-type="recruiter" autocomplete="off">Recruiter
                    @endif
                </div>
            </div>
        </div>
        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
            <canvas id="pvStatus" width="400" height="400"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            $('#pv-status-recruiter').select2({
                templateSelection: function(selection) {
                    var selectedOptions = $('#bdm-status-recruiter').val();
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

            $('#pv-status-bdm').select2({
                // Customize the display of selected elements
                templateSelection: function(selection) {
                    var selectedOptions = $('#bdm-status-bdm').val();
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
            @if(in_array(getLoggedInUserRole(), ['admin','bdm']))
                $('.pv-status-user-type-bdm').click();
            @else
                $('.pv-status-user-type-recruiter').click();
            @endif
            preparepvStatus();
        });

        function preparepvStatus() {
            $.ajax({
                url: "{{ route('getPvStatusData') }}",
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'fromDate' : $('#pv_status_fromDate').val(),
                    'toDate'   : $('#pv_status_toDate').val(),
                    'bdmUser'  : $('#pv-status-bdm').val(),
                    'recUser'  : $('#pv-status-recruiter').val(),
                    'type'     : $(".pv-status-user-type:checked").attr("data-type"),
                },
                method: 'POST',
                success: function (response) {
                    if (response.status == 1) {
                        var ctx = document.getElementById('pvStatus').getContext('2d');
                        var chartInstance = Chart.getChart('pvStatus');
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
                                        'rgb(220, 53, 69, 0.7)',
                                        'rgb(40, 167, 69, 0.7)',
                                        'rgb(140, 1, 1, 0.7)',
                                        'rgb(108, 117, 125, 0.7)',
                                        'rgb(52, 58, 64, 0.7)',

                                    ],
                                    borderColor: [
                                        'rgba(220, 53, 69, 1)',
                                        'rgba(40, 167, 69, 1)',
                                        'rgba(140, 1, 1, 1)',
                                        'rgba(108, 117, 125, 1)',
                                        'rgba(52, 58, 64, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        $('.pv-status-datepicker').change(function (){
            $('.pv-status-day-type').parent().removeClass('active');
            preparepvStatus();
        });

        $(".pv-status-day-type").change(function (){
            var dayType = parseInt($(this).attr('data-type'));
            if(!dayType){
                return;
            }
            var fromDate = new Date();
            var toDate = new Date();
            fromDate.setDate(fromDate.getDate() -  dayType);
            $('#pv_status_fromDate').val(formatDate(fromDate));
            $('#pv_status_toDate').val(formatDate(toDate));
            preparepvStatus();
        });

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return month + '/' + day + '/' + year;
        }

        $('.pv-status-user-type').change(function (){
            var dayType = $(this).attr('data-type');
            if(dayType == 'bdm'){
                $('.pv-status-bdm-list').show();
                $('.pv-status-recruiter-list').hide();
            } else {
                $('.pv-status-recruiter-list').show();
                $('.pv-status-bdm-list').hide();
            }
            preparepvStatus();
        });

        $('#pv-status-recruiter, #pv-status-bdm').change(function (){
            preparepvStatus();
        });
    });
</script>
