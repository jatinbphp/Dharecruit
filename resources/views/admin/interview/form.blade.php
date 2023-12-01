{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('hiring_manager') ? ' has-error' : '' }}">
            <label class="control-label" for="hiring_manager">Enter Hiring Manager Name :</label>
            {!! Form::text('hiring_manager', null, ['class' => 'form-control', 'placeholder' => 'Enter Hiring Manager Name', 'id' => 'hiring_manager']) !!}
            @if ($errors->has('hiring_manager'))
                <span class="text-danger">
                    <strong>{{ $errors->first('hiring_manager') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('client') ? ' has-error' : '' }}">
            <label class="control-label" for="client">Enter Client Name :</label>
            {!! Form::text('client', null, ['class' => 'form-control', 'placeholder' => 'Enter Client Name', 'id' => 'client', 'readonly' => true]) !!}
            @if ($errors->has('client'))
                <span class="text-danger">
                    <strong>{{ $errors->first('client') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('recruiter_name') ? ' has-error' : '' }}">
            <label class="control-label" for="recruiter_name">Enter Recruiter Name :<span class="text-red">*</span></label>
            {!! Form::text('recruiter_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Recruiter Name', 'id' => 'recruiter_name', 'readonly' => true]) !!}
            @if ($errors->has('recruiter_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('recruiter_name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('interview_date') ? ' has-error' : '' }}">
            <label class="control-label" for="interview_date">Select Interview Date :<span class="text-red">*</span></label>
            {!! Form::date('interview_date', null, ['class' => 'form-control', 'placeholder' => 'Select Interview Date ', 'id' => 'interview_date']) !!}
            @if ($errors->has('interview_date'))
                <span class="text-danger">
                    <strong>{{ $errors->first('interview_date') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('interview_time') ? ' has-error' : '' }}">
            <label class="control-label" for="interview_time">Select Interview Time :<span class="text-red">*</span></label>
            {!! Form::time('interview_time', null, ['class' => 'form-control', 'placeholder' => 'Select Interview Time ', 'id' => 'interview_time']) !!}
            @if ($errors->has('interview_time'))
                <span class="text-danger">
                    <strong>{{ $errors->first('interview_time') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('candidate_phone_number') ? ' has-error' : '' }}">
            <label class="control-label" for="candidate_phone_number">Enter Candidate Phone :<span class="text-red">*</span></label>
            {!! Form::text('candidate_phone_number', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Phone ', 'id' => 'candidate_phone_number']) !!}
            @if ($errors->has('candidate_phone_number'))
                <span class="text-danger">
                    <strong>{{ $errors->first('candidate_phone_number') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('candidate_email') ? ' has-error' : '' }}">
            <label class="control-label" for="candidate_email">Enter Candidate Email :<span class="text-red">*</span></label>
            {!! Form::text('candidate_email', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Email ', 'id' => 'candidate_email', 'readonly' => true]) !!}
            @if ($errors->has('candidate_email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('candidate_email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('time_zone') ? ' has-error' : '' }}">
            <label class="control-label" for="time_zone">Enter Time Zone :<span class="text-red">*</span></label>
            {!! Form::text('time_zone', null, ['class' => 'form-control', 'placeholder' => 'Enter Time Zone ', 'id' => 'time_zone']) !!}
            @if ($errors->has('time_zone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('time_zone') }}</strong>
                </span>
            @endif
        </div>
    </div>
    @if(isset($interview))
        <div class="col-md-12">
            <div class="form-group{{ $errors->has('feedback') ? ' has-error' : '' }}">
                <label class="control-label" for="feedback">Feedback :</label>
                {!! Form::textarea('feedback', null, ['class'=>'form-control','rows' => 4, 'cols' => 54,]) !!}
                @if ($errors->has('feedback'))         
                <span class="text-danger">
                        <strong>{{ $errors->first('feedback') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    @endif

    <div class="col-md-12">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="control-label" for="status">Status :<span class="text-red">*</span></label>
            {!! Form::select('status', $interviewStatus, null, ['class' => 'form-control select2','id'=>'status']) !!}
            @if ($errors->has('status'))
                <span class="text-danger">
                    <strong>{{ $errors->first('status') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('document') ? ' has-error' : '' }}">
            <label class="control-label" for="document">Document :</label><br>
            {!! Form::file('document[]', ['class' => '', 'id'=> 'document','multiple'=>true]) !!}
            @if ($errors->has('document'))
            <br>
            <span class="text-danger">
                    <strong>{{ $errors->first('document') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row mt-2 pl-3 pr-3">
    @if(isset($interviewDocuments))
        @foreach($interviewDocuments as $id => $document)
            <div class="col-md-2 mt-2" id="document-{{$id}}">
                <div class="text-center">
                    <a href="{{asset('storage/'.$document)}}" target="_blank"><img src="{{url('assets/dist/img/resume.png')}}" height="50"></a>
                    @php
                        $documentNameArray = explode('/',$document);
                        $documentName = isset($documentNameArray[2]) ? $documentNameArray[2] : '';
                    @endphp
                    <br>
                    <label>{{$documentName}}</label>
                    <br>
                    <span data-toggle="tooltip" title="Delete File" data-trigger="hover">
                        <span class="text-danger remove-interview-document" data-id="{{$id}}" ><i class="fa fa-trash"></i></span>
                    </span>
                </div>
            </div>
        @endforeach
    @endif
</div>
@section('jquery')
    <script type="text/javascript">
        $(function () {
            $("#fill_by_job_id").click(function(){
                var jobId = $("#search_by_job_id").val();
                if(!jobId || jobId == 0){
                    swal("Error", "Something is wrong!", "error");
                }
                $.ajax({
                    url : "{{ route('interview.getCandidatesName') }}",
                    data : {'job_id' : jobId , "_token": "{{ csrf_token() }}",},
                    type : 'POST',
                    dataType : 'json',
                    success : function(response){
                        if(response.status == 1){
                            $('#candidateNameDiv').html(response.cnadidateName);
                            $('#candidateModel').modal('show');
                        }
                    }
                });
            });
        });
        
        function loadCandidateData(select){
            var candidateId = $('#candidateSelection').val();
            var jobId       = $('#search_by_job_id').val();

            if(!candidateId || !jobId){
                swal("Error", "Something is wrong!", "error");
                return;
            }

            $('#job_id').val(jobId);
            $('#submission_id').val(candidateId);

            $.ajax({
                url : "{{ route('interview.getCandidateData') }}",
                data : {'candidate_id' : candidateId , "_token": "{{ csrf_token() }}",},
                type : 'POST',
                dataType : 'json',
                success : function(response){
                    if(response.status == 1){
                        $('#candidateModel').modal('hide');
                        var candidateData = response.candidateData;
                        for (data in candidateData) {
                            $("#" + data).val(candidateData[data]);
                        }
                    }
                }
            });
        }
        $(".remove-interview-document").click(function(){
            var id = $(this).attr('data-id');
            swal({
                title: "Are you sure?",
                text: "You want to delete this document?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{url('admin/interview/removeDocument')}}/"+id,
                        type: "POST",
                        data: {_token: '{{csrf_token()}}' },
                        success: function(data){
                            if(data.status == 1){
                                swal("Deleted", "Your data successfully deleted!", "success");
                                $('#document-'+id).remove();
                            } else {
                                swal("Error", "Something is wrong!", "error");
                            }
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });
    </script>
@endsection