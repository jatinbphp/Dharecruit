@extends('admin.layouts.app')

@section('content')

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-logo">
            <a href="../../index2.html"><b>Shopping Cart <br></b>Admin</a>
        </div>
        @if(session('errormessage'))
        <div class="alert alert-danger">
            {{ session('errormessage') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                {!! Form::open(['url' => route('admin.signin'), 'method' => 'post']) !!}
                <div class="input-group mb-3">
                    {!! Form::email('email', '', ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) !!}
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>





</body>

</html>

@endsection