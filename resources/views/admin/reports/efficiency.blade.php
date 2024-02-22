@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$menu ." Of ". ucwords(str_replace('_', ' ', $subType))}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">{{$menu ." Of ". ucwords(str_replace('_', ' ', $subType))}}</li>
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
                                @if(in_array($menu, ['Performance Report', 'Team Performance Report']))
                                    <div class="col-md-12 border mt-3 p-3" id="reportsFilterDiv">
                                        {!! Form::open(['id' => 'filterReportForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                        <div class="row">
                                            <div class="col-md-4">
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
                                            <div class="col-md-4">
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
                                            @if(isset($subType) && in_array($subType, ['sub_received', 'lead_sub_received']))
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        @if((isManager() || isLeadUser()) && $subType == 'lead_sub_received')
                                                            @if(isManager() && isLeadUser())
                                                                @php
                                                                    $allBdm  = \App\Models\Admin::getActiveBDM();
                                                                    $teamBdm = array_intersect_key($allBdm, array_flip(array_merge(getManagerAllUsers(), getTeamMembers())));
                                                                @endphp
                                                            @elseif(isManager())
                                                                @php
                                                                    $allBdm  = \App\Models\Admin::getActiveBDM();
                                                                    $teamBdm = array_intersect_key($allBdm, array_flip(getManagerAllUsers()));
                                                                @endphp
                                                            @elseif(isLeadUser())
                                                                @php
                                                                    $allBdm  = \App\Models\Admin::getActiveBDM();
                                                                    $teamBdm = array_intersect_key($allBdm, array_flip(getTeamMembers()));
                                                                @endphp
                                                            @endif
                                                            <label class="control-label" for="bdm">BDM</label>
                                                            {!! Form::select('bdm[]', $teamBdm, null, ['class' => 'form-control select2', 'id'=>'bdm', 'multiple' => true, 'data-placeholder' => 'Select BDM Users']) !!}
                                                        @elseif(getLoggedInUserRole() == 'admin')
                                                            <label class="control-label" for="bdm">BDM</label>
                                                            {!! Form::select('bdm[]', \App\Models\Admin::getActiveBDM(), null, ['class' => 'form-control select2', 'id'=>'bdm', 'multiple' => true, 'data-placeholder' => 'Select BDM Users']) !!}
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(getLoggedInUserRole() == 'admin')
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label" for="bdm_team">Select Team</label>
                                                            {!! Form::select('teams[]', getTeamIdWiseTeamName(\App\Models\Team::TEAM_TYPE_BDM), null, ['class' => 'form-control select2', 'id'=>'bdm_team', 'multiple' => true, 'data-placeholder' => 'Select Team']) !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($subType) && in_array($subType, ['sub_sent', 'lead_sub_sent']))
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        @if($subType == 'lead_sub_sent' && (isManager() || isLeadUser()))
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
{{--                                                            <label class="control-label" for="recruiter">Recruiter</label>--}}
{{--                                                            {!! Form::select('recruiter[]', $teamRec, null, ['class' => 'form-control select2', 'id'=>'recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}--}}
                                                        @elseif(getLoggedInUserRole() == 'admin')
                                                            <label class="control-label" for="recruiter">Recruiter</label>
                                                            {!! Form::select('recruiter[]', \App\Models\Admin::getActiveRecruiter(), null, ['class' => 'form-control select2', 'id'=>'recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(getLoggedInUserRole() == 'admin')
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label" for="rec_team">Select Team</label>
                                                            {!! Form::select('teams[]', getTeamIdWiseTeamName(\App\Models\Team::TEAM_TYPE_RECRUITER), null, ['class' => 'form-control select2', 'id'=>'rec_team', 'multiple' => true, 'data-placeholder' => 'Select Team']) !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="data_toggle">Show User Wise Data</label><br>
                                                    {!! Form::checkbox('', '', null, ['id' => 'show_user_wise_data', 'class' => 'toggle-checkbox', 'checked' => true, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'danger', 'data-size' => 'small']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-info float-right" onclick="searchReportData()">Search</button>
                                        <button class="btn btn-default float-right mr-2" onclick="clearReportData()">Clear</button>
                                        {!! Form::close() !!}
                                    </div>
                                @endif
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
        $( document ).ready(function(){
            searchReportData();
            $('#show_user_wise_data').bootstrapToggle('off');
        });

        function clearReportData()
        {
            $('#filterReportForm')[0].reset();
            $('select').trigger('change');
            $('#reportContent').html("");
            searchReportData();
            $('#show_user_wise_data').trigger('change');
        }

        function searchReportData()
        {
            {{--@if(isset($subType) && $subType == 'sub_received')
                if(!$('#bdm option:selected').length > 0) {
                    swal("Warning", "Please Select BDM", "warning");
                    return;
                }
            @endif
            @if(isset($subType) && $subType == 'sub_sent')
                if(!$('#recruiter option:selected').length > 0) {
                    swal("Warning", "Please Select Recruiter", "warning");
                    return;
                }
            @endif--}}
            $('#overlay').show();
            $.ajax({
                url: "{{route('reports',[$type, $subType])}}",
                type: "get",
                data: $("#filterReportForm").serialize(),
                success: function(responce){
                    if(responce.content){
                        $('#reportContent').html(responce.content);
                        $('#show_user_wise_data').trigger('change');
                        $(".efficiency-report-table").each(function () {
                            const tableID = $(this).attr("id");
                            $('#'+tableID).DataTable({
                                "order": [],
                                "bPaginate": false,
                                "bFilter": false,
                                "bInfo": false,
                                drawCallback: function (settings) {
                                    $(settings.nTable).find('tbody').find('td').removeClass('border-bottom');
                                    $(settings.nTable).find('tbody tr:last').find('td').addClass('border-bottom');
                                }
                            });
                        });
                    }
                    $('#overlay').hide();
                }
            });
        }

        $('#show_user_wise_data').change(function (){
            if($(this).is(':checked')){
                $('.user-wise-data').show();
            }else{
                $('.user-wise-data').hide();
            }
        });
    </script>
@endsection
