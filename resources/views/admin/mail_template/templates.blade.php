@extends('admin.layouts.app')
@section('content')
    @php
        $userType = Auth::user()->role;
    @endphp
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">{{$menu}}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @include ('admin.error')
            <div id="responce" class="alert alert-success" style="display: none">
            </div>
            <div class="callout callout-danger">
                <h4><i class="fa fa-info-circle"></i> Note:</h4>
                <div class="email-note" style="font-size: 17px;">
                    <p class="note-heading text-danger font-weight-bold">Do not modify anything between square brackets [] in the email templates, as they are placeholders for dynamic values and changing them may cause errors in the email content. Always ensure that the placeholders remain intact for proper variable substitution during email generation.</p>

                    <p class="note-suggestion text-primary font-weight-bold">Suggestion: For better clarity and proper design, use the following naming conventions for placeholders:</p>
                    <div class="row">
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[candidate_name]</span> represents the name of the candidate.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[job_id]</span> represents the ID of the job.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[job_title]</span> represents the title of the job.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[interview_date]</span> represents the date of the interview.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[interview_time]</span> represents the time of the interview.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[time_zone]</span> represents the time zone of the interview.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[feedback]</span> represents the feedback of the interview.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[job_type]</span> represents the type of the job.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[job_term]</span> represents the term of the job.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[job_location]</span> represents the location of the job.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[job_duration]</span> represents the duration of the job.</li>
                        </div>
                        <div class="col-md-4">
                            <li class="font-weight-bold"><span class="text-success ">[end_client]</span> represents the client of the interview.</li>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-tabs">
                        <div class="card-header p-0 pt-1">
                            @if(isset($templateData) && $templateData && count($templateData))
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    @php $i = 1; @endphp
                                    @foreach($templateData as $template)
                                        <li class="nav-item" onclick="loadTemplate('{{$template->id}}')">
                                            <a class="nav-link tab-link @if($i == 1) active @endif" data-id="{{$template->id}}" data-action="test" id="custom-tabs-one-{{$template->id}}-tab" data-toggle="pill" href="#custom-tabs-one-{{$template->id}}" role="tab" aria-controls="custom-tabs-one-{{$template->id}}" aria-selected="true">{{$template->template_name}}</a>
                                        </li>
                                        @php $i++; @endphp
                                    @endforeach
                                </ul>
                            @else
                                <p class="pl-3 pt-2">No Template Data Found.</p>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                @php $i = 1; @endphp
                                @foreach($templateData as $template)
                                    <div class="tab-pane fade show @if($i == 1) active @endif" id="custom-tabs-one-{{$template->id}}" role="tabpanel" aria-labelledby="custom-tabs-one-{{$template->id}}-tab">
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('jquery')
    <script type="text/javascript">
        $(document).ready(function() {
            var activeTab = $('.tab-link.active');
            if (activeTab.length > 0) {
                loadTemplate(activeTab.data('id'));
            }
        });

        function loadTemplate(id){
            if(!id){
                return;
            }

            $.ajax({
                url: "{{url('admin/mail_template/')}}/"+id+'/edit',
                type: "GET",
                success: function(responce){
                    $('#custom-tabs-one-'+id).html(responce);
                    $(".description").summernote({
                        height: 250,
                        toolbar: [
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough', 'superscript', 'subscript']],
                            ['fontsize', ['fontsize', 'height']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['table','picture','link','map','minidiag']],
                            ['misc', ['codeview']],
                        ],
                    });
                }
            });
        }

        function updateMailTemplate(id){
            var subject = $('#subject_'+id).val();
            var content = $('#content_'+id).val();
            $.ajax({
                url: '{{ url('/admin/mail_template') }}/'+id,
                type: 'PATCH',
                data: {
                    'subject': subject, 'content': content,_token: '{{csrf_token()}}'
                },
                success: function (data) {
                    if(data == 1){
                        swal('Success', "Template Updated Successfully.", 'success');
                        loadTemplate(id);
                    } else {
                        swal('Error', "Something is wrong.", 'error');
                    }
                },
            });
        }
    </script>
@endsection
