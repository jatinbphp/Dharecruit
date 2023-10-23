@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper" style="min-height: 946px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$sub_menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('submission.show',['submission'=>$requirement['id']])}}">{{$sub_menu}}</a></li>
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
                            <h3 class="card-title">Add New {{$sub_menu}}</h3>
                        </div>
                        {!! Form::open(['url' => route('submission.store'), 'id' => 'submissionsForm', 'class' => 'form-horizontal','files'=>true]) !!}
                            <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            Search By Email Or Id   
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email', 'id' => 'search_email']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Id', 'id' => 'search_id']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <button class="btn btn-info float-left" id="search_fill" type="button">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="requirement_id" value="{{$requirement['id']}}">
                                    @include ('admin.submission.form')
                                </div>
                            <div class="card-footer">
                                <a href="{{ route('submission.show',['submission'=>$requirement['id']]) }}" ><button class="btn btn-default" type="button">Back</button></a>
                                <!-- <button class="btn btn-info float-right" type="submit">Add</button> -->
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection