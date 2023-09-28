@extends('layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{$menu}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('admin/moi/').$mois->id }}">{{$menu}}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit {{$menu}}</h3>
                    </div>
                    {!! Form::open(['url' => url('admin/moi/'.$mois->id), 'method'=>'patch', 'id' => 'moi_form', 'class' => 'form-horizontal','files'=>true]) !!}
                    <div class="card-body">
                        @include ('moi.form')
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('moi.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
                        <button class="btn btn-info float-right" type="submit">Update</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
