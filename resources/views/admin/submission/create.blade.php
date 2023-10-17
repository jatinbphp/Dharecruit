@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper" style="min-height: 946px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('submission.index')}}">{{$menu}}</a></li>
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
                            <h3 class="card-title">Add New {{$menu}}</h3>
                        </div>
                        {!! Form::open(['url' => route('submission.store'), 'id' => 'submissionsForm', 'class' => 'form-horizontal','files'=>true]) !!}
                            <div class="card-body">
                                <input type="hidden" name="requirement_id" value="{{$requirement['id']}}">
                                @include ('admin.submission.form')
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('submission.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
                                <button class="btn btn-info float-right" type="submit">Add</button>
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
    $("#email").focusout(function(){
        var email = $(this).val();
        $.ajax({
            url : "{{ route('submission.alreadyAddedUserDetail') }}",
            data : {'email' : email, "_token": "{{ csrf_token() }}",},
            type : 'POST',
            dataType : 'json',
            success : function(data){
                var data = Object.values(data)[0];
                $('#submissionsForm *').filter(':input').each(function () {
                    var tagType = $(this).prop("tagName").toLowerCase();
                    var elementId = this.id;
                    if(elementId){
                        if(tagType == 'input'){
                            var type = $("#" + elementId).attr("type");
                            if(type == 'file'){
                                var resumeElement = '<div class="mt-2"><a href="{{asset("storage")}}/'+ data['documents']+'" target="_blank"><img src=" {{url('assets/dist/img/resume.png')}}" height="50"></a></div>';
                                $("#" + elementId).closest('div').append(resumeElement);
                            } else {
                                var id = "#" + elementId;
                                $(id).val(data[elementId]);                                
                            }
                        } else if(tagType == 'select'){
                            $("#" +elementId).select2("val", data[elementId]);
                        }
                    }
                });
            }
        });
    });
</script>
@endsection