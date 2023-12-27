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
                            <li class="breadcrumb-item"><a href="{{route('requirement.index')}}">{{$menu}}</a></li>
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
                        <div class='add-new-form' style='{{(count($errors) == 0 ? "display: none;" : "")}}'>
                            {!! Form::open(['url' => route('requirement.store'), 'id' => 'requirementsForm', 'class' => 'form-horizontal','files'=>true]) !!}
                            <div class="card-body">
                                @include ('admin.requirement.form')
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('requirement.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
                                <button class="btn btn-info float-right" type="submit">Add</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="row mt-3 pl-3 pr-3 search-poc-email">
                            <div class="col-md-6  mt-3">
                                <div class="form-group">
                                    <label class="control-label" for="search_poc_email">POC Email Name : (Search Database)</label>
                                    {!! Form::text('search_poc_email', null, ['class' => 'form-control','placeholder' => 'Enter POC Email', 'id'=> 'search_poc_email']) !!}
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group mt-5">
                                    <button class="btn btn-info" id='search_by_poc_email' type="button">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
