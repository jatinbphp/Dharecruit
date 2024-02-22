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
                @if(getLoggedInUserRole() == 'admin' || isLeadUser() || isManager())
                    <div class="row">
                        @if(getLoggedInUserRole() == 'admin' || isLeadUser() || isManager())
                            <div class="col-md-12 @if(isLeadUser() || isManager()) mt-5 @endif">
                                @include('admin.chart.interview')
                            </div>
                        @endif
                        @if(getLoggedInUserRole() == 'admin')
                            <div class="col-md-12">
                                @include('admin.chart.requirementvsserved')
                            </div>
                            <div class="col-md-12">
                                @include('admin.chart.requirement')
                            </div>
                            <div class="col-6">
                                @include('admin.chart.bdm_status')
                            </div>
                            <div class="col-6">
                                @include('admin.chart.pv_status')
                            </div>
                            <div class="col-6">
                                @include('admin.chart.interview_status')
                            </div>
                        @endif
                    </div>
                @endif
                @if(in_array(getLoggedInUserRole(), ['bdm', 'recruiter']))
                    <div class="row mt-5">
                        <div class="col-md-12" id="monthly_interview">
                            @include('admin.chart.monthly_interview')
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
