{!! Form::hidden('redirects_to', URL::previous()) !!}
<div style="border: 2px solid black; border-radius: 25px">
    <div class="row mt-3 mb-3 pl-3 pr-3">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label class="control-label" for="name">Name :<span class="text-red">*</span></label>
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Name', 'id' => 'name', 'readonly' => (isset($submission)) ? true : false]) !!}
                @if ($errors->has('name'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label class="control-label" for="email">Email :<span class="text-red">*</span></label>
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email', 'id' => 'email', 'readonly' => (isset($submission)) ? true : false]) !!}
                @if ($errors->has('email'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                <label class="control-label" for="location">Location :<span class="text-red">*</span></label>
                {!! Form::text('location', null, ['class' => 'form-control', 'placeholder' => 'Enter Location', 'id' => 'location']) !!}
                @if ($errors->has('location'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('location') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                <label class="control-label" for="phone">Phone :<span class="text-red">*</span></label>
                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Enter Phone', 'id' => 'phone']) !!}
                @if ($errors->has('phone'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('employer_detail') ? ' has-error' : '' }}">
                <label class="control-label" for="employer_detail">Employer Detail :<span class="text-red">*</span></label>
                {!! Form::select('employer_detail', \App\Models\Submission::$empDetails, null, ['class' => 'form-control select2','id'=>'employer_detail']) !!}
                @if ($errors->has('employer_detail'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('employer_detail') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('work_authorization') ? ' has-error' : '' }}">
                <label class="control-label" for="work_authorization">Work Authorization :<span class="text-red">*</span></label>
                {!! Form::text('work_authorization', null, ['class' => 'form-control', 'placeholder' => 'Enter Work Authorization', 'id' => 'work_authorization']) !!}
                @if ($errors->has('work_authorization'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('work_authorization') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('last_4_ssn') ? ' has-error' : '' }}">
                <label class="control-label" for="last_4_ssn">Last 4 SSN :<span class="text-red">*</span></label>
                {!! Form::text('last_4_ssn', null, ['class' => 'form-control', 'placeholder' => 'Enter Last 4 SSN', 'id' => 'last_4_ssn']) !!}
                @if ($errors->has('last_4_ssn'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('last_4_ssn') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('education_details') ? ' has-error' : '' }}">
                <label class="control-label" for="education_details">Education Details :<span class="text-red">*</span></label>
                {!! Form::text('education_details', null, ['class' => 'form-control', 'placeholder' => 'Enter Education Details', 'id' => 'education_details']) !!}
                @if ($errors->has('education_details'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('education_details') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('resume_experience') ? ' has-error' : '' }}">
                <label class="control-label" for="resume_experience">Resume Experience :<span class="text-red">*</span></label>
                {!! Form::text('resume_experience', null, ['class' => 'form-control', 'placeholder' => 'Enter Resume Experience', 'id' => 'resume_experience']) !!}
                @if ($errors->has('resume_experience'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('resume_experience') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('linkedin_id') ? ' has-error' : '' }}">
                <label class="control-label" for="linkedin_id">Linkedin ID :<span class="text-red">*</span></label>
                {!! Form::text('linkedin_id', null, ['class' => 'form-control', 'placeholder' => 'Enter Linkedin ID', 'id' => 'linkedin_id']) !!}
                @if ($errors->has('linkedin_id'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('linkedin_id') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('relocation') ? ' has-error' : '' }}">
                <label class="control-label" for="relocation">Relocation :<span class="text-red">*</span></label>
                {!! Form::text('relocation', null, ['class' => 'form-control', 'placeholder' => 'Enter Relocation', 'id' => 'relocation']) !!}
                @if ($errors->has('relocation'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('relocation') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        {{-- <div class="col-md-6">
            <div class="form-group{{ $errors->has('vendor_rate') ? ' has-error' : '' }}">
                <label class="control-label" for="vendor_rate">Vendor Rate :<span class="text-red">*</span></label>
                {!! Form::text('vendor_rate', null, ['class' => 'form-control', 'placeholder' => 'Enter Vendor Rate', 'id' => 'vendor_rate']) !!}
                @if ($errors->has('vendor_rate'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('vendor_rate') }}</strong>
                    </span>
                @endif
            </div>
        </div> --}}

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('recruiter_rate') ? ' has-error' : '' }}">
                <label class="control-label" for="recruiter_rate">Recruiter Rate :<span class="text-red">*</span></label>
                {!! Form::text('recruiter_rate', null, ['class' => 'form-control', 'placeholder' => 'Enter Recruiter Rate', 'id' => 'recruiter_rate']) !!}
                @if ($errors->has('recruiter_rate'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('recruiter_rate') }}</strong>
                    </span>
                @endif
            </div>
        </div>


        <div class="col-md-12">
            <div class="form-group{{ $errors->has('resume') ? ' has-error' : '' }}">
                <label class="control-label" for="resume">Resume :<span class="text-red">*</span></label>
                {!! Form::file('resume', ['class' => '', 'id'=> 'resume','accept'=>'.xlsx,.xls,.doc,.docx,.ppt,.pptx,.txt,.pdf']) !!}
                {!! Form::hidden('existResume', null, ['id' => 'existResume']) !!}
                @if ($errors->has('resume'))
                <br>
                <span class="text-danger">
                        <strong>{{ $errors->first('resume') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        @if(isset($submission['documents']))
            <div class="col-md-2 mt-2">
                <div class="text-center">
                    <a href="{{asset('storage/'.$submission['documents'])}}" target="_blank"><img src="{{url('assets/dist/img/resume.png')}}" height="50"></a>
                    @php
                        $documentNameArray = explode('/',$submission['documents']);
                        $documentName = isset($documentNameArray[2]) ? $documentNameArray[2] : '';
                    @endphp
                    <label>{{$documentName}}</label>
                </div>
            </div>
        @endif
    </div>
</div>
{{-- @if(!isset($submission))
    <div class="text-right">
        <button class="btn btn-info" id="empSave">Save</button>
    </div>
@endif --}}

{{-- <div id="empDiv" @if(!isset($submission)) style="display: none;" @endif>
    <hr> --}}
<div class="search-employee-email" style='{{(isset($errors) && count($errors) != 0 ) ? "display:none" : ""}}'>
    <div class="mt-3" style="border: 2px solid black; border-radius: 25px">
    <div class="row mt-3 pl-3 pr-3">
        <div class="col-md-6  mt-3">
                <div class="form-group">
                    <label class="control-label" for="search_emp_email">Employee Email : (Search Database)</label>
                    {!! Form::text('search_emp_email', null, ['class' => 'form-control','placeholder' => 'Enter Employee Email :', 'id'=> 'search_emp_email']) !!}
                </div>
            </div>
            <div class="col-md-6 float-left">
                <div class="form-group mt-5">
                    <button class="btn btn-info" id='search_by_emp_email' type="button">Search</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="employee-detail" style='{{ ((!isset($submission) && isset($errors) && count($errors) == 0) || (isset($errors) && count($errors)) == 0) ? "display:none" : ""}}'>
    <div class="mt-3" style="border: 2px solid black; border-radius: 25px">
        <div class="row mt-3 pl-3 pr-3">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('employer_name') ? ' has-error' : '' }}">
                    <label class="control-label" for="employer_name">CompanyName- 3rd Party Employer :<span class="text-red">*</span></label>
                    {!! Form::text('employer_name', null, ['class' => 'form-control','placeholder' => 'Enter CompanyName- 3rd Party Employer', 'id'=>'employer_name', 'readonly' => (isset($submission)) ? true : false]) !!}
                    @if ($errors->has('employer_name'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('employer_name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-6" @if(Auth::user()->role == 'bdm') style="display: none;" @endif>
                <div class="form-group{{ $errors->has('employee_name') ? ' has-error' : '' }}">
                    <label class="control-label" for="employee_name">Recruiter’s Name - 3rd Party Employee :<span class="text-red">*</span></label>
                    {!! Form::text('employee_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Recruiter’s Name - 3rd Party Employee', 'id' => 'employee_name', 'readonly' => (isset($submission)) ? true : false]) !!}
                    <span class="text-danger"><strong id="employee_name_message"></strong></span>
                    @if ($errors->has('employee_name'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('employee_name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt-3 pl-3 pr-3" @if(Auth::user()->role == 'bdm') style="display: none;" @endif>
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('employee_email') ? ' has-error' : '' }}">
                    <label class="control-label" for="employee_email">Employee Email :<span class="text-red">*</span></label>
                    {!! Form::text('employee_email', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Email', 'id' => 'employee_email', 'readonly' => (isset($submission)) ? true : false]) !!}
                    @if ($errors->has('employee_email'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('employee_email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('employee_phone') ? ' has-error' : '' }}">
                    <label class="control-label" for="employee_phone">Employee Phone Number :<span class="text-red">*</span></label>
                    {!! Form::text('employee_phone', null, ['class' => 'form-control', 'placeholder' => 'Employee Phone Number', 'id' => 'employee_phone', 'readonly' => (isset($submission)) ? true : false]) !!}
                    @if ($errors->has('employee_phone'))
                        <span class="text-danger">
                            <strong>{{ $errors->first('employee_phone') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- <div class="text-right mt-3 mb-3 pr-3">
            <button class="btn btn-info" id="empSave">Save</button>
        </div> --}}
    </div>
</div>
<div class="modal fade" id="empModel" tabindex="-1" role="dialog" aria-labelledby="empModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="empModalLabel">Select Employee Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="empNameDiv"> </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('jquery')
    <script type="text/javascript">
    var settings = <?php echo json_encode($settings);?>;
        $("#search_fill").click(function(){
            var email = $('#search_email').val();
            var id = $('#search_id').val();
            var notUpdateFileds = [
                'employee_name',
                'employee_email',
                'employee_phone',
                'requirement_id',
                'recruiter_rate'
            ];
            $.ajax({
                url : "{{ route('submission.alreadyAddedUserDetail') }}",
                data : {'email' : email, 'id' : id , "_token": "{{ csrf_token() }}",},
                type : 'POST',
                dataType : 'json',
                success : function(response){
                    if(response.status == 1){
                        var data = response.responceData;
                        $('#submissionsForm *').filter(':input').each(function () {
                            var tagType = $(this).prop("tagName").toLowerCase();
                            var elementId = this.id;
                            if(elementId){
                                if(tagType == 'input'){
                                    var type = $("#" + elementId).attr("type");
                                    if(type == 'file'){
                                        $('#resumeId').remove();
                                        var resumeElement = '<div id="resumeId" class="col-md-2 mt-4 "><div id="documentContent" class="text-center"><a href="{{asset("storage")}}/'+ data['documents']+'" target="_blank"><img src=" {{url('assets/dist/img/resume.png')}}" height="50"></a></div></div>';
                                        var documentNameArray = data['documents'].split('/');
                                        var documentName = '2' in documentNameArray ? documentNameArray[2] : '';
                                        var label = "<label>"+documentName+"</label>";
                                        $("#" + elementId).closest('div').append(resumeElement);
                                        $("#documentContent").append(label);
                                    } else {
                                        if(!notUpdateFileds.includes(elementId)){
                                            if(elementId == 'employer_name'){
                                                if(settings.hasOwnProperty('is_fill_employer_name') && settings['is_fill_employer_name'] == 'yes'){
                                                    var id = "#" + elementId;
                                                    $(id).val(data[elementId]);
                                                }
                                            } else {
                                                var id = "#" + elementId;
                                                $(id).val(data[elementId]);
                                            }
                                        }
                                    }
                                } else if(tagType == 'select'){
                                    $("#" +elementId).select2("val", data[elementId]);
                                }
                            }
                            $("#existResume").val(data['documents']);
                        });
                    }
                }
            });
        });
        $("#employer_name").focusout(function(){
            return;
            var employer_name = $(this).val();
            $.ajax({
                url : "{{ route('emp_name') }}",
                data : {'employer_name' : employer_name, "_token": "{{ csrf_token() }}",},
                type : 'POST',
                dataType : 'json',
                success : function(data){
                    if(data.status == 1){
                        $('#empNameDiv').html(data.empName);
                        $('#empModel').modal('show');
                        // $('.select2').select2();
                    }
                }
            });
        });

        function checkData() {
            var employee_name = $("#empSelection option:selected").val();
            var employer_name = $("#employer_name").val();
            var updateElementIds = [
                'employer_name',
                'employee_name',
                'employee_email',
                'employee_phone',
            ];

            var mapElementWithData = {
                'employer_name' : 'name',
                'employee_name' : 'employee_name',
                'employee_email' : 'email',
                'employee_phone' : 'phone',
            };

            if(employee_name == 0){
                for (id of updateElementIds) {
                    if(id == 'employer_name'){
                        continue;
                    }
                    $("#"+id).val('');
                }
                $('#empModel').modal('hide');
                return;
            }

            $.ajax({
                url : "{{ route('get_emp_details') }}",
                data : {'employer_name' : employer_name, 'employee_name':employee_name, "_token": "{{ csrf_token() }}",},
                type : 'POST',
                dataType : 'json',
                success : function(data){
                    if(data.status == 1){
                        $('#submissionsForm *').filter(':input').each(function () {
                            var tagType = $(this).prop("tagName").toLowerCase();
                            var elementId = this.id;
                            if(elementId){
                                if(tagType == 'input'){
                                    if(updateElementIds.includes(elementId))
                                    var id = "#" + elementId;
                                    $(id).val(data.requs[mapElementWithData[elementId]]);
                                }
                            }
                        });
                    }
                    $('#empModel').modal('hide');
                }
            });
        }

        $('.add-submission').click(function(){
            var requirementId = $('#requirement_id').val();
            var candidateEmail = $('#email').val();
            if(!requirementId || !candidateEmail){
                swal("Error", "Please Fill Data!", "error");
            }

            $.ajax({
                url: "{{url('admin/submission/checkPvCompany')}}",
                type: "POST",
                data: {'requirement_id':requirementId, 'email':candidateEmail, _token: '{{csrf_token()}}' },
                success: function(data){
                    if(data.status == 0){
                        swal("Error", "Something is wrong!", "error");
                        return;
                    }
                    if(data.isSamePvCandidate == 1){
                        swal({
                            title: "Are you sure?",
                            text: "This candidate has already been submitted, you would like to submit?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: '#DD6B55',
                            confirmButtonText: 'Yes, Add',
                            cancelButtonText: "No, cancel",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('#submissionsForm').submit();
                            } else {
                                swal("Cancelled", "Your data safe!", "error");
                            }
                        });
                    } else {
                        $('#submissionsForm').submit();
                    }
                }
            });
        });

        $('#search_by_emp_email').click(function(){
            var empEmail = $('#search_emp_email').val().trim();
            if(!empEmail){
                swal("Error", "Please Enter POC Email.", "error");
                return;
            }
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

            if(!emailPattern.test(empEmail.trim())){
                swal("Error", "Please Valid Email Address.", "error");
                return;
            }

            $.ajax({
                url: "{{route('submission.checkEmpData')}}",
                type: "POST",
                data: {'emp_email' : empEmail,_token: '{{csrf_token()}}' },
                success: function(data){
                    if(data.status == 1){
                        if(data.is_current_user_email == 1){
                            fillEmpData(data);
                        } else if(data.emp_registered == 1){
                            swal({
                                title: "Are you sure?",
                                text: "Click Yes to Add and Register.",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: '#138496',
                                confirmButtonText: 'Yes, Post Submission',
                                cancelButtonText: "No, cancel",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    swal.close();
                                    fillEmpData(data);
                                } else {
                                    swal.close();
                                }
                            });
                        } else if(data.same_employer == 1) {
                            $('#employee_email').attr("readonly",true).val(empEmail);
                            $('#employer_name').attr("readonly",true).val(data.employer_name);
                            $('.employee-detail').show();
                            $('.search-employee-email').hide();
                        } else if(data.new_emp_email == 1){
                            $('#employee_email').attr("readonly",true).val(empEmail);
                            var domain = (empEmail.match(/@(.+?)\./) || [])[1] || "";
                            $('#employer_name').attr("readonly",true).val(domain);
                            $('.employee-detail').show();
                            $('.search-employee-email').hide();
                        }
                    } else {
                        swal("Error", "Something is wrong!", "error");
                    }
                }
            });
        });

        function fillEmpData(data) {
            var submissionData = data.empdata;
            var textBox = '';
            for(var key in data.linking_data) {
                if(key == 'linkEmployeeEmail'){
                    textBox = document.getElementById('employee_email');
                }else if(key == 'linkEmployeePhoneNumber'){
                    textBox = document.getElementById('employee_phone');
                }
                if(textBox){
                    textBox.insertAdjacentHTML('afterend', data.linking_data[key]);
                }
            }

            $('#employer_name').attr("readonly",true).val(submissionData.name);
            $('#employee_name').attr("readonly",true).val(submissionData.employee_name);
            $('#employee_email').attr("readonly",true).val(submissionData.email);
            $('#employee_phone').attr("readonly",true).val(submissionData.phone);
            $('.employee-detail').show();
            $('.search-employee-email').hide();
        }

    $('#employee_name').on('keyup', function () {
        if ($(this).prop('readonly')) {
            return;
        }
        var employeeName = $(this).val();
        var employerName = $('#employer_name').val();
        console.log('{{ route('submission.checkEmp') }}');
        $.ajax({
            type: 'POST',
            url: '{{ route('submission.checkEmp') }}',
            data: { employee_name: employeeName, employer_name : employerName, "_token": "{{ csrf_token() }}"},
            success: function (response) {
                if(response.status == 1){
                    $('#employee_name_message').text('Company Name Already Added With Recruiter ('+employerName+' )');
                    $('#submission_add').prop('disabled', true);
                } else {
                    $('#employee_name_message').text('');
                    $('#submission_add').prop('disabled', false);
                }
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    });
    </script>
@endsection
