<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="date">From Date</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'fromDate']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="date">To Date</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'toDate']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="job_title">Job Title</label>
            {!! Form::text('job_title', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Title', 'id' => 'job_title']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="bdm">BDM</label>
            {!! Form::select('bdm', \App\Models\Admin::getActiveBDM(), null, ['class' => 'form-control select2','id'=>'bdm'],['data-id' => '1']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="job_id">Job Id</label>
            {!! Form::text('job_id', null, ['autocomplete' => 'off', 'class' => 'form-control float-right', 'placeholder' => 'Enter Job Id', 'id' => 'job_id']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="client">Client</label>
            {!! Form::text('client', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Email', 'id' => 'client']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="job_location">Job Location</label>
            {!! Form::text('job_location', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Location', 'id' => 'job_location']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="moi">MOI</label>
            {!! Form::select('moi', \App\Models\Moi::getActiveMoies(), null, ['class' => 'form-control select2','id'=>'moi']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="work_type">Work Type</label>
            {!! Form::select('work_type', \App\Models\Requirement::$workType, null, ['class' => 'form-control select2','id'=>'work_type']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="category">Category</label>
            {!! Form::select('category', \App\Models\Category::getActiveCategories(), null, ['class' => 'form-control select2','id'=>'category']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="served">Served</label>
            {!! Form::select('served', \App\Models\Submission::getServedOptions(), null, ['class' => 'form-control select2','id'=>'served']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="status">Status</label>
            {!! Form::select('status', \App\Models\Requirement::$allStatus, null, ['class' => 'form-control select2','id'=>'status']) !!}
        </div>
    </div>
</div>
<button class="btn btn-info float-right" onclick="showRequirementFilterData()">Search</button>
<button class="btn btn-default float-right mr-2" onclick="clearRequirementData()">Clear</button>
