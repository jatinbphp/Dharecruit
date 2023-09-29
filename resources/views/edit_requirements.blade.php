@extends('layouts.app')
@section('content')
<style type="text/css">
    input[type=checkbox] {
        -ms-transform: scale(2); 
        -moz-transform: scale(2); 
        -webkit-transform: scale(2); 
        -o-transform: scale(2); 
        transform: scale(2);
        margin-left: 6px;
        margin-top: 10px;
    }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Requirement Form</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Requirement Form</li>
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
                        <h3 class="card-title">Edit Requirement</h3>
                    </div>
                    <form method="POST" action="{{ url('admin/update-requirements/'. $requirements->id) }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Job Title</label>
                                        <input id="job_title" type="text" class="form-control @error('job_title') is-invalid @enderror" name="job_title" value="{{ old('job_title', $requirements->job_title) }}"  autocomplete="job_title" autofocus>
                                        @error('job_title')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">No # Position</label>
                                        <input id="no_position" type="text" class="form-control @error('no_position') is-invalid @enderror" name="no_position" value="{{ old('no_position', $requirements->no_position) }}"  autocomplete="no_position" autofocus>
                                        @error('no_position')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Experience</label>
                                        <input id="experience" type="text" class="form-control @error('experience') is-invalid @enderror" name="experience" value="{{ old('experience', $requirements->experience) }}"  autocomplete="experience" autofocus>
                                        @error('experience')
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
                                        <label for="exampleInputEmail1">Locations</label>
                                        <input id="locations" type="text" class="form-control @error('locations') is-invalid @enderror" name="locations" value="{{ old('locations', $requirements->locations) }}"  autocomplete="locations" autofocus>
                                        @error('locations')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Onsite/Hybrid/Remote</label>
                                        <input id="work_type" type="text" class="form-control @error('work_type') is-invalid @enderror" name="work_type" value="{{ old('work_type', $requirements->work_type) }}"  autocomplete="work_type" autofocus>
                                        @error('work_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Duration</label>
                                        <input id="duration" type="text" class="form-control @error('duration') is-invalid @enderror" name="duration" value="{{ old('duration', $requirements->duration) }}"  autocomplete="duration" autofocus>
                                        @error('duration')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Visa</label>
                                        <input id="visa" type="text" class="form-control @error('visa') is-invalid @enderror" name="visa" value="{{ old('visa', $requirements->visa) }}"  autocomplete="visa" autofocus>
                                        @error('visa')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Client</label>
                                        <input id="client" type="text" class="form-control @error('client') is-invalid @enderror" name="client" value="{{ old('client', $requirements->client) }}"  autocomplete="client" autofocus>
                                        @error('client')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Vendor Rate</label>
                                        <input id="vendor_rate" type="text" class="form-control @error('vendor_rate') is-invalid @enderror" name="vendor_rate" value="{{ old('vendor_rate', $requirements->vendor_rate) }}"  autocomplete="vendor_rate" autofocus>
                                        @error('vendor_rate')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="my_rate">My Rate</label>
                                        <input id="my_rate" type="text" class="form-control @error('my_rate') is-invalid @enderror" name="my_rate" value="{{ old('my_rate', $requirements->my_rate) }}"  autocomplete="my_rate" autofocus>
                                        @error('my_rate')
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
                                        <label for="priority">Priority</label>
                                        <select class="form-control" name="priority" onchange="displayReasonField(this)">
                                            <option value="">--Select Priority--</option>
                                            <option value="medium" {{ $requirements->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ $requirements->priority == 'high' ? 'selected' : '' }}>High</option>
                                        </select>
                                        @error('priority')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="term">Term</label>
                                        <select class="form-control" name="term" >
                                            <option value="">--Select Term--</option>
                                            <option value="C2C" {{ $requirements->term == 'C2C' ? 'selected' : '' }}>C2C</option>
                                            <option value="C2H" {{ $requirements->term == 'C2H' ? 'selected' : '' }}>C2H</option>
                                            <option value="W2" {{ $requirements->term == 'W2' ? 'selected' : '' }}>W2</option>
                                            <option value="FullTime" {{ $requirements->term == 'FullTime' ? 'selected' : '' }}>FullTime</option>
                                        </select>
                                        @error('term')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="priority_reason_row" style="{{ !empty($requirements->priority_reason) ? 'display: block;' : 'display: none' }}">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="priority_reason">Priority Reason</label>
                                        <textarea class="form-control" cols="12" rows="3" id="priority_reason" name="priority_reason">{{ !empty($requirements->priority_reason) ? $requirements->priority_reason : null }}</textarea>
                                        @error('priority_reason')
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
                                        <label for="category">Category</label>
                                        <select class="form-control" name="category" >
                                            <option value="">--Select Category--</option>
                                            @if(isset($categories) && !empty($categories))
                                                @foreach($categories as $key => $value)
                                                    <option value="{{ $value->id }}" {{ $requirements->category == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('category')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror                      
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="MOI">MOI</label>
                                        <select class="form-control" name="MOI" >
                                           <option value="">--Select MOI    --</option>
                                            @if(isset($mois) && !empty($mois))
                                                @foreach($mois as $key => $value)
                                                    <option value="{{ $value->id }}" {{ $requirements->MOI == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('MOI')
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
                                        <label for="job_keyword">Job Keyword</label>
                                        <input id="job_keyword" type="text" class="form-control @error('job_keyword') is-invalid @enderror" name="job_keyword" value="{{ old('job_keyword', $requirements->job_keyword) }}"  autocomplete="job_keyword" autofocus>
                                        @error('job_keyword')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="special_notes">Special Notes</label>
                                        <input id="special_notes" type="text" class="form-control" name="special_notes" value="{{ old('special_notes', $requirements->special_notes) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="job_description">Job Description</label>
                                        <textarea class="form-control" cols="12" rows="5" name="job_description">{{ !empty($requirements->job_description) ? $requirements->job_description : null }}</textarea>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">PV Company Name</label>
                                        <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name', $requirements->company_name) }}"  autocomplete="company_name" autofocus>
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
                                        <input id="poc_name" type="text" class="form-control @error('poc_name') is-invalid @enderror" name="poc_name" value="{{ old('poc_name', $requirements->poc_name) }}"  autocomplete="poc_name" autofocus>
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
                                        <input id="poc_email" type="email" class="form-control @error('poc_email') is-invalid @enderror" name="poc_email" value="{{ old('poc_email', $requirements->poc_email) }}"  autocomplete="poc_email" autofocus>
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
                                        <input id="poc_phone" type="text" class="form-control @error('poc_phone') is-invalid @enderror" name="poc_phone" value="{{ old('poc_phone', $requirements->poc_phone) }}"  autocomplete="poc_phone" autofocus>
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
                                        <input id="poc_location" type="text" class="form-control" name="poc_location" value="{{ old('poc_location', $requirements->poc_location) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">PV Company Location</label>
                                        <input id="pv_company_location" type="text" class="form-control @error('pv_company_location') is-invalid @enderror" name="pv_company_location" value="{{ old('pv_company_location', $requirements->pv_company_location) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Client Name</label>
                                        <input id="client_name" type="text" class="form-control @error('client_name') is-invalid @enderror" name="client_name" value="{{ old('client_name', $requirements->client_name) }}"  autocomplete="client_name" autofocus>
                                        @error('client_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="check_display_client">Display Client</label><br>
                                        <input id="check_display_client" type="checkbox" class="" name="check_display_client" {{ $requirements->check_display_client == 1 ? 'checked' : null }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('admin.my-requirement') }}" ><button class="btn btn-default" type="button">Back</button></a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript">
    function displayReasonField(event){
        if(event.value == "high"){
            $('#priority_reason_row').css('display', 'block');
        }else{
            $('#priority_reason_row').css('display', 'none');
            $('#priority_reason').html('');
        }
    }
</script>
@endsection