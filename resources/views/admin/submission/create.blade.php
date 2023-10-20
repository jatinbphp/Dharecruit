@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper" style="min-height: 946px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$sub_menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('submission.show',['submission'=>$requirement['id']])}}">{{$sub_menu}}</a></li>
                            <li class="breadcrumb-item active">Add</li>
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
                            <h3 class="card-title">Add New {{$sub_menu}}</h3>
                        </div>
                        {!! Form::open(['url' => route('submission.store'), 'id' => 'submissionsForm', 'class' => 'form-horizontal','files'=>true]) !!}
                            <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            Search By Email Or Id   
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email', 'id' => 'search_email']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Id', 'id' => 'search_id']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <button class="btn btn-info float-left" id="search_fill" type="button">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="requirement_id" value="{{$requirement['id']}}">
                                    @include ('admin.submission.form')
                                </div>
                            <div class="card-footer">
                                <a href="{{ route('submission.show',['submission'=>$requirement['id']]) }}" ><button class="btn btn-default" type="button">Back</button></a>
                                <!-- <button class="btn btn-info float-right" type="submit">Add</button> -->
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('jquery')
<script type="text/javascript">
    $("#search_fill").click(function(){
        var email = $('#search_email').val();
        var id = $('#search_id').val();
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
                                    var resumeElement = '<div id="resumeId" class="mt-2"><a href="{{asset("storage")}}/'+ data['documents']+'" target="_blank"><img src=" {{url('assets/dist/img/resume.png')}}" height="50"></a></div>';
                                    $("#" + elementId).closest('div').append(resumeElement);
                                } else {
                                    var id = "#" + elementId;
                                    $(id).val(data[elementId]);
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
</script>
@endsection
