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
                {!! Form::text('fromDate', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'fromDate']) !!}
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
                {!! Form::text('toDate', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'toDate']) !!}
            </div>
        </div>
    </div>
    @if(in_array($menu,['My Requirements', 'Manage Submission']))
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="job_title">Job Title</label>
                {!! Form::text('job_title', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Title', 'id' => 'job_title']) !!}
            </div>
        </div>
    @endif
    @if(in_array(Auth::user()->role, ['admin', 'recruiter']))
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="bdm">BDM</label>
                {!! Form::select('bdm', \App\Models\Admin::getActiveBDM(true), null, ['class' => 'form-control select2','id'=>'bdm']) !!}
            </div>
        </div>
    @endif
    @if(in_array(Auth::user()->role, ['admin', 'bdm']) && $type != 3)
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="recruiter">Recruiter</label>
                {!! Form::select('recruiter', \App\Models\Admin::getActiveRecruiter(true), null, ['class' => 'form-control select2','id'=>'recruiter']) !!}
            </div>
        </div>
    @endif
    @if(Auth::user()->role == 'admin')
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="candidate_name">Candidate Name</label>
                {!! Form::text('candidate_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Name', 'id' => 'candidate_name']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="candidate_id">Candidate Id</label>
                {!! Form::text('candidate_id', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Id', 'id' => 'candidate_id']) !!}
            </div>
        </div>
    @endif
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="job_id">Job Id</label>
            {!! Form::text('job_id', null, ['autocomplete' => 'off', 'class' => 'form-control float-right', 'placeholder' => 'Enter Job Id', 'id' => 'job_id']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="client">Client</label>
            {!! Form::text('client', null, ['class' => 'form-control', 'placeholder' => 'Enter Client', 'id' => 'client']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="job_location">Job Location</label>
            {!! Form::text('job_location', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Location', 'id' => 'job_location']) !!}
        </div>
    </div>
    @if($menu == 'My Requirements')
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="moi">MOI</label>
                {!! Form::select('moi', \App\Models\Moi::getActiveMoies(), null, ['class' => 'form-control select2','id'=>'moi']) !!}
            </div>
        </div>
    @endif
    @if($menu == 'My Requirements')
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="work_type">Work Type</label>
                {!! Form::select('work_type', \App\Models\Requirement::$workType, null, ['class' => 'form-control select2','id'=>'work_type']) !!}
            </div>
        </div>
    @endif
    @if($menu == 'My Requirements')
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="category">Category</label>
                {!! Form::select('category', \App\Models\Category::getActiveCategories(), null, ['class' => 'form-control select2','id'=>'category']) !!}
            </div>
        </div>
    @endif
    @if($menu == 'My Requirements')
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="served">Served Job Status</label>
                {!! Form::select('served', \App\Models\Submission::getServedOptions(), null, ['class' => 'form-control select2','id'=>'served']) !!}
            </div>
        </div>
    @endif
    @if($menu == 'My Requirements')
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="filter_status">Status</label>
            {!! Form::select('status', \App\Models\Requirement::$allStatus, null, ['class' => 'form-control select2','id'=>'filter_status']) !!}
        </div>
    </div>
    @endif
    @if(in_array(Auth::user()->role, ['admin', 'recruiter']) && $type != 3)
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="filter_employer_name">Employer Name</label>
                {!! Form::text('filter_employer_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Employer Name', 'id' => 'filter_employer_name']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="filter_employee_name">Employee Name</label>
                {!! Form::text('filter_employee_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Name', 'id' => 'filter_employee_name']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="filter_employee_phone_number">Employee Phone Number</label>
                {!! Form::text('filter_employee_phone_number', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Phone Number', 'id' => 'filter_employee_phone_number']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="filter_employee_email">Employee Email</label>
                {!! Form::text('filter_employee_email', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Email', 'id' => 'filter_employee_email']) !!}
            </div>
        </div>
    @endif
    @if(in_array(Auth::user()->role, ['admin', 'bdm']) && $type != 3)
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="pv_email">Pv Email</label>
                {!! Form::text('pv_email', null, ['class' => 'form-control', 'placeholder' => 'Enter Pv Email', 'id' => 'pv_email']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="pv_company">Pv Company</label>
                {!! Form::text('pv_company', null, ['class' => 'form-control', 'placeholder' => 'Enter Pv Company', 'id' => 'pv_company']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="pv_name">Pv Name</label>
                {!! Form::text('pv_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Pv Name', 'id' => 'pv_name']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="pv_phone">Pv Phone Number</label>
                {!! Form::text('pv_phone', null, ['class' => 'form-control', 'placeholder' => 'Enter Pv Phone Number', 'id' => 'pv_phone']) !!}
            </div>
        </div>
    @endif
    @if(Auth::user()->role == 'bdm' && $menu == 'My Requirements')
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="requirement_type">Requirement Type</label>
                {!! Form::select('requirement_type', \App\Models\Requirement::getRequirementTypes(), null, ['class' => 'form-control select2','id'=>'requirement_type']) !!}
            </div>
        </div>
    @endif
    @if(in_array($menu,['My Requirements', 'Manage Submission', 'Team Submission']))
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="bdm_feedback">BDM FeedBack</label>
                {!! Form::select('bdm_feedback[]', \App\Models\Submission::getBDMFilterOptions(), null, ['class' => 'form-control select2','id'=>'bdm_feedback', 'multiple' => true, 'data-placeholder' => 'Please Select BDM Feedback']) !!}
            </div>
        </div>
    @endif
    @if(in_array($menu,['My Requirements', 'Manage Submission', 'Team Submission']))
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="pv_feedback">PV Feedback</label>
                {!! Form::select('pv_feedback[]', \App\Models\Submission::$pvStatus, null, ['class' => 'form-control select2','id'=>'pv_feedback', 'multiple' => true, 'data-placeholder' => 'Please Select PV Feedback']) !!}
            </div>
        </div>
    @endif
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="client_feedback">Client Feedback</label>
            {!! Form::select('client_feedback[]', \App\Models\Interview::$interviewStatusFilterOptions, null, ['class' => 'form-control select2','id'=>'client_feedback', 'multiple' => true, 'data-placeholder' => 'Please Select Client Feedback']) !!}
        </div>
    </div>
    @if($type == 3)
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="client_feedback">Select From Team</label>
                {!! Form::select('team_users[]', \App\Models\TeamMember::getTeamUsers(), null, ['class' => 'form-control select2','id'=>'team_users', 'multiple' => true, 'data-placeholder' => 'Please Select From Team']) !!}
            </div>
        </div>
    @endif
</div>
<button class="btn btn-info float-right" onclick="showRequirementFilterData()">Search</button>
<button class="btn btn-default float-right mr-2" onclick="clearRequirementData()">Clear</button>
