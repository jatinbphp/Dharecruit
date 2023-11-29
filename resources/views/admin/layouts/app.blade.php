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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
                        <button class="btn btn-block btn-outline-primary btn-sm @if($menu=='Requirements') active @endif">All</button>
                    </a>
                </li>
            @endif

            @if(\Illuminate\Support\Facades\Auth::user()->role == 'bdm')
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('my_requirement') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm @if($menu=='My Requirements') active @endif">My Requirements</button>
                    </a>
                </li>
            @endif

            @if($check11)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('submission.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm @if($menu=='Requirements') active @endif">All</button>
                    </a>
                </li>
            @endif

            @if(\Illuminate\Support\Facades\Auth::user()->role == 'recruiter')
                <li class="nav-item d-none d-sm-inline-block ml-2" class="nav-item">
                    <a href="{{ route('my_submission') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm @if($menu=='My Requirements') active @endif">My Requirements</button>
                    </a>
                </li>
            @endif

            @if($loginRole == 'admin' || $check12)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('bdm_submission.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm @if($menu=='Manage Submission') active @endif">Submission</button>
                    </a>
                </li>
            @endif
            @if($loginRole == 'admin' || $check13)
                <li class="nav-item d-none d-sm-inline-block ml-2">
                    <a href="{{ route('interview.index') }}">
                        <button class="btn btn-block btn-outline-primary btn-sm  @if($menu=='Manage Interview') active @endif">Interview</button>
                    </a>
                </li>
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

                    @if($loginRole == 'admin' || $check1 || $check2 || $check3 || $check4 || $check5 || $check6)
                        <li class="nav-item @if(in_array($menu, ['Permission','Admin User','BDM User','Recruiter User','TL Recruiter User','TL BDM User'])) menu-open @endif">
                            <a href="#" class="nav-link @if(in_array($menu, ['Permission','Admin User','BDM User','Recruiter User','TL Recruiter User','TL BDM User'])) active @endif">
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
                                @if($loginRole == 'admin' || $check5)
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
                </ul>
            </nav>
        </div>
    </aside>

    @yield('content')

    <footer class="main-footer">
        <strong>{{ config('app.name', 'Laravel') }} Admin</strong>
    </footer>
</div>
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
            callbacks: {
                onImageUpload: function(files) {
                    for (var i = 0; i < files.length; i++)
                        upload_image(files[i], this);
                }
            },
        });

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

        $('#showDate').click(function(){
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

    $('#showTime').click(function(){
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
        $.ajax({
            url: "{{route('get_update_submission_data')}}",
            type: "POST",
            data: {'id':id, _token: '{{csrf_token()}}' },
            success: function(response){
                if(response.status == 1){
                    addSubmissionData(response.submissionData);
                    $("#updateSubmissionCandidateModal").modal('show');
                }else{
                    swal("Error", "Something is wrong!", "error");
                }
            }
        });
    };

    function addSubmissionData(submissionData){
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
        $('.log-button').show();
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
                    if(data.showLogButton == 0){
                        $('.show-logs').hide();
                    } else {
                        $('.show-logs').show();
                    }
                    $('.show-view').show();
                    $('.show-edit').show();
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
                    $("#reason").val(submission.reason);
                    $('#requirementData').html(data.requirementData);
                    $('#candidateData').html(data.candidateData);
                    $('#historyData').html(data.historyData);
                    $('#edit-section').html(data.editData);
                    $('#common-skill').html(submission.common_skills);
                    $('#skill-match').html(submission.skills_match);
                    $('#other-reason').html(submission.reason);
                    $('#status').html(submission.status[0].toUpperCase() + submission.status.slice(1))
                    addSubmissionData(data.submission);
                    $('#candidateModal').modal('show');
                    if(data.is_show == 1){
                        $('.candidate-'+cId).parent('div').removeClass('border');
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
    });

    $('#show_my_candidate').on('change', function(){
        if($('#show_my_candidate').is(':checked')){
            $('.other-candidate').hide();
        }else{
            $('.other-candidate').show();
        }
    });

</script>
@yield('jquery')
</body>
</html>
