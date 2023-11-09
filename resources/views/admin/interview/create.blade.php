@extends('admin.layouts.app')
@section('content')
<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{$menu}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('interview.index')}}">{{$menu}}</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @include ('admin.error')
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Add {{$menu}}</h3>
                    </div>
                    {!! Form::open(['url' => route('interview.store'), 'id' => 'interviewForm', 'class' => 'form-horizontal','files'=>true]) !!}
                    {!! Form::hidden('job_id', null, ['class' => 'form-control', 'id' => 'job_id']) !!}
                    {!! Form::hidden('submission_id', null, ['class' => 'form-control', 'id' => 'submission_id']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::text(null, null, ['class' => 'form-control', 'placeholder' => 'Enter Job Id', 'id' => 'search_by_job_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button class="btn btn-info float-left" id="fill_by_job_id" type="button">Search</button>
                                </div>
                            </div>
                        </div>
                        @include ('admin.interview.form')
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('interview.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
                        <button class="btn btn-info float-right" type="submit">Add</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="candidateModel" tabindex="-1" role="dialog" aria-labelledby="candidateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="candidateModalLabel">Select Candidate Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="candidateNameDiv"> </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
