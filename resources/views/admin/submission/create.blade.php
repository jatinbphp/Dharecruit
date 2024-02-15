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
                        <div class='add-new-form' style='{{(count($errors) == 0 ? "display: none;" : "")}}'>
                            {!! Form::open(['url' => route('submission.store'), 'id' => 'submissionsForm', 'class' => 'form-horizontal','files'=>true]) !!}
                                <div class="card-body">
                                    <input type="hidden" id="requirement_id" name="requirement_id" value="{{$requirement['id']}}">
                                    @include ('admin.submission.form')
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('submission.show',['submission'=>$requirement['id']]) }}" ><button class="btn btn-default" type="button">Back</button></a>
                                    <button class="btn btn-info float-right add-submission" id="submission_add" type="button">Add</button>
                                </div>
                            {!! Form::close() !!}
                        </div>
                        @php
                            $settingRow =  \App\Models\Setting::where('name', 'is_show_search_by_candidate_id')->first();
                        @endphp
                        <div class="search-emp-email">
                            <div class="row mb-2 mt-3 pl-3 pr-3">
                                <div class="col-sm-6">
                                    @if(!empty($settingRow) && $settingRow->value == 'yes')
                                        Search By Email Or Candidate Id
                                    @else
                                        Search By Email
                                    @endif

                                </div>
                            </div>
                            <div class="row pl-3 pr-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email', 'id' => 'search_email']) !!}
                                    </div>
                                </div>
                                @if(!empty($settingRow) && $settingRow->value == 'yes')
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Id', 'id' => 'search_id']) !!}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button class="btn btn-info float-left" id="search_fill" type="button">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
