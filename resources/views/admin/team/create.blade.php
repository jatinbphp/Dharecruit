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
                            <li class="breadcrumb-item"><a href="{{route('category.index')}}">{{$menu}}</a></li>
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
                            <h3 class="card-title">Add {{$menu}}</h3>
                        </div>
                        {!! Form::open(['url' => route('manage_team.store'), 'id' => 'manageTeamForm', 'class' => 'form-horizontal','files'=>true]) !!}
                        <div class="card-body">
                            @include ('admin.team.form')
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('manage_team.index') }}" ><button class="btn btn-default" type="button">Back</button></a>
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
        $(document).ready(function() {
            createLeadOptions([]);
            $('#manageTeamForm').validate({
                rules: {
                    team_type: 'required',
                    team_name: 'required',
                    team_lead: 'required',
                    team_color: 'required',
                    'team_members[]': {
                        required: true,
                    }
                },
                messages: {
                    team_type: 'The team type field is required.',
                    team_name: 'The team name field is required.',
                    team_lead: 'The team lead field is required.',
                    team_color: 'The team color field is required.',
                    'team_members[]': {
                        required: 'The team member field is required.',
                    }
                },
                errorPlacement: function(error, element) {
                    var inputName = $(element).attr('name');
                    $('.error[data-input="' + inputName + '"]').html(error);
                },
            });

            $('#team_name').keyup(function() {
                var teamName = $(this).val();
                if(!teamName){
                    $('#team_name_error').addClass('error').removeClass('text-success').text('');
                    return;
                }

                $.ajax({
                    url: "{{url('admin/check-team-name')}}/" + teamName,
                    type: 'GET',
                    success: function(response) {
                        if(response == 1){
                            $('#team_name_error').removeClass('error').addClass('text-success').text('Team name is available.');
                        } else{
                            $('#team_name_error').removeClass('text-success').addClass('error').text('Team name is not available.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        $('#team_type').change(function (){
            var selectType = $(this).val();
            $.ajax({
                url: "{{ route('fetchUsers') }}",
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'team_type': selectType
                },
                success: function(response) {
                    createLeadOptions(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        function createLeadOptions(data) {
            var leadDropdown = $("#team_lead");
            leadDropdown.empty();
            $("<option>").val('').text('Please Select Team Lead').prop('selected', true).appendTo(leadDropdown);
            $.each(data, function (key, value) {
                $("<option>").attr("value", key).text(value).appendTo(leadDropdown);
            });
            leadDropdown.select2();
        }

        $('#team_lead').change(function (){
            var teamLead = $(this).val();
            var selectType = $('#team_type').val();
            $.ajax({
                url: "{{ route('fetchTeamMember') }}",
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'team_lead': teamLead,
                    'team_type': selectType,
                },
                success: function(response) {
                    showMembers(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        function showMembers(data){
            var memberHtml = $('<div>').addClass('row');
            $('#team_member').empty();
            $.each(data, function(key, value) {
                var checkboxCol = $('<div>').addClass('col-md-3');
                var checkboxLabel = $('<label>');
                var checkbox = $('<input>').attr({
                    type: 'checkbox',
                    id: 'checkbox' + key,
                    value: key,
                    name: 'team_members[]',
                }).addClass('flat-red');
                var label = $('<span>').attr('style', 'margin-left: 10px').text(value);
                checkboxLabel.append(checkbox).append(label);
                checkboxCol.append(checkboxLabel);
                // checkboxCol.append(label);
                memberHtml.append(checkboxCol);
            });
            $('#team_member').append(memberHtml);
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass   : 'iradio_flat-green'
            });

            if(Object.keys(data).length === 0){
                $('#team_members_section').hide();
            } else {
                $('#team_members_section').show();
            }
        }
    </script>
@endsection
