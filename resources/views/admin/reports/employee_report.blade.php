@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">{{$menu}}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @include ('admin.error')
            <div id="responce" class="alert alert-success" style="display: none"></div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12 border mt-3 p-3" id="reportsFilterDiv">
                                    {!! Form::open(['id' => 'filterReportForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="date">From Date</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                    </div>
                                                    @php
                                                        $defaultDays = 30;
                                                        $settingRow =  \App\Models\Setting::where('name', 'show_bet_date_data_on_pv_and_poc_reports')->first();

                                                        if(!empty($settingRow) && $settingRow->value){
                                                            $defaultDays = $settingRow->value;
                                                        }
                                                    @endphp
                                                    {!! Form::text('fromDate', \Carbon\Carbon::now()->subDays($defaultDays)->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'fromDate']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="date">To Date</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                    </div>
                                                    {!! Form::text('toDate', \Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'toDate']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="employer_name">Employer Name</label>
                                                {!! Form::select('employer_name[]', \App\Models\Admin::getActiveEmployers(), null, ['class' => 'form-control select2', 'id'=>'employer_name', 'multiple' => true, 'data-placeholder' => 'Select Employer Name']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="employee_name">Employee Name</label>
                                                {!! Form::select('employee_name[]', \App\Models\Admin::getActiveEmployees(), null, ['class' => 'form-control select2', 'id'=>'employee_name', 'multiple' => true, 'data-placeholder' => 'Select Employee Name']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="who_added">Who Added</label>
                                                {!! Form::select('who_added[]', \App\Models\Admin::getActiveRecruiter(), null, ['class' => 'form-control select2', 'id'=>'who_added', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="vendor_email">Vendor Email</label>
                                                {!! Form::text('vendor_email', null, ['autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Enter Vendor Email', 'id' => 'vendor_email']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="vendor_phone">Vendor Phone</label>
                                                {!! Form::text('vendor_phone', null, ['autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'Enter Vendor Phone ', 'id' => 'vendor_phone']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="bdm_count">Recruiter Name</label>
                                                {!! Form::select('recruiter_names[]', \App\Models\Admin::getActiveRecruiter(), null, ['class' => 'form-control select2', 'id'=>'recruiter_names', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="data_toggle">Show Only Row With Data</label><br>
                                                {!! Form::checkbox('', '', null, ['id' => 'data_toggle', 'class' => 'toggle-checkbox', 'checked' => true, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'danger', 'data-size' => 'small']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="toggle_columns">Show Email And Phone</label><br>
                                                {!! Form::checkbox('', '', null, ['id' => 'toggle_columns', 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'danger', 'data-size' => 'small']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="toggle_categorties">Show Categories</label><br>
                                                {!! Form::checkbox('', '', null, ['id' => 'toggle_categorties', 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'danger', 'data-size' => 'small']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="toggle_counts">Show Counts</label><br>
                                                {!! Form::checkbox('', '', null, ['id' => 'toggle_counts', 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'danger', 'data-size' => 'small']) !!}
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="control-label" for="data_toggle">Select Frame</label><br>
                                                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                                    <label class="btn btn-sm btn-outline-danger">
                                                        <input type="radio" class="employee-frame-type employee-frame-type-time-frame" data-type="time_frame" name="frame_type" value="time_frame" autocomplete="off">Time Frame
                                                    </label>
                                                    <label class="btn btn-sm btn-outline-danger">
                                                        <input type="radio" class="employee-frame-type" data-type="submission_frame" name="frame_type" value="submission_frame" autocomplete="off">Submission Frame
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-info float-right" onclick="searchReportData()">Search</button>
                                    <button class="btn btn-default float-right mr-2" onclick="clearReportData()">Clear</button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <div id="overlay" style="display: none">
                                <div id="spinner"></div>
                            </div>
                            <div class="row mt-3" id="reportContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('jquery')
    <script type="text/javascript">
        $(document).ready(function (){
            $('.employee-frame-type-time-frame').trigger('click');
            //searchReportData();
        });

        function clearReportData()
        {
            $('#filterReportForm')[0].reset();
            $('select').trigger('change');
            $('#reportContent').html("");
            searchReportData();
            $('#data_toggle').trigger('change');
            $('#toggle_columns').trigger('change');
            $('#toggle_categorties').trigger('change');
            $('#toggle_counts').trigger('change');
        }

        function searchReportData()
        {
            // if(!$('#p_v_company option:selected').length > 0) {
            //     swal("Warning", "Please Select PV Company", "warning");
            //     return;
            // }
            // if(!$('#poc_name option:selected').length > 0) {
            //     swal("Warning", "Please Select Atleast One POC", "warning");
            //     return;
            // }
            $('#overlay').show();
            $.ajax({
                url: "{{route('reports',['type' => $type, 'subType' => null])}}",
                type: "get",
                data: $("#filterReportForm").serialize(),
                success: function(responce){
                    if(responce.content){
                        $('#reportContent').html(responce.content);
                        $('#data_toggle').trigger('change');
                        $('#toggle_columns').trigger('change');
                        $('#toggle_categorties').trigger('change');
                        $('#toggle_counts').trigger('change');
                        $('#poc_report').DataTable({
                            "order": [],
                            "bPaginate": false,
                            "bFilter": false,
                            "bInfo": false,
                        });
                    }
                    $('#overlay').hide();
                }
            });
        }

        {{--$('#p_v_company').on('change', function() {--}}
        {{--    const selectedCompany = $(this).val();--}}
        {{--    const pocDropdown = $('#poc_name');--}}
        {{--    const selectedPoc = pocDropdown.val();--}}
        {{--    pocDropdown.empty().trigger("change");--}}
        {{--    if(selectedCompany && selectedCompany.length > 0){--}}
        {{--        $.ajax({--}}
        {{--            url: '{{route('reports.getPocNames')}}',--}}
        {{--            method: 'POST',--}}
        {{--            data: { companyName: selectedCompany, _token: '{{csrf_token()}}' },--}}
        {{--            success: function(data) {--}}
        {{--                $.each(data, function(index, value) {--}}
        {{--                    pocDropdown.append($('<option>', {--}}
        {{--                        value: value,--}}
        {{--                        text: value--}}
        {{--                    }));--}}
        {{--                });--}}
        {{--                pocDropdown.val(selectedPoc).trigger("change");--}}
        {{--            },--}}
        {{--            error: function(xhr, status, error) {--}}
        {{--                console.error(error);--}}
        {{--            }--}}
        {{--        });--}}
        {{--    }--}}
        {{--});--}}

        $('#toggle_columns').change(function (){
            var toggleClasses = [];
            var totalColmns = [];

            @if(isset($hideColumns) && $hideColumns)
                toggleClasses = <?php echo json_encode($hideColumns);?>;
            @endif
                @if(isset($totalShowCompanyColumn) && $totalShowCompanyColumn)
                totalColmns = <?php echo json_encode($totalShowCompanyColumn);?>;
            @endif
            var totalColumnsLength = parseInt(totalColmns.length);
            var toggleDataLength = parseInt(toggleClasses.length);
            if($(this).is(':checked')){
                $.each(toggleClasses, function(index, currentClass) {
                    $('.' + currentClass).show();
                });
                totalColspanValue = totalColumnsLength + toggleDataLength;
                if(totalColspanValue){
                    $('.company-name').attr('colspan', totalColspanValue);
                    $('tr td:nth-child(' + totalColumnsLength + ')').removeClass('border-right');
                    $('tr td:nth-child(' + totalColspanValue + ')').addClass('border-right');
                }
            }else{
                $.each(toggleClasses, function(index, currentClass) {
                    $('.' + currentClass).hide();
                });
                if(totalColumnsLength){
                    totalColspanValue = totalColumnsLength + toggleDataLength;
                    $('.company-name').attr('colspan', totalColumnsLength);
                    $('tr td:nth-child(' + totalColspanValue + ')').removeClass('border-right');
                    $('tr td:nth-child(' + totalColumnsLength + ')').addClass('border-right');
                }
            }
        });

        $('#toggle_categorties').change(function (){
            if($(this).is(':checked')){
                $('.category_wise_count').show();
            }else{
                $('.category_wise_count').hide();
            }
        });

        $('#toggle_counts').change(function (){
            if($(this).is(':checked')){
                $('.show-count').show();
            }else{
                $('.show-count').hide();
            }
        });

        $('.employee-frame-type').change(function (){
            searchReportData();
        });
    </script>
@endsection
