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
                        <li class="breadcrumb-item"><a href="{{ route('category.index') }}">{{$menu}}</a></li>
                        <li class="breadcrumb-item active">Add</li>
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
                        <h3 class="card-title">Add {{$menu}}</h3>
                    </div>
                    {!! Form::open(['url' => route('category.store'), 'id' => 'category_form', 'class' => 'form-horizontal','files'=>true]) !!}
                    <div class="card-body">
                        @include ('category.form')
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('category.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
                        <button class="btn btn-info float-right" type="submit">Add</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection
