@php
    $user_group = \Illuminate\Support\Facades\Auth::user()->role;
    $permission = \App\Models\Permission::where('type', $user_group)->first();
    $modules = \App\Models\Permission::$permission;
    $right = !empty($permission) ? explode(',', $permission->access_modules) : [];
    $i = 1;
    foreach ($modules as $key => $mod){
        ${'access'.$i} = $key;
        ${'check'.$i} = in_array(${'access'.$i}, $right);
        $i++;
    }
    $loginRole = \Illuminate\Support\Facades\Auth::user()->role;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'Laravel') }} | {{ $menu }}</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content = "28800; url={{ route('login') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/iCheck/flat/blue.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/iCheck/all.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link rel="stylesheet" href="{{ URL::asset('assets/dist/css/custom.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/ladda/ladda-themeless.min.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/comboTree/comboTreeStyle.css')}}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.min.css">
    <link href="https://cdn.syncfusion.com/ej2/material.css" rel="stylesheet">
    <style>
        .candidate, .job-title {cursor: pointer}
        a.disabled {
            pointer-events: none;
            cursor: default;
        }
        .disabled{color: #c5c5c5!important;}

        [type="radio"]:checked,
        [type="radio"]:not(:checked) {
            position: absolute;
            left: -9999px;
        }
        [type="radio"]:checked + label,
        [type="radio"]:not(:checked) + label
        {
            position: relative;
            padding-left: 28px;
            cursor: pointer;
            line-height: 20px;
            display: inline-block;
        }
        [type="radio"]:checked + label:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 20px;
            border: 4px solid #1ABC9C;
            border-radius: 100%;
            background: #fff;
        }
        [type="radio"]:not(:checked) + label:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
            height: 20px;
            border: 4px solid #ddd;
            border-radius: 100%;
            background: #fff;
        }
        [type="radio"]:checked + label:after,
        [type="radio"]:not(:checked) + label:after {
            content: '';
            width: 6px;
            height: 6px;
            background: #1ABC9C;
            position: absolute;
            top: 7px;
            left: 7px;
            border-radius: 100%;
            -webkit-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }
        [type="radio"]:not(:checked) + label:after {
            opacity: 0;
            -webkit-transform: scale(0);
            transform: scale(0);
        }
        [type="radio"]:checked + label:after {
            opacity: 1;
            -webkit-transform: scale(1);
            transform: scale(1);
        }
        .border-warning-10{
            border: 10px solid #ffc107!important;
        }
        .border-width-5{
            border: 5px solid!important;
        }
        .border-color-info{
            color: #B266B3!important;
        }
        .border-color-warning{
            color: #ffc107!important;
        }
        .border-top {
            border-top: 1px solid #050505 !important;
        }
        .border-right {
            border-right: 1px solid #050505 !important;
        }
        .border-bottom {
            border-bottom: 1px solid #050505 !important;
        }
        .border-left {
            border-left: 1px solid #050505 !important;
        }
        .color-group {
            background-color: #E7FFF9
        }
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Adjust the transparency as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #spinner {
            border: 10px solid #f3f3f3;
            border-top: 10px solid #0b0b0c;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden-element {
            display: none;
        }

        caption {
            caption-side: top;
        }

        .border-with-label {
            position: relative;
            padding: 15px;
            margin-bottom: 15px;
        }

        .border-with-label::before {
            content: attr(data-label);
            position: absolute;
            top: -10px;
            left: 10px;
            padding: 0 5px;
            font-size: 15px;
            font-weight: bold;
            background-color: white;
        }

        .element-border {
            border: 1px solid black !important;
        }

        .rm-left-border {
            border-left: none !important;
        }
        .equalHeight {
            display: flex;
            flex-wrap: wrap;
        }
        .scrollable {
            overflow-y: auto;
        }
        .ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sticky-col {
            position: sticky !important;
            left: 0;
            background-color: #fff; /* Change as needed */
            z-index: 999;
        }
        .sticky-badge {
            position: sticky !important;
            left: 0;
            z-index: 999;
        }
        .report-badge {
            font-size: 1rem;
            font-weight: 600;
        }

        .sort-indicator {
            color: #777; /* Default color */
        }

        .indicator-class {
            color: #0a0e14 !important;
        }

        .sort-indicator.blur {
            opacity: 0.5; /* Make the arrow semi-transparent when blurred */
        }
    </style>
</head>
<body class="hold-transition sidebar-mini sidebar-collapse" id="bodyid">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ url('admin/dashboard') }}" class="nav-link">Home</a>
            </li>
            @if($loginRole == 'admin' || $check9)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('requirement.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Requirements') active @endif">All</button>
                    </a>
                </li>
            @endif

            @if(\Illuminate\Support\Facades\Auth::user()->role == 'bdm')
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('my_requirement') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='My Requirements') active @endif">My Requirements</button>
                    </a>
                </li>
            @endif

            @if($check11)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('submission.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Requirements') active @endif">All</button>
                    </a>
                </li>
            @endif

            @if(\Illuminate\Support\Facades\Auth::user()->role == 'recruiter')
                <li class="nav-item d-none d-sm-inline-block ml-2" class="nav-item">
                    <a href="{{ route('my_submission') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='My Requirements') active @endif">My Requirements</button>
                    </a>
                </li>
            @endif

            @if($loginRole == 'admin' || $check12)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('bdm_submission.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Manage Submission') active @endif">Submission</button>
                    </a>
                </li>
            @endif
            @if($loginRole == 'admin' || $check13)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('interview.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Manage Interview') active @endif">Interview</button>
                    </a>
                </li>
            @endif
        </ul>
        <ul class="navbar-nav ml-auto mb-1">
            @if(isLeadUser())
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'recruiter')
                    <li class="nav-item d-none d-sm-inline-block ml-2" class="nav-item">
                        <a href="{{ route('submission.teamSubmissions') }}">
                            <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Team Requirements') active @endif">Team Requirements</button>
                        </a>
                    </li>
                @else
                    <li class="nav-item d-none d-sm-inline-block ml-2">
                        <a href="{{ route('requirement.teamLeadRequirement') }}">
                            <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Team Requirements') active @endif">Team Requirement</button>
                        </a>
                    </li>
                @endif

                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('bdm_submission.teamLeadSubmissions') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Team Submission') active @endif">Team Submission</button>
                    </a>
                </li>

                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('interview.teamLeadInterviews') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if($menu=='Team Interviews') active @endif">Team Interviews</button>
                    </a>
                </li>
            @endif
            @if(isLeadUser() || isManager())
                @if(\Illuminate\Support\Facades\Auth::user()->role == 'bdm')
                    <li class="nav-item d-none d-sm-inline-block ml-2">
                        <a href="{{ route('reports',['type' => 'efficiency', 'subType' => 'lead_sub_received']) }}">
                            <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if(isset($subType) && $subType=='lead_sub_received') active @endif">Report</button>
                        </a>
                    </li>
                @endif

                @if(\Illuminate\Support\Facades\Auth::user()->role == 'recruiter')
                    <li class="nav-item d-none d-sm-inline-block ml-2">
                        <a href="{{ route('reports',['type' => 'efficiency', 'subType' => 'lead_sub_sent']) }}">
                            <button class="btn btn-block btn-outline-primary btn-sm mt-1 @if(isset($subType) && $subType=='lead_sub_sent') active @endif">Report</button>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span><i class="fa fa-user mr-2"></i>{{ ucfirst(Auth::guard('admin')->user()->name) }} ({{ucfirst(Auth::guard('admin')->user()->role)}})</span>
            </li>
        </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4" id="left-menubar" style="height: 100%; min-height:0!important; overflow-x: hidden;">
        <a href="{{url('/admin')}}" class="brand-link" style="text-align: center">
            <span class="brand-text font-weight-light"><b>{{ config('app.name', 'Laravel') }} Admin</b></span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item has-treeview @if(isset($menu) && $menu=='User') menu-open  @endif" style="border-bottom: 1px solid #4f5962; margin-bottom: 4.5%;">

                        <a href="#" class="nav-link @if(isset($menu) && $menu=='User') active  @endif">
                            <img src=" {{url('assets/dist/img/AdminLTELogo.png')}}" class="img-circle elevation-2" alt="User Image" style="width: 2.1rem; margin-right: 1.5%;">
                            <p style="padding-right: 6.5%;">
                                {{ ucfirst(Auth::guard('admin')->user()->name) }}
                                <i class="fa fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?php $eid = \Illuminate\Support\Facades\Auth::guard('admin')->user()->id; ?>
                                <a href="{{ route('profile_update.edit',['profile_update'=>$eid]) }}" class="nav-link @if(isset($menu) && $menu=='User') active @endif">
                                    <i class="nav-icon fa fa-pencil"></i><p class="text-warning">Edit Profile</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('logout') }}" class="nav-link">
                                    <i class="nav-icon fa fa-sign-out"></i><p class="text-danger">Log out</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link @if($menu=='Dashboard') active @endif">
                            <i class="nav-icon fa fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    @if($loginRole == 'admin' || $check1 || $check2 || $check3 || $check4 || $check5 || $check6 || $check20)
                        <li class="nav-item @if(in_array($menu, ['Permission','Admin User','BDM User','Recruiter User','TL Recruiter User','TL BDM User','Manage Team', 'Manage Manager'])) menu-open @endif">
                            <a href="#" class="nav-link @if(in_array($menu, ['Permission','Admin User','BDM User','Recruiter User','TL Recruiter User','TL BDM User','Manage Team', 'Manage Manager'])) active @endif">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Manage Users <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if($loginRole == 'admin' || $check1)
                                    <li class="nav-item">
                                        <a href="{{ route('permission.index') }}" class="nav-link @if($menu=='Permission') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage Permission</p>
                                        </a>
                                    </li>
                                @endif
                                @if($loginRole == 'admin' || $check2)
                                    <li class="nav-item">
                                        <a href="{{ route('user.index') }}" class="nav-link @if($menu=='Admin User') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage Admin</p>
                                        </a>
                                    </li>
                                @endif
                                @if($loginRole == 'admin' || $check3)
                                    <li class="nav-item">
                                        <a href="{{ route('bdm_user.index') }}" class="nav-link @if($menu=='BDM User') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage BDM</p>
                                        </a>
                                    </li>
                                @endif
                                @if($loginRole == 'admin' || $check4)
                                    <li class="nav-item">
                                        <a href="{{ route('recruiter_user.index') }}" class="nav-link @if($menu=='Recruiter User') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage Recruiter</p>
                                        </a>
                                    </li>
                                @endif
                                {{--@if($loginRole == 'admin' || $check5)
                                    <li class="nav-item">
                                        <a href="{{ route('tl_recruiter_user.index') }}" class="nav-link @if($menu=='TL Recruiter User') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage TL Recruiter</p>
                                        </a>
                                    </li>
                                @endif
                                @if($loginRole == 'admin' || $check6)
                                    <li class="nav-item">
                                        <a href="{{ route('tl_bdm_user.index') }}" class="nav-link @if($menu=='TL BDM User') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage TL BDM</p>
                                        </a>
                                    </li>
                                @endif--}}
                                @if($loginRole == 'admin' || $check20)
                                    <li class="nav-item">
                                        <a href="{{ route('manage_team.index') }}" class="nav-link @if($menu=='Manage Team') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage Team</p>
                                        </a>
                                    </li>
                                @endif
                                @if($loginRole == 'admin' || $check21)
                                    <li class="nav-item">
                                        <a href="{{ route('manage_manager.index') }}" class="nav-link @if($menu=='Manage Manager') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Manage Manager</p>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check7)
                        <li class="nav-item">
                            <a href="{{ route('category.index') }}" class="nav-link @if($menu=='Category') active @endif">
                                <i class="nav-icon fa fa-sitemap"></i>
                                <p>Manage Category</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check8)
                        <li class="nav-item">
                            <a href="{{ route('moi.index') }}" class="nav-link @if($menu=='Moi') active @endif">
                                <i class="nav-icon fa fa-bars"></i>
                                <p>Manage MOI</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check14)
                        <li class="nav-item">
                            <a href="{{ route('visa.index') }}" class="nav-link @if($menu=='Visa') active @endif">
                                <i class="nav-icon fa fa-passport"></i>
                                <p>Manage Visa</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check10)
                        <li class="nav-item">
                            <a href="{{ route('pv_company.index') }}" class="nav-link @if($menu=='PV Company') active @endif">
                                <i class="nav-icon fa fa-building"></i>
                                <p>Manage PV Company</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check15)
                        <li class="nav-item">
                            <a href="{{ route('employee.index') }}" class="nav-link @if($menu=='Employee') active @endif">
                                <i class="nav-icon fa fa-user"></i>
                                <p>Manage Employee</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check19)
                        <li class="nav-item">
                            <a href="{{ route('manage_candidate.index') }}" class="nav-link @if($menu=='Candidate') active @endif">
                                <i class="nav-icon fa fa-user-plus"></i>
                                <p>Manage Candidate</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check9)
                        <li class="nav-item">
                            <a href="{{ route('requirement.index') }}" class="nav-link @if($menu=='Requirements') active @endif">
                                <i class="nav-icon fa fa-file-signature"></i>
                                <p>Manage Requirement</p>
                            </a>
                        </li>
                    @endif

                    @if(\Illuminate\Support\Facades\Auth::user()->role == 'bdm')
                        <li class="nav-item">
                            <a href="{{ route('my_requirement') }}" class="nav-link @if($menu=='My Requirements') active @endif">
                                <i class="nav-icon fa fa-file-signature"></i>
                                <p>My Requirement</p>
                            </a>
                        </li>
                    @endif

                    @if($check11)
                        <li class="nav-item">
                            <a href="{{ route('submission.index') }}" class="nav-link @if($menu=='Requirements') active @endif">
                                <i class="nav-icon fa fa-file-signature"></i>
                                <p>Manage Requirement</p>
                            </a>
                        </li>
                    @endif

                    @if(\Illuminate\Support\Facades\Auth::user()->role == 'recruiter')
                        <li class="nav-item">
                            <a href="{{ route('my_submission') }}" class="nav-link @if($menu=='My Requirements') active @endif">
                                <i class="nav-icon fa fa-file-signature"></i>
                                <p>My Requirement</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check12)
                        <li class="nav-item">
                            <a href="{{ route('bdm_submission.index') }}" class="nav-link @if($menu=='Manage Submission') active @endif">
                                <i class="nav-icon fa fa fa-paper-plane"></i>
                                <p>Manage Submission</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin' || $check13)
                        <li class="nav-item">
                            <a href="{{ route('interview.index') }}" class="nav-link @if($menu=='Manage Interview') active @endif">
                                <i class="nav-icon fa fa-question-circle"></i>
                                <p>Manage Interview</p>
                            </a>
                        </li>
                    @endif

                    @if($loginRole == 'admin')
                        <li class="nav-item">
                            <a href="{{ route('setting.index') }}" class="nav-link @if($menu=='Manage Setting') active @endif">
                                <i class="nav-icon fa fa-cog"></i>
                                <p>Manage Settings</p>
                            </a>
                        </li>
                    @endif
                    @if($loginRole == 'admin' || $check16)
                        <li class="nav-item @if(in_array($menu, ['Performance Report', 'PV Company Report', 'POC Report', 'Employer Report', 'Employee Report'])) menu-open @endif">
                            <a href="#" class="nav-link @if(in_array($menu, ['Performance Report', 'PV Company Report', 'POC Report', 'Employer Report', 'Employee Report'])) active @endif">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Manage Reports <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            @if(in_array($loginRole, ['admin', 'bdm']))
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports',['type' => 'efficiency', 'subType' => 'sub_received']) }}" class="nav-link @if(isset($subType) && $subType=='sub_received') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Sub Received(BDM)</p>
                                        </a>
                                    </li>
                                </ul>
                            @endif
                            @if(in_array($loginRole, ['admin', 'recruiter']))
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports',['type' => 'efficiency', 'subType' => 'sub_sent']) }}" class="nav-link @if(isset($subType) && $subType=='sub_sent') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Sub Sent(Recruiter)</p>
                                        </a>
                                    </li>
                                </ul>
                            @endif
                            @if($loginRole == 'admin')
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports',['type' => 'p_v_report']) }}" class="nav-link @if($menu == 'PV Company Report') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Prime Vendor Report</p>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports',['type' => 'poc_report']) }}" class="nav-link @if($menu == 'POC Report') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>POC Report</p>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports',['type' => 'employer_report']) }}" class="nav-link @if($menu == 'Employer Report') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Employer Report</p>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('reports',['type' => 'employee_report']) }}" class="nav-link @if($menu == 'Employee Report') active @endif">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Employee Report</p>
                                        </a>
                                    </li>
                                </ul>
                            @endif
                        </li>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->role == 'bdm')
                        <li class="nav-item">
                            <a href="{{ route('pv_transfer.index') }}" class="nav-link @if($menu=='PV Data & Transfer') active @endif">
                                <i class="nav-icon fa fa-exchange-alt"></i>
                                <p>PV Data & Transfer</p>
                            </a>
                        </li>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a href="{{ route('mail_template.index') }}" class="nav-link @if($menu=='Mail Template') active @endif">
                                <i class="nav-icon fa fa-envelope"></i>
                                <p>Email Template</p>
                            </a>
                        </li>
                    @endif
{{--                    @if(\Illuminate\Support\Facades\Auth::user()->role == 'admin')--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('manage_candidate_view.index') }}" class="nav-link @if($menu=='Mail Template') active @endif">--}}
{{--                                <i class="nav-icon fa fa-binoculars"></i>--}}
{{--                                <p>Manage Candidate View</p>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
                </ul>
            </nav>
        </div>
    </aside>

    @yield('content')

    <footer class="main-footer">
        <strong>{{ config('app.name', 'Laravel') }} Admin</strong>
    </footer>
</div>
<script>
    var instanceGlobalBdm = [];
    var instanceGlobalRec = [];
</script>
<script src="{{ URL('assets/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{ URL('assets/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="{{ URL('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ URL('assets/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{ URL('assets/plugins/chart.js/Chart.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="{{ URL('assets/plugins/sparklines/sparkline.js')}}"></script>
<script src="{{ URL('assets/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<script src="{{ URL('assets/plugins/moment/moment.min.js')}}"></script>
<script src="{{ URL('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ URL('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<script src="{{ URL('assets/plugins/summernote/summernote-bs4.min.js')}}"></script>
<script src="{{ URL('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<script src="{{ URL('assets/dist/js/adminlte.js')}}"></script>
<script src="{{ URL('assets/dist/js/demo.js')}}"></script>
<!-- <script src="{{ URL('assets/dist/js/pages/dashboard.js')}}"></script> -->
<script src="{{ URL('assets/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{ URL('assets/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="{{ URL::asset('assets/plugins/ladda/spin.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/ladda/ladda.min.js')}}"></script>
<script src="{{ URL('assets/dist/js/jquery.validate.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/jSignature/libs/jSignature.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/jSignature/libs/modernizr.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/comboTree/comboTreePlugin.js')}}"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>Ladda.bind( 'input[type=submit]' );</script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        $('.select2').select2();
        $('#example2').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "dom": '<"top"i>rt<"bottom"flp><"clear">'
        });

        /*Datepicker*/
        $('.datepicker').datepicker({
            format: 'yyyy-m-d',
            autoclose: true,
        });

        $('.datepicker2').datepicker({
            format: 'yyyy-m-d',
            // startDate: '+0d',
            autoclose: true,
            todayHighlight: true
        });

        $('#reqDateFrom').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false
        });

        $('#reqDateFrom').on('apply.daterangepicker', function (event, picker) {
            if (picker.startDate) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            } else {
                $(this).val('');
            }
        });

        $('#reqDateTo').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false
        });

        $('#reqDateTo').on('apply.daterangepicker', function (event, picker) {
            if (picker.startDate) {
                $(this).val(picker.startDate.format('DD/MM/YYYY'));
            } else {
                $(this).val('');
            }
        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass   : 'iradio_flat-green'
        });

        $('#bdm_feedback').select2({
            templateResult: function(data) {
                // Apply custom classes to the option
                if (data.id) {
                    var colorClass = getBDMColorClass(data.id);
                    return $('<span class="' + colorClass + '">' + data.text + '</span>');
                }
                return data.text;
            }
        });

        function getBDMColorClass(value) {
            switch (value) {
                case 'accepted':
                    return 'text-success';
                case 'rejected':
                    return 'text-danger';
                case 'no_viewed':
                    return 'text-primary';
                default:
                    return '';
            }
        }

        $('#pv_feedback').select2({
            templateResult: function(data) {
                // Apply custom classes to the option
                if (data.id) {
                    var colorClass = getPVColorClass(data.id);
                    var PVColorCss = getPVColorCss(data.id);
                    return $('<span class="' + colorClass + '" style="' + PVColorCss + '">' + data.text + '</span>');
                }
                return data.text;
            }
        });

        function getPVColorClass(value) {
            switch (value) {
                case 'rejected_by_pv':
                case 'rejected_by_end_client':
                    return 'text-danger';
                case 'submitted_to_end_client':
                    return 'text-success';
                case 'no_response_from_pv':
                case 'position_closed':
                    return 'text-secondary';
                default:
                    return '';
            }
        }

        function getPVColorCss(value) {
            switch (value) {
                case 'rejected_by_pv':
                case 'no_response_from_pv':
                case 'position_closed':
                case 'submitted_to_end_client':
                    return 'border-bottom :solid;';
                case'rejected_by_end_client':
                    return 'border-bottom :6px double;';
                default:
                    return '';
            }
        }

        $('#client_feedback').select2({
            templateResult: function(data) {
                // Apply custom classes to the option
                if (data.id) {
                    var colorClass = getClientColorClass(data.id);
                    var divClass = getClientDivClass(data.id);
                    return $('<div class="' + divClass + ' text-center" style="width: fit-content;"><span class="' + colorClass + '">' + data.text + '</span>');
                }
                return data.text;
            }
        });

        function getClientColorClass(value) {
            switch (value) {
                case 'scheduled':
                case 're_scheduled':
                case 'selected_for_next_round':
                case 'confirmed_position':
                case 'waiting_feedback':
                    return 'text-dark';
                case 'backout':
                case 'rejected':
                    return 'text-white';
                default:
                    return '';
            }
        }

        function getClientDivClass(value) {
            switch (value) {
                case 'scheduled':
                    return 'border border-warning rounded-pill  pt-2 pl-2 pb-2 pr-2';
                case 're_scheduled':
                    return 'border-warning-10 rounded-pill  pt-2 pl-2 pb-2 pr-2';
                case 'selected_for_next_round':
                case 'waiting_feedback':
                    return 'bg-warning rounded-pill  pt-2 pl-2 pb-2 pr-2';
                case 'confirmed_position':
                    return 'bg-success  pt-2 pl-2 pb-2 pr-2';
                case 'backout':
                    return 'bg-dark  pt-2 pl-2 pb-2 pr-2';
                case 'rejected':
                    return 'bg-danger  pt-2 pl-2 pb-2 pr-2';
                default:
                    return '';
            }
        }

        $('.toggle-checkbox').bootstrapToggle();

        $('#data_toggle').change(function (){
            if($(this).is(':checked')){
                $('.empty-row').hide();
            }else{
                $('.empty-row').show();
            }
        });
    });
