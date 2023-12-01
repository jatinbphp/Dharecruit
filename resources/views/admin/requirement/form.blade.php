{!! Form::hidden('redirects_to', URL::previous()) !!}
<div style="border: 2px solid black; border-radius: 25px">
    <div class="row mt-3 pl-3 pr-3">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('pv_company_name') ? ' has-error' : '' }}">
                <label class="control-label" for="pv_company_name">PV Company Name :<span class="text-red">*</span></label>
                {!! Form::text('pv_company_name', null, ['class' => 'form-control','placeholder' => 'Enter PV Company Name', 'id'=> 'pv_company_name', 'readonly' => (isset($requirement)) ? true : false ]) !!}
                @if ($errors->has('pv_company_name'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('pv_company_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('poc_name') ? ' has-error' : '' }}">
                <label class="control-label" for="poc_name">POC Name :<span class="text-red">*</span></label>
                {!! Form::text('poc_name', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Name', 'id' => 'poc_name', 'readonly' => (isset($requirement)) ? true : false]) !!}
                @if ($errors->has('poc_name'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('poc_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('poc_email') ? ' has-error' : '' }}">
                <label class="control-label" for="poc_email">POC Email :<span class="text-red">*</span></label>
                {!! Form::text('poc_email', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Email', 'id' => 'poc_email', 'readonly' => (isset($requirement)) ? true : false]) !!}
                @if ($errors->has('poc_email'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('poc_email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('poc_phone_number') ? ' has-error' : '' }}">
                <label class="control-label" for="poc_phone_number">POC Phone Number :<span class="text-red">*</span></label>
                {!! Form::text('poc_phone_number', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Phone Number', 'id' => 'poc_phone_number', 'readonly' => (isset($requirement)) ? true : false]) !!}
                @if ($errors->has('poc_phone_number'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('poc_phone_number') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('poc_location') ? ' has-error' : '' }}">
                <label class="control-label" for="poc_location">POC Location :<span class="text-red"></span></label>
                {!! Form::text('poc_location', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Location', 'id' => 'poc_location']) !!}
                @if ($errors->has('poc_location'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('poc_location') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('pv_company_location') ? ' has-error' : '' }}">
                <label class="control-label" for="pv_company_location">PV Company Location :<span class="text-red"></span></label>
                {!! Form::text('pv_company_location', null, ['class' => 'form-control', 'placeholder' => 'Enter PV Company Location', 'id' => 'pv_company_location']) !!}
                @if ($errors->has('pv_company_location'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('pv_company_location') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-3 pl-3 pr-3">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('client_name') ? ' has-error' : '' }}">
                <label class="control-label" for="client_name">Client Name :</label> (optional)
                {!! Form::text('client_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Client Name', 'id' => 'client_name']) !!}
                @if ($errors->has('client_name'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('client_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('display_client') ? ' has-error' : '' }}">
                <label class="control-label" for="display_client">Display Client :<span class="text-red"></span></label>
                <br>
                <div class="icheck-primary d-inline">
                    {!! Form::checkbox('display_client', null, null, ['id' => 'display_client']) !!}
                    <label for="display_client"></label>
                </div>

                @if ($errors->has('display_client'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('display_client') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="modal fade" id="pocModal" tabindex="-1" role="dialog" aria-labelledby="pocModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pocModalLabel">Select POC Name</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" id="pocNameDiv"> </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @if(!isset($requirement))
<div class="text-right">
    <button class="btn btn-info" id="requirementSave">Save</button>
</div>
@endif --}}

{{-- <div id="pvDiv"> --}}
<div class="mt-3" style="border: 2px solid black; border-radius: 25px">
    <div class="row mt-3 pl-3 pr-3">
        <div class="col-md-4">
            <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
                <label class="control-label" for="job_title">Job Title :<span class="text-red">*</span></label>
                {!! Form::text('job_title', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Title', 'id' => 'job_title']) !!}
                @if ($errors->has('job_title'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('job_title') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('no_of_position') ? ' has-error' : '' }}">
                <label class="control-label" for="no_of_position">No # Position :<span class="text-red">*</span></label>
                {!! Form::text('no_of_position', null, ['class' => 'form-control', 'placeholder' => 'Enter No # Position', 'id' => 'no_of_position']) !!}
                @if ($errors->has('no_of_position'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('no_of_position') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('experience') ? ' has-error' : '' }}">
                <label class="control-label" for="experience">Experience :<span class="text-red">*</span></label>
                {!! Form::text('experience', null, ['class' => 'form-control', 'placeholder' => 'Enter Experience', 'id' => 'experience']) !!}
                @if ($errors->has('experience'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('experience') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-4">
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

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('work_type') ? ' has-error' : '' }}">
                <label class="control-label" for="work_type">Onsite/Hybrid/Remote :<span class="text-red"></span></label>
                {!! Form::text('work_type', null, ['class' => 'form-control', 'placeholder' => 'Onsite/Hybrid/Remote', 'id' => 'work_type']) !!}
                @if ($errors->has('work_type'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('work_type') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('duration') ? ' has-error' : '' }}">
                <label class="control-label" for="duration">Duration :<span class="text-red"></span></label>
                {!! Form::text('duration', null, ['class' => 'form-control', 'placeholder' => 'Duration', 'id' => 'duration']) !!}
                @if ($errors->has('duration'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('duration') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-4">
            <div class="form-group{{ $errors->has('visa') ? ' has-error' : '' }}">
                <label class="control-label" for="visa">Visa :@if (empty($customers))<span class="text-red">*</span>@endif</label>
                {!! Form::select('visa[]', $visa, !empty($selectedVisa) ? $selectedVisa : null, ['multiple' => true, 'class' => 'form-control select2','id'=>'visa', 'data-placeholder' => 'Please Select Visa']) !!}
                @if ($errors->has('visa'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('visa') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <!-- <div class="col-md-3">
            <div class="form-group{{ $errors->has('client') ? ' has-error' : '' }}">
                <label class="control-label" for="client">Client :<span class="text-red">*</span></label>
                {!! Form::text('client', null, ['class' => 'form-control', 'placeholder' => 'Enter Client', 'id' => 'client']) !!}
                @if ($errors->has('client'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('client') }}</strong>
                    </span>
                @endif
            </div>
        </div> -->

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('vendor_rate') ? ' has-error' : '' }}">
                <label class="control-label" for="vendor_rate">Vendor Rate :<span class="text-red">*</span></label>
                {!! Form::text('vendor_rate', null, ['class' => 'form-control', 'placeholder' => 'Enter Vendor Rate', 'id' => 'vendor_rate']) !!}
                @if ($errors->has('vendor_rate'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('vendor_rate') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('my_rate') ? ' has-error' : '' }}">
                <label class="control-label" for="my_rate">My Rate :<span class="text-red">*</span></label>
                {!! Form::text('my_rate', null, ['class' => 'form-control', 'placeholder' => 'Enter My Rate', 'id' => 'my_rate']) !!}
                @if ($errors->has('my_rate'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('my_rate') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('priority') ? ' has-error' : '' }}">
                <label class="control-label" for="priority">Priority</label>
                {!! Form::select('priority', \App\Models\Requirement::$priority, null, ['class' => 'form-control select2','id'=>'priority']) !!}
                @if ($errors->has('priority'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('priority') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('term') ? ' has-error' : '' }}">
                <label class="control-label" for="term">Term <span class="text-red">*</span></label>
                {!! Form::select('term', \App\Models\Requirement::$term, null, ['class' => 'form-control select2','id'=>'term']) !!}
                @if ($errors->has('term'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('term') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-12" id="priorityReason" style="display: none;">
            <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                <label class="control-label" for="reason">Reason :<span class="text-red"></span></label>
                {!! Form::text('reason', null, ['class' => 'form-control', 'placeholder' => 'Enter Reason', 'id' => 'reason']) !!}
                @if ($errors->has('reason'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('reason') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-4">
            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                <label class="control-label" for="category">Category <span class="text-red">*</span></label>
                {!! Form::select('category', $category, null, ['class' => 'form-control select2','id'=>'category']) !!}
                @if ($errors->has('category'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('category') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group{{ $errors->has('moi') ? ' has-error' : '' }}">
                <label class="control-label" for="moi">MOI <span class="text-red">*</span></label>
                {!! Form::select('moi[]', $moi, !empty($selectedMoi) ? $selectedMoi : null, ['multiple' => true, 'class' => 'form-control select2','id'=>'moi', 'data-placeholder' => 'Please Select MOI']) !!}
                @if ($errors->has('moi'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('moi') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                <label class="control-label" for="notes">Special Notes :<span class="text-red"></span></label>
                {!! Form::text('notes', null, ['class' => 'form-control', 'placeholder' => 'Enter Notes', 'id' => 'notes']) !!}
                @if ($errors->has('notes'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('notes') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row pl-3 pr-3">
        <div class="col-md-12">
            <div class="form-group{{ $errors->has('job_keyword') ? ' has-error' : '' }}">
                <label class="control-label" for="job_keyword">Job Keyword :<span class="text-red">*</span></label>
                {!! Form::textarea('job_keyword', null, ['class' => 'form-control description', 'rows'=>4, 'placeholder' => 'Enter Job Keyword', 'id' => 'job_keyword']) !!}
                @if ($errors->has('job_keyword'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('job_keyword') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label class="control-label" for="notes">Job Description :<span class="text-red">*</span></label>
                {!! Form::textarea('description', null, ['class' => 'form-control description', 'rows'=>4, 'placeholder' => 'Enter Job Description', 'id' => 'description']) !!}
                @if ($errors->has('description'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label" for="recruiter">Assign To Recruter :</label>
                {!! Form::select('recruiter[]', $recruiter, !empty($selectedRecruiter) ? $selectedRecruiter : null, ['multiple' => true ,'class' => 'form-control select2','id'=>'recruiter', 'data-placeholder' => 'Please Select Recruter']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('document') ? ' has-error' : '' }}">
                <label class="control-label" for="document">Document :</label>
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

    <div class="row mt-2 pl-3 pr-3 mb-3">
        @if(isset($requirementDocuments))
            @foreach($requirementDocuments as $id => $document)
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
                            <span class="text-danger remove-document" data-id="{{$id}}" ><i class="fa fa-trash"></i></span>
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    {{-- <hr>
    <div class="text-right mb-3 pr-3">
        <button class="btn btn-info" id="pvSave">Save</button>
    </div> --}}
</div>

@section('jquery')
    <script type="text/javascript">
        $('#priority').on('change', function(){
            if($(this).val() == 'high'){
                $('#priorityReason').show();
            }else{
                $('#priorityReason').hide();
            }
        });

        $("#pv_company_name").focusout(function(){
            @if(!isset($requirement))
                var pv_company_name = $(this).val();
                $.ajax({
                    url : "{{ route('get_pocName') }}",
                    data : {'pv_company_name' : pv_company_name, "_token": "{{ csrf_token() }}",},
                    type : 'POST',
                    dataType : 'json',
                    success : function(data){
                        if(data.status == 1){
                            $('#pocNameDiv').html(data.pocName);
                            $('#pocModal').modal('show');
                            $('.select2').select2();
                        }
                    }
                });
            @endif
        });

        function checkData() {
            var poc_name = $("#pocSelection option:selected").val();
            var pv_company_name = $("#pv_company_name").val();

            var updateElementIds = [
                'pv_company_name',
                'poc_name',
                'poc_email',
                'poc_phone_number',
                'poc_location',
                'pv_company_location',
                'client_name',
            ];

            var mapElementWithData = {
                'pv_company_name' : 'name',
                'poc_name' : 'poc_name',
                'poc_email' : 'email',
                'poc_phone_number' : 'phone',
                'poc_location':'poc_location',
                'pv_company_location':'pv_company_location',
                'client_name':'client_name',
            };

            if(poc_name == 0){
                for (id of updateElementIds) {
                    if(id == 'pv_company_name'){
                        continue;
                    }
                    $("#"+id).val('');
                }
                $('#pocModal').modal('hide');
                return;
            }

            $.ajax({
                url : "{{ route('get_pvDetails') }}",
                data : {'poc_name' : poc_name, 'pv_company_name':pv_company_name, "_token": "{{ csrf_token() }}",},
                type : 'POST',
                dataType : 'json',
                success : function(data){
                    if(data.status == 1){
                        $('#requirementsForm *').filter(':input').each(function () {
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
                    $('#pocModal').modal('hide');
                }
            });
        }
        $(".remove-document").click(function(){
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
                        url: "{{url('admin/requirement/removeDocument')}}/"+id,
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

        var availablePvCompanyName = <?php echo json_encode($pvCompanyName);?>;
 
        $(document).on('focusout keydown', '#pv_company_name', function (index, value) {
            $("#pv_company_name").autocomplete({
                source: availablePvCompanyName,
                minLength: 4 
            });
        });
    </script>
@endsection
