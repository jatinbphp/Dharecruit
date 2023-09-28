@extends('layouts.app')
@section('content')
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>PV Form</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">PV Form</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
    @include('flash.flash-message')
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">PV Form</h3>
            </div>
            <form method="POST" action="{{ route('admin.store-pv') }}">
            @csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">PV Company Name</label>
                      <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" required autocomplete="company_name" autofocus>
                      @error('company_name')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">POC Name</label>
                      <input id="poc_name" type="text" class="form-control @error('poc_name') is-invalid @enderror" name="poc_name" value="{{ old('poc_name') }}" required autocomplete="poc_name" autofocus>
                      @error('poc_name')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>
                </div>  
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">POC Email</label>
                      <input id="poc_email" type="email" class="form-control @error('poc_email') is-invalid @enderror" name="poc_email" value="{{ old('poc_email') }}" required autocomplete="poc_email" autofocus>
                      @error('poc_email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">POC Phone Number</label>
                      <input id="poc_phone" type="text" class="form-control @error('poc_phone') is-invalid @enderror" name="poc_phone" value="{{ old('poc_phone') }}" required autocomplete="poc_phone" autofocus>
                      @error('poc_phone')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">POC Location</label>
                      <input id="poc_location" type="text" class="form-control" name="poc_location" value="{{ old('poc_location') }}">
                    </div>
                  </div>  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">PV Company Location</label>
                      <input id="pv_company_location" type="text" class="form-control @error('pv_company_location') is-invalid @enderror" name="pv_company_location" value="{{ old('pv_company_location') }}">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Client Name</label>
                      <input id="client_name" type="text" class="form-control @error('client_name') is-invalid @enderror" name="client_name" value="{{ old('client_name') }}" required autocomplete="client_name" autofocus>
                      @error('client_name')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="check_display_client">Display Client</label>
                      <input id="check_display_client" type="checkbox" class="" name="check_display_client" value="1">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('js')
@endsection