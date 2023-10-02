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
                            <li class="breadcrumb-item"><a href="{{route('customers.index')}}">{{$menu}}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Edit {{$menu}}</h3>
                        </div>
                        {!! Form::model($customers,['url' => route('customers.update',['customer'=>$customers->id]),'method'=>'patch','id' => 'usersForm','class' => 'form-horizontal','files'=>true]) !!}
                        <div class="card-body">
                            <div class="callout callout-danger">
                                <h4><i class="fa fa-info"></i> Note:</h4>
                                <p>Leave Password and Confirm Password empty if you are not going to change the password.</p>
                            </div>
                            
                            @include ('admin.customers.form')
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('customers.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
                            <button class="btn btn-info float-right" type="submit">Update</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

