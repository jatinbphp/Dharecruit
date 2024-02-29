@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                @if($loggedUser['role'] == 'admin')
                    <div class="row">
                        <div class="col-lg-3 col-6 mt-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{$users}}</h3>
                                    <p>Total Admin</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="{{route('user.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('admin.chart.individual.individual_submission')
                                    </div>
                                    <div class="col-md-12">
                                        @include('admin.chart.individual.individual_served')
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
