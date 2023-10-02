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
                            <li class="breadcrumb-item"><a href="{{url('admin/advertisement')}}">User</a></li>
                            <li class="breadcrumb-item active">Edit</li>
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
                            <h3 class="card-title">Edit User</h3>
                        </div>

                        {!! Form::model($user,['url' => url('admin/profile_update/'.$user->id),'method'=>'patch','id'=>'profileForm','class' => 'form-horizontal','files'=>true]) !!}
                            <div class="card-body">
                                <div class="callout callout-danger">
                                    <h4><i class="fa fa-info"></i> Note:</h4>
                                    <p>Leave Password and Confirm Password empty if you are not going to change the password.</p>
                                </div>

                                @include ('admin.users.profile_form')
                            </div>
                            <div class="card-footer">
                                <a href="{{ url('masteradmin/') }}" ><button class="btn btn-default" type="button">Back</button></a>
                                <button class="btn btn-info pull-right" type="submit">Edit</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