</script>

<script src="{{ URL::asset('assets/plugins/summernote/summernote.js') }}"></script>

<script type="text/javascript">
    /*DISPLAY IMAGE*/
    function AjaxUploadImage(obj,id){
        var file = obj.files[0];
        var imagefile = file.type;
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            $('#previewing'+URL).attr('src', 'noimage.png');
            alert("<p id='error'>Please Select A valid Image File</p>" + "<h4>Note</h4>" + "<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
            return false;
        } else{
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(obj.files[0]);
        }

        function imageIsLoaded(e){
            $('#DisplayImage').css("display", "block");
            $('#DisplayImage').css("margin-top", "1.5%");
            $('#DisplayImage').attr('src', e.target.result);
            $('#DisplayImage').attr('width', '150');
        }
    }

    /*REORDER CODE*/
    function slideout() {
        setTimeout(function() {
            $("#responce").slideUp("slow", function() {});
        }, 3000);
    }
    $("#responce").hide();

    $( function() {
        $('#filterDiv').hide();
        $( "#sortable" ).sortable({opacity: 0.9, cursor: 'move', update: function() {
                var order = $(this).sortable("serialize") + '&update=update';
                var reorder_url = $(this).attr("url");
                $.get(reorder_url, order, function(theResponse) {
                    $("#responce").html(theResponse);
                    $("#responce").slideDown('slow');
                    slideout();
                });
            }});
        $( "#sortable" ).disableSelection();

        /*SUMMER NOTE CODE*/
        $(".description").each(function() {
            var textarea = $(this);
            textarea.summernote({
                height: 250,
                placeholder: textarea.attr('placeholder'),
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize', 'height']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['table','picture','link','map','minidiag']],
                    ['misc', ['codeview']],
                ],
                callbacks: {
                    onImageUpload: function(files) {
                        for (var i = 0; i < files.length; i++)
                            upload_image(files[i], this);
                    }
                },
                onInit: function() {
                    var placeholderText = textarea.attr('placeholder');
                    placeholderText = placeholderText.replace(/\n/g, "<br>");
                    textarea.summernote('option', 'placeholder', placeholderText);
                },
                onChange: function(contents) {
                    if (contents.trim() === '') {
                        var placeholderText = textarea.attr('placeholder');
                        placeholderText = placeholderText.replace(/\n/g, "<br>");
                        textarea.summernote('option', 'placeholder', placeholderText);
                    }
                }
            });
        });

        /* COLOR PICKER CODE */
        $('.colorpicker-element').colorpicker();
        $('.my-colorpicker2').on('colorpickerChange', function(event) {
            $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
        })

        /* TOOLTIP CODE */
        $('[data-toggle="tooltip"]').tooltip();

        function upload_image(file, el) {
            var form_data = new FormData();
            form_data.append('image', file);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
                data: form_data,
                url: '{{url('admin/image/upload')}}',
                type: "post",
                cache: false,
                contentType: false,
                processData: false,
                success: function(img){
                    $(el).summernote('editor.insertImage', img);
                }
            });
        }

        $('#requirementTable, #mySubmissionTable, #interviewTable tbody').on('click', '.job-title', function (event) {
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{route('get_requirement')}}",
                type: "post",
                data: {'id': id,'_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    if(data.status == 1){
                        $('#jobTitle').html(data.requirementTitle);
                        $('#requirementData').html(data.requirementContent);
                        $('#status_submit').hide();
                        $('#candidateData').hide();
                        $('#statusUpdate').hide();
                        $('#requirementData').removeClass('border-bottom mb-2 pb-2');
                        $('#candidateModal').modal('show');
                        if(data.is_show_requirement == 1){
                            $('.job-title-'+id).removeClass('pt-1 pl-2 pb-1 pr-2 border border-primary text-primary border-warning text-warning');
                        }
                        if(data.isShowRequirement == 1){
                            $('.show-logs').hide();
                            $('.show-view').hide();
                            $('.show-edit').hide();
                        }
                    }else{
                        swal("Cancelled", "Something is wrong. Please try again!", "error");
                    }
                }
            });
        });

        $('#candidateModal').on('shown.bs.modal', function () {
            var div1 = $('.first-div');
            var div2 = $('.second-div');
            var scrollableClass = 'scrollable';

            var outerHeightDiv1 = div1.outerHeight();
            div2.height(outerHeightDiv1);

            // Check if content height exceeds the set height
            if (div2[0].scrollHeight > outerHeightDiv1) {
                div2.addClass(scrollableClass);
            } else {
                div2.removeClass(scrollableClass);
            }
        });

        function setHeight() {
            var outerHeightDiv1 = div1.outerHeight();
            div2.height(outerHeightDiv1);

            // Check if content height exceeds the set height
            if (div2[0].scrollHeight > outerHeightDiv1) {
                div2.addClass(scrollableClass);
            } else {
                div2.removeClass(scrollableClass);
            }
        }

        $('#showDate').change(function(){
            if($('#showDate').is(":checked")){
                $(".submission-date").show();
            }else{
                $(".submission-date").hide();
            }
        })
    });

    $('#filterBtn').on('click', function(){
        $('#filterDiv').toggle('show');
    });

    function showData(id,type) {
        $("."+type+id).show();
        $("."+type+"icon-"+id).hide();
    }

    function toggleOptions(type) {
        if($("#"+type).is(':checked')){
            $('.'+type).show();
            $('.'+type+'-icon').hide();
        } else {
            $('.'+type).hide();
            $('.'+type+'-icon').show();
        }
    }

    $('#requirementTable, #mySubmissionTable tbody').on('click', '.view-submission', function (event) {
        var requirementId = $(this).attr('data-id');
        $.ajax({
            url: "{{route('get_submission')}}",
            type: "post",
            data: {'requirement_id': requirementId,'_token' : $('meta[name=_token]').attr('content') },
            success: function(responce){
                if(responce.status == 1){
                    $('#submissionHeadingData').html(responce.submissionHeadingData);
                    $('#submissionHeaderData').html(responce.submissionHeaderData);
                    $('#submissionData').html(responce.submissionData);
                    $('#viewSubmissionCandidateModal').modal('show');
                }else{
                    swal("Cancelled", "Something is wrong. Please try again!", "error");
                }
            }
        });
    });

    $('#showTime').change(function(){
        if($('#showTime').is(':checked')){
            $('.status-time').show();
        } else {
            $('.status-time').hide();
        }
    });
    function showPVData(){
        $(".pv-companny-popup-icon").hide();
        $(".pv-company").show();
    }

    function showUpdateSubmissionModel(id){
        // $.ajax({
        //     url: "{{route('get_update_submission_data')}}",
        //     type: "POST",
        //     data: {'id':id, _token: '{{csrf_token()}}' },
        //     success: function(response){
        //         if(response.status == 1){
        //             addSubmissionData(response.submissionData);
        //             $("#updateSubmissionCandidateModal").modal('show');
        //         }else{
        //             swal("Error", "Something is wrong!", "error");
        //         }
        //     }
        // });
    };

    function addSubmissionData(data){
        var submissionData = data.submission;
        var linkingData = data.linking_data;
        $('#submissionsForm *').filter(':input').each(function () {
            var tagType = $(this).prop("tagName").toLowerCase();
            var elementId = this.id;
            if(elementId){
                if(tagType == 'input'){
                    var type = $("#" + elementId).attr("type");
                    if(type == 'file'){
                        $('#resumeId').remove();
                        var resumeElement = '<div id="resumeId" class="col-md-2 mt-4 "><div id="documentContent" class="text-center"><a href="{{asset("storage")}}/'+ submissionData['documents']+'" target="_blank"><img src=" {{url('assets/dist/img/resume.png')}}" height="50"></a></div></div>';
                        var documentNameArray = submissionData['documents'].split('/');
                        var documentName = '2' in documentNameArray ? documentNameArray[2] : '';
                        var label = "<label>"+documentName+"</label>";
                        $("#" + elementId).closest('div').append(resumeElement);
                        $("#documentContent").append(label);
                    } else {
                        var id = "#" + elementId;
                        $(id).val(submissionData[elementId]);
                        var employeeEmail = submissionData['employee_email'];
                        if(elementId == 'employee_email'){
                            $('#add_link_email_icon').attr("onclick", "showFiled('linking_email', '"+ employeeEmail +"')");
                            var textBox = document.getElementById('employee_email');

                            if(textBox){
                                $('#linkEmployeeEmail').remove();
                                textBox.insertAdjacentHTML('afterend', linkingData['linkEmployeeEmail']);
                            }
                        } else if(elementId == 'employee_phone'){
                            var employeeEmail = submissionData['employee_email'];
                            $('#add_phone_icon').attr("onclick", "showFiled('linking_phone', '"+ employeeEmail +"')");
                            var textBox = document.getElementById('employee_phone');

                            if(textBox){
                                $('#linkEmployeePhoneNumber').remove();
                                textBox.insertAdjacentHTML('afterend', linkingData['linkEmployeePhoneNumber']);
                            }
                        }
                    }
                } else if(tagType == 'select'){
                    if($("#" +elementId).length > 0){
                        if($("#" +elementId).hasClass('select2-hidden-accessible')){
                            $("#" +elementId).select2("val", submissionData[elementId]);
                        }else{
                            $("#" +elementId).val(submissionData[elementId])
                        }
                    }
                }
            }
            $("#existResume").val(submissionData['documents']);
        });
    }

    function updateSubmission(){
        $.ajax({
            url: "{{route('update_submission_data')}}",
            type: "POST",
            data: $('#submissionsForm').serialize(),
            success: function(response){
                $("#updateSubmissionCandidateModal").modal('hide');
                if(response.status == 1){
                    window.location.replace(response.url);
                }else{
                    swal("Error", "Something is wrong!", "error");
                }
            }
        });
    }

    function showLogs(){
        if($('.log-button').hasClass('show-logs')){
            $('.log-data').show();
            $('.log-button').removeClass('show-logs btn-primary');
            $('.log-button').addClass('hide-logs btn-danger');
            $('.log-button').html('Hide Logs');
        } else {
            $('.log-data').hide();
            $('.log-button').addClass('show-logs btn-primary');
            $('.log-button').removeClass('hide-logs btn-danger');
            $('.log-button').html('Show Logs');
        }
    }

    function showView() {
        $('.model-data-view').show();
        $('.model-data-edit').hide();
        if(!$('.log-button').hasClass('no-display')){
            $('.log-button').show();
        }
        $('.show-view').addClass('btn-primary');
        $('.show-view').removeClass('btn-outline-primary');
        $('.show-edit').removeClass('btn-warning');
        $('.show-edit').addClass('btn-outline-warning');
    }

    function showEdit() {
        $('.model-data-view').hide();
        $('.model-data-edit').show();
        $('.log-button').hide();
        $('.show-edit').addClass('btn-warning');
        $('.show-edit').removeClass('btn-outline-warning');
        $('.show-view').addClass('btn-outline-primary');
        $('.show-view').removeClass('btn-primary');
    }

    $('.model-close').click(function(){
        $('.log-data').hide();
        $('.log-button').addClass('show-logs btn-primary');
        $('.log-button').removeClass('hide-logs btn-danger');
        $('.log-button').html('Show Logs');
    })

    $('#requirementTable, #interviewTable, #mySubmissionTable tbody').on('click', '.candidate', function (event) {
        var cId = $(this).attr('data-cid');
        $.ajax({
            url: "{{route('get_candidate')}}",
            type: "post",
            data: {'cId': cId,'_token' : $('meta[name=_token]').attr('content') },
            success: function(data){
                if(data.status == 1){
                    if(data.isSamePvCandidate == 1){
                        $('#candidateSubmissionMessage').removeClass('d-none');
                    } else {
                        $('#candidateSubmissionMessage').addClass('d-none');
                    }
                    if(data.showLogButton == 0){
                        $('.show-logs').addClass('no-display');
                        $('.show-logs').hide();
                    } else {
                        $('.show-logs').removeClass('no-display');
                        $('.show-logs').show();
                    }
                    $('.show-view').show();
                    $('.show-edit').show();
                    $('.pv-status-select').show();
                    var submission = data.submission;
                    $('#jobTitle').html(submission.requirement.job_title);
                    $('#submissionId').val(cId);
                    $('#status_submit').show();
                    $('#candidateData').show();
                    $('#statusUpdate').show();
                    if ($("#candidateStatus").length > 0) {
                        $("#candidateStatus").select2("val", submission.status);
                    }
                    if ($("#common_skills").length > 0) {
                        $("#common_skills").select2("val", submission.common_skills);
                    }
                    if ($("#skills_match").length > 0) {
                        $("#skills_match").select2("val", submission.skills_match);
                    }
                    if ($("#candidatePvStatus").length > 0) {
                        $("#candidatePvStatus").select2("val", submission.pv_status);
                    }

                    if(submission.status && submission.status != 'accepted'){
                       $('.pv-status-select').hide();
                    }else{
                        if ($('#candidateStatus').is(':visible')) {
                            $('.pv-status-select').show();
                        }
                    }
                    $("#reason").val(submission.reason);
                    $("#pv_rej_reason").val(submission.pv_reason);
                    $('#requirementData').html(data.requirementData);
                    $('#candidateData').html(data.candidateData);
                    $('#candidateData').html(data.candidateData);
                    $('#historyData').html(data.historyData);
                    $('#edit-section').html(data.editData);
                    $('#common-skill').html(submission.common_skills);
                    $('#skill-match').html(submission.skills_match);
                    $('#other-reason').html(submission.reason);
                    $('#status').html(submission.status[0].toUpperCase() + submission.status.slice(1))
                    addSubmissionData(data);
                    if(submission.pv_status){
                        $('#candidateStatus').prop('disabled', true);
                        var pvStatus = submission.pv_status.replace(/_/g, ' ');
                        $('#pv_status_data').html(pvStatus[0].toUpperCase() + pvStatus.slice(1));
                    }else{
                        $('#candidateStatus').prop('disabled', false);
                    }
                    addSubmissionData(data);
                    $('#candidatesubmissionId').val(data.submission.id);
                    $('#candidateModal').modal('show');
                    if(data.is_show == 1){
                        $('.candidate-'+cId).parent('div').removeClass('border');
                    }
                    if(data.isInterviewCreated == 1){
                        $('#candidatePvStatus').prop('disabled', true);
                    }else{
                        $('#candidatePvStatus').prop('disabled', false);
                    }
                }else{
                    swal("Cancelled", "Something is wrong. Please try again!", "error");
                }
            }
        });
    });

    $('#candidateStatus').on('change', function(){
        if($(this).val() == 'rejected'){
            $('.rejection').show();
        }else{
            $('.rejection').hide();
        }

        if($(this).val() == 'accepted'){
            if ($('#candidateStatus').is(':visible')) {
                $('.pv-status-select').show();
            }
        }else{
            if ($('#candidateStatus').is(':visible')) {
                $('.pv-status-select').hide();
                $('#candidatePvStatus').val(null).trigger('change');
            }
        }
    });

    $('#candidatePvStatus').on('change', function(){
        if($(this).val() == ''){
            if ($('#candidateStatus').is(':visible')) {
                $('#candidateStatus').prop('disabled', false);
            }
        }else{
            if ($('#candidateStatus').is(':visible')) {
                $('#candidateStatus').prop('disabled', true);
            }
        }

        var rejReasons = ['rejected_by_pv', 'rejected_by_end_client'];
        if(rejReasons.includes($(this).val())){
            $('.pv_rejection').show();
        } else {
            $('.pv_rejection').hide();
        }
    });

    $('#show_my_candidate').on('change', function(){
        if($('#show_my_candidate').is(':checked')){
            $('.other-candidate').hide();
        }else{
            $('.other-candidate').show();
        }
    });

    $('#show_my_team_candidate').on('change', function(){
        if($('#show_my_team_candidate').is(':checked')){
            $('.other-team-candidate').hide();
        }else{
            $('.other-team-candidate').show();
        }
    });

    $('#showFeedback').change(function(){
        if($('#showFeedback').is(':checked')){
            $('.feedback').show();
        } else {
            $('.feedback').hide();
        }
    });

    function toggleLink() {
        $('.linking-filed').toggle();
    }

    function showFiled(type, email){
        $('#type').val(type);
        $('#link_emp_email').val(email);
        $('.linking-emp-data').hide();
        $("#"+type).show();
        $("#linking_emp_data").modal('show');
    }

    function linkEmpButton(){
        var email = $('#link_emp_email').val();
        var type = $('#type').val();

        if(!type || !email){
            swal("Error", "Something is wrong.", "error");
            return;
        }

        var empEmail = $('#linking_emp_email').val();
        var empPhone = $('#linking_emp_phone_number').val();

        if(type == 'linking_email'){
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if(!emailPattern.test(empEmail.trim())){
                swal("Error", "Please Enter Valid Email Address.", "error");
                return;
            }
        } else if(type == 'linking_phone'){
            var phoneno = /^\d{10}$/;
            if(!phoneno.test(empPhone.trim())){
                swal("Error", "Please Enter Valid Phopne Number.", "error");
                return;
            }
        }

        $.ajax({
            url : "{{ route('submission.saveEmpLinkingData') }}",
            data : {'emp_email' : empEmail, 'emp_phone' : empPhone, 'type': type,'email': email, "_token": "{{ csrf_token() }}",},
            type : 'POST',
            dataType : 'json',
            success : function(data){
                if(data.is_found == 1) {
                    $("#linking_emp_data").modal('hide');
                    swal("Error", data.message, "error");
                }
                else if(data.status == 1){
                    var li = '<li class="list-group-item p-1"><span class="text-primary">'+ data.value + '</span> ( '+ data.user_name +' : ' + data.date + ' ) </li>'
                    $("#"+data.parent_div+" ul").append(li);
                    $("#linking_emp_data").modal('hide');
                    swal("Success", "Data Added SuccessFully.", "success");
                } else {
                    $("#linking_emp_data").modal('hide');
                }
                $('#linking_emp_email').val('');
                $('#linking_emp_phone_number').val('');
            }
        });
    };

    $('#toggle-poc').change(function(){
        if($('#toggle-poc').is(':checked')){
            $('.toggle-column').removeClass('hidden-element');
        } else {
            $('.toggle-column').addClass('hidden-element');
        }
    });

    $('#toggle_job_keyword').change(function(){
        if($(this).is(':checked')){
            $('.toggle-job-keyword-column').removeClass('hidden-element');
        } else {
            $('.toggle-job-keyword-column').addClass('hidden-element');
        }
    });

    function submitForm(){
        $("#candidateStatus").prop("disabled", false);
        $("#candidateForm").submit();
    }

    function toggleButton(el, text){
        const companyId = $(el).attr('data-company-id');
        const className = text +'-group-' + companyId;
        const mainTrClass = text +'-' + companyId;
        if($(el).hasClass('hide-rows')){
            $(el).removeClass('hide-rows').addClass('show-rows');
            $('.'+className).removeClass('border-bottom border-top');
            const tr = $('tr:has(td.' + className + ')');
            tr.first().find('td.' + className + ':first-child').addClass('border-top');
            tr.last().find('td.' + className).addClass('border-bottom');
        } else {
            $(el).addClass('hide-rows').removeClass('show-rows');
            $('tr.' + mainTrClass).find('td').addClass('border-top border-bottom');
        }
        $(el).find("i").toggleClass("fa-plus fa-minus");
    }

    function showJobData(title)
    {
        $('#modelTitle').html(title);
        $('#modelTitle').prop('outerHTML');
        $('#modalContent').html($('#jobKeyword').prop('outerHTML'));
        $("#showAllDataModal").modal("show");
    }

    function closeShowAllDataModal() {
        $('#showAllDataModal').modal('hide');
        event.stopPropagation();
    }

    // function copyRequirementData()
    // {
    //     <div class="col-md-2 mt-1 text-right">
    //          <button id="copyButton" onclick="copyRequirementData()" type="button">Copy Content</button>
    //    </div>
    //     var firstDivContent = $('#copy-req-data').prop('outerHTML');
    //     var secondDivContent = $('#copy-desc-data').prop('outerHTML');
    //
    //     // Combine the content
    //     var combinedContent = firstDivContent + '\n' + secondDivContent;
    //
    //     // navigator.clipboard.writeText(combinedContent)
    //     //     .then(function() {
    //     //         console.log('Text successfully copied to clipboard');
    //     //     })
    //     //     .catch(function(err) {
    //     //         console.error('Unable to copy text to clipboard', err);
    //     //     });
    // }

    function toggleRedRequirement(requirementId){
        if(!requirementId){
            return;
        }

        $.ajax({
            url: "{{ route('requirement.update_is_red') }}",
            method: 'POST',
            data: {
                '_token' : '{{ csrf_token() }}',
                'id'     : requirementId,
            },
            success: function(response) {
                if(response.status == 1){
                    if($('#'+requirementId).is(':checked')){
                        $('#row-'+requirementId).css('background-color', '#fb0000');
                    } else {
                        $('#row-'+requirementId).css('background-color', '');
                    }
                    swal("Success", "Data Updated Successfully!", "success");
                } else {
                    swal("Error", "Something Went Wrong!", "error");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }
    function getRandomColor() {
        var r, g, b;
        var threshold = 100;

        do {
            r = Math.floor(Math.random() * 256);
            g = Math.floor(Math.random() * 256);
            b = Math.floor(Math.random() * 256);
        } while (r < threshold && g < threshold && b < threshold);

        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    function prepareDatesBasedOnStepSize(fromDateInput, toDateInput, stepValue, stepType){
        var fromDate = new Date(fromDateInput.val());
        var toDate = new Date(toDateInput.val());

        if (stepType === 'monthly') {
            fromDate = new Date(fromDate.getFullYear(), fromDate.getMonth());
            toDate = new Date(fromDate.getFullYear(), fromDate.getMonth() + stepValue, 0);
        } else if (stepType === 'weekly') {
            const currentDay = fromDate.getDay();
            const mondayOffset = (currentDay === 1 ? 0 : currentDay === 0 ? 6 : currentDay - 1);
            fromDate.setDate(fromDate.getDate() - mondayOffset);
            toDate = new Date(fromDate);
            toDate.setDate(toDate.getDate() + (stepValue * 7) - 1);
        } else if (stepType === 'daily') {
            fromDate.setDate(fromDate.getDate());
            toDate = new Date(fromDate);
            toDate.setDate(toDate.getDate() + stepValue - 1);
        }

        console.log(stepValue);
        console.log(stepType);

        fromDateInput.val(formatDate(fromDate));
        toDateInput.val(formatDate(toDate));
    }

    function setDateForNextPrevButtons(step, fromDateInput, toDateInput, stepValue, stepType){
        var fromDate = new Date(fromDateInput.val());
        var toDate = new Date(toDateInput.val());
        if (stepType === 'monthly') {
            if (step == 1) {
                fromDate.setMonth(fromDate.getMonth() + stepValue);
                toDate = new Date(fromDate.getFullYear(), fromDate.getMonth() + stepValue, 0);
            }
            else if (step == -1) {
                fromDate = new Date(fromDate.getFullYear(), fromDate.getMonth() - stepValue, 1);
                toDate = new Date(fromDate.getFullYear(), fromDate.getMonth() + stepValue, 0);
            }
        } else if (stepType === 'weekly') {
            const currentDay = fromDate.getDay();
            const mondayOffset = (currentDay === 1 ? 0 : currentDay === 0 ? 6 : currentDay - 1);

            if (step == 1) {
                fromDate.setDate(fromDate.getDate() - mondayOffset + stepValue * 7);
                toDate = new Date(fromDate);
                toDate.setDate(toDate.getDate() + (stepValue * 7) - 1);
            } else if (step == -1) {
                fromDate.setDate(fromDate.getDate() - mondayOffset - stepValue * 7);
                toDate = new Date(fromDate);
                toDate.setDate(toDate.getDate() + (stepValue * 7) - 1);
            }
        } else if (stepType === 'daily') {
            if (step == 1) {
                fromDate.setDate(fromDate.getDate() + stepValue);
                toDate = new Date(fromDate);
                toDate.setDate(toDate.getDate() + stepValue - 1);
            } else if (step == -1) {
                fromDate.setDate(fromDate.getDate() - stepValue);
                toDate = new Date(fromDate);
                toDate.setDate(toDate.getDate() + stepValue - 1);
            }
        }

        fromDateInput.val(formatDate(fromDate));
        toDateInput.val(formatDate(toDate));
    }

    function formatDate(date) {
        var year = date.getFullYear();
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var day = date.getDate().toString().padStart(2, '0');
        return month + '/' + day + '/' + year;
    }

    var sortOrder = 1;
    function sortTable(table, columnIndex) {
        var rows = table.find('tbody tr').get();
        rows.sort(function(a, b) {
            var keyA = $(a).find('td').eq(columnIndex).text().trim();
            var keyB = $(b).find('td').eq(columnIndex).text().trim();

            var intA = parseInt(keyA);
            var intB = parseInt(keyB);

            if (!isNaN(intA) && !isNaN(intB)) {
                return sortOrder * (intA - intB);
            } else {
                return sortOrder * keyA.localeCompare(keyB);
            }

        });
        var parentRows = [];
        var childRows = [];
        rows.forEach(function(row) {
            if ($(row).hasClass('parent')) {
                parentRows.push(row);
            } else {
                childRows.push(row);
            }
        });

        var sortedRows = parentRows.reduce(function(acc, parentRow) {
            acc.push(parentRow);
            var correspondingChildRows = childRows.filter(function(childRow) {
                return $(childRow).prevAll('.parent').first()[0] === parentRow;
            });
            correspondingChildRows.forEach(function(childRow) {
                acc.push(childRow);
            });
            return acc;
        }, []);
        table.children('tbody').empty().append(sortedRows);
    }
</script>
@yield('jquery')
</body>
</html>
