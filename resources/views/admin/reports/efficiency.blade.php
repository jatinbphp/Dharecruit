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
                                @if($menu == 'Efficiency Report')
                                    <div class="col-md-12 border mt-3 p-3" id="reportsFilterDiv">
                                        {!! Form::open(['id' => 'filterRepoetForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
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
                                                        {!! Form::text('fromDate', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'fromDate']) !!}
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
                                                        {!! Form::text('toDate', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'toDate']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label" for="bdm">BDM</label>
                                                    {!! Form::select('bdm[]', \App\Models\Admin::getActiveBDM(), null, ['class' => 'form-control select2', 'id'=>'bdm', 'multiple' => true, 'data-placeholder' => 'Select BDM Users']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label" for="recruiter">Recruiter</label>
                                                    {!! Form::select('recruiter[]', \App\Models\Admin::getActiveRecruiter(), null, ['class' => 'form-control select2', 'id'=>'recruiter', 'multiple' => true, 'data-placeholder' => 'Select Recruiter Users']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-info float-right" onclick="searchReportData()">Search</button>
                                        <button class="btn btn-default float-right mr-2" onclick="clearRepoetData()">Clear</button>
                                        {!! Form::close() !!}
                                    </div>
                                @endif
                            </div>
                            <div class="row mt-3" id="reportContent">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('jquery')
    <script type="text/javascript">
        function clearRepoetData()
        {
            $('#filterRepoetForm')[0].reset();
            $('select').trigger('change');
            $('#reportContent').html("");
        }

        function searchReportData()
        {
            $.ajax({
                url: "{{url('admin/reports/')}}/efficiency",
                type: "get",
                data: $("#filterRepoetForm").serialize(),
                success: function(responce){
                    console.log(responce);
                    if(responce.content){
                        $('#reportContent').html(responce.content);
                    }
                }
            });
        }
    </script>
@endsection