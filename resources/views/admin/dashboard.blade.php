@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @if($loggedUser['role'] == 'admin')
                        <div class="col-lg-3 col-6 mt-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{$adminUsers}}</h3>
                                    <p>Total Admin</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="{{route('user.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 mt-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{$bdmUsers}}</h3>
                                    <p>Total BDM</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="{{route('bdm_user.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 mt-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{$recUsers}}</h3>
                                    <p>Total Recruiters</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="{{route('recruiter_user.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6 mt-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{$totalRequirements}}</h3>
                                    <p>Total Requirements</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-registered"></i>
                                </div>
                                <a href="{{route('requirement.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    <div class="col-12 @if(getLoggedInUserRole() != 'admin') mt-3 @endif">
                        <div class="card card-primary card-tabs">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="false">Overall</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Individual</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="row w-100 mt-3 border border-dark border-with-label" data-label="Global Filter">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="global_from_date">From: </label>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                                {!! Form::text('fromDate',\Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker global-date-datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'global_from_date']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="global_to_date">To: </label>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                                {!! Form::text('fromDate',\Carbon\Carbon::now()->format('m/d/Y'), ['autocomplete' => 'off', 'class' => 'datepicker global-date-datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'global_to_date']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                                        <div class="col-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="global_bdm_user">Bdm: </label>
                                                    {!! Form::text('', null, ['placeholder' => 'Please Select BDM User', 'id' => 'global_bdm_user']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                                        <div class="col-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <label class="control-label mr-3 mt-1 h5" style="font-weight: 400" for="global_rec_user">Rec: </label>
                                                    {!! Form::text('', null, ['placeholder' => 'Please Select Rec User', 'id' => 'global_rec_user']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-2">
                                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                                            <label class="btn btn-sm btn-outline-danger">
                                                <input type="radio" class="global-frame-type global-frame-type-time-frame" data-type="time_frame" name="global-frame-type-options" autocomplete="off">Time Frame
                                            </label>
                                            <label class="btn btn-sm btn-outline-danger">
                                                <input type="radio" class="global-frame-type global-frame-type-submission-frame" data-type="submission_frame" name="global-frame-type-options" autocomplete="off">Submission Frame
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                        <div class="row">
                                            @if(getLoggedInUserRole() == 'admin')
                                                <div class="col-md-12">
                                                    @include('admin.chart.overall.requirement_submission_served')
                                                </div>
                                            @endif
                                            <div class="col-md-12" id="interviews">
                                                @include('admin.chart.overall.interviews')
                                            </div>
                                        </div>
                                        @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                                            <div class="row">
                                                <div class="col-md-12" id="req_assigned_served_submission">
                                                    @include('admin.chart.overall.requirementassigned_served_submission')
                                                </div>
                                            </div>
                                        @endif
                                        @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                                            <div class="row">
                                                <div class="col-md-12" id="req_assigned_served_submission">
                                                    @include('admin.chart.overall.requirementcount_served_submission')
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-12" id="bdm_accept_vs_submitted_end_client">
                                                @include('admin.chart.overall.bdm_accept_submitted')
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                @include('admin.chart.overall.bdm_status')
                                            </div>
                                            <div class="col-6">
                                                @include('admin.chart.overall.pv_status')
                                            </div>
                                            <div class="col-6">
                                                @include('admin.chart.overall.interview_status')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                        <div class="row">
                                            <div class="col-md-12 @if(isLeadUser() || isManager()) mt-5 @endif">
                                                @include('admin.chart.individual.interview')
                                            </div>
                                        </div>
                                        @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('admin.chart.individual.individual_requirement_assigned')
                                                </div>
                                            </div>
                                        @endif
                                        @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('admin.chart.individual.individual_requirement_count')
                                                </div>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-12">
                                                @include('admin.chart.individual.individual_submission')
                                            </div>
                                            <div class="col-md-12">
                                                @include('admin.chart.individual.individual_served')
                                            </div>
                                            <div class="col-md-12">
                                                @include('admin.chart.individual.individual_bdm_accept')
                                            </div>
                                            <div class="col-md-12">
                                                @include('admin.chart.individual.individual_pv_sub_end_client')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var bdmData = {!! $bdm_team_data !!};
        @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
            instanceGlobalBdm = $('#global_bdm_user').comboTree({
                source : bdmData,
                isMultiple:true,
                selectAll:true,
                cascadeSelect:true,
            });
            instanceGlobalBdm.selectAll();
        @endif
        var recData = {!! $rec_team_data !!};
        @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
            instanceGlobalRec = $('#global_rec_user').comboTree({
                source : recData,
                isMultiple:true,
                selectAll:true,
                cascadeSelect:true,
            });
            instanceGlobalRec.selectAll();
        @endif

        $("#global_bdm_user").change(function () {
            @if(in_array(getLoggedInUserRole(), ['admin', 'bdm']))
                window.globalSelectedBdm = instanceGlobalBdm.getSelectedIds();
                $('.chart-bdm-user').each(function() {
                    var id = $(this).attr('id');
                    if (!window.globalSelectedBdmCheck) {
                        window.globalSelectedBdmCheck = [];
                    }
                    window.globalSelectedBdmCheck.push(id);
                    $('#'+id).trigger('change');
                });
                $('.chart-bdm-user').trigger('change');
            @endif
        });

        $("#global_rec_user").change(function () {
            @if(in_array(getLoggedInUserRole(), ['admin', 'recruiter']))
                window.globalSelectedRec = instanceGlobalRec.getSelectedIds();
                $('.chart-rec-user').each(function() {
                    var id = $(this).attr('id');
                    if (!window.globalSelectedRecCheck) {
                        window.globalSelectedRecCheck = [];
                    }
                    window.globalSelectedRecCheck.push(id);
                    $('#'+id).trigger('change');
                });
                $('.chart-rec-user').trigger('change');
            @endif
        });

        $('.global-date-datepicker').change(function (){
            var globalFromDate = $('#global_from_date').val();
            var globalToDate   = $('#global_to_date').val();

            if(globalFromDate){
                $('.chart-from-datepicker').val(globalFromDate);
            }

            if(globalToDate){
                $('.chart-to-datepicker').val(globalToDate);
            }
            $('.char-datepick').trigger('change');
        });

        $('.global-frame-type').change(function (){
            if($(this).attr('data-type') == 'time_frame'){
                $('.time-frame').trigger('click');
            } else {
                $('.submission-frame').trigger('click');
            }
        });
    });
</script>

