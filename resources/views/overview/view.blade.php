@extends('template.layout')
@section('content')
<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
        <div class="row" style="margin-bottom:15px;">
            <div class="col-md-4"><h2 style="margin-top:0px;"><i class="entypo-list"></i> Overview</h2></div>			
            <div class="col-md-8"></div>
        </div>
        @if (Session::get('message') && Session::get('message') != "")
            <div class="row" style="padding-top:10px;">
                <div class="col-md-12">
                    <div  class="{{Session::get('class')}}{{Session::forget('class')}} alert-dismissible fade in">
                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        {!! Session::get('message') !!}
                        {{Session::forget('message')}}
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-responsive table-bordered datatable dataTable" id="tbl_overview">
                        <thead>
                            <tr class="replace-inputs">
                                <td>Full Name</td>
                                <td>Email</td>
                                <td>Sub Users</td>
                                <td>Allocated Device</td>
                                <td>Active Devices</td>
                                <td>Status</td>
                                <td>Date Created</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
       {!! $footer !!}
    </div>   
</div>
<div class="modal" id="user_profile_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!--            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title">Modal title</h4>
                        </div>-->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="message"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel-group" id="accordion-test">

                            <div class="panel panel-gradient">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-test" href="#collapseOne">
                                            <i class="entypo-users"></i>User Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <form id="frm_user_profile" method="post">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="first_name">First Name</label>
                                                        <input type="text" class="form-control" name="first_name" placeholder="First Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email_address">Email</label>
                                                        <input type="text" class="form-control" name="email_address" disabled="disabled">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="last_name">Last Name</label>
                                                        <input type="text" class="form-control" name="last_name" placeholder="Last Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mobile_phone">Mobile Phone</label>
                                                        <input type="text" class="form-control" name="mobile_phone" placeholder="Mobile Phone">
                                                    </div>
                                                </div>                                                
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="time_zone">Time Zone</label>
                                                        <select id="time_zone" name="time_zone" class="form-control" data-validate="required">
                                                            @foreach ($time_zones as $key => $val)
                                                                <option value="{{$key}}">{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a class="btn btn-info btn-icon icon-left pull-right" onclick="javascript:UpdateUserProfile()"><i class="fa fa-save"></i> Save</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-gradient">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-test" href="#collapseTwo">
                                            <i class="entypo-key"></i> Your Account Details
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <form id="frm_user_password" method="POST">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="password">Password</label>
                                                        <input type="password" class="form-control" name="password" placeholder="Password">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="confirm_password">Confirm Password</label>
                                                        <input type="password" class="form-control" name="confirm_password" placeholder="Password">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a class="btn btn-info btn-icon icon-left pull-right" type="button" onclick="javascript:UpdatePassword()"><i class="fa fa-save"></i> Save</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" name="hdn_platform_user_id"/>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="confirmation_modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p id="message" style="text-align: center; font-weight: bold; color:black;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default text-center" data-dismiss="modal">Ok</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="delete_alert" class="modal custom-width">
    <div class="modal-dialog" style="width:25%">
        <div class="modal-content">
            <form id="frm_delete" method="POST">
                <div style="font-weight:bold; color: black;" class="modal-body">
                    <i class="entypo-attention"></i> Are you sure you want to delete this user?
                </div>
                <div class="modal-footer text-center">
                    <button data-dismiss="modal" class="btn btn-default" type="button">No</button>
                    <button class="btn btn-red" type="submit">Yes</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#tbl_overview').dataTable({
            "processing": true,
            "serverSide": true,
            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "autoWidth": false,
            "ajax": "{{url("ajax/GenerateOverview")}}",
            "language": {
                "sEmptyTable": "<span class='text-center' style='color:#ff002a;font-weight:bold;'>No data available in table</span>",
                "paginate": {
                    "previous": "<i class='entypo-left-open'></i>",
                    "next": "<i class='entypo-right-open'></i>"
                }
            },
            "dom": "<'row'<'col-sm-3 col-left'l><'col-sm-9 col-right'<'export-data'T>f>r>t<'row'<'col-sm-3 col-left'i><'col-sm-9 col-right'p>>",
            "tableTools": {
                "sSwfPath": "{{$assets_dir.'/js/datatables/extensions/table_tools/swf/copy_csv_xls_pdf.swf'}}"
            },
            "columnDefs": [
                {"orderable": false, "targets": 2},
                {"orderable": false, "targets": 3},
                {"orderable": false, "targets": 4},
                {"orderable": false, "targets": 5},
                {"orderable": false, "targets": 7}
            ]
        });

        $('#user_profile_modal').on('hidden.bs.modal', function () {
            if ($.fn.dataTable.isDataTable('#tbl_overview')) {
                table = $('#tbl_overview').DataTable();
            }
            else {
                table = $('#tbl_overview').DataTable({
                    paging: false
                });
            }
            table.ajax.reload();
        });
    });

    function UpdateUserStatus(user_id, status) {
        $("#preloader").removeClass('hide');
        $.ajax({
            url: '{{url("ajax/UpdateUserStatus")}}',
            data: {user_id: user_id, status: status},
            type: 'post',
            complete: function (response) {
                $("#preloader").addClass('hide');
                if (response.responseText === '1') {
                    if (parseInt(status) === 1) {
                        $("#btn_user_status_" + user_id).removeClass();
                        $("#btn_user_status_" + user_id).addClass('btn btn-danger btn-sm');
                        $("#btn_user_status_" + user_id).html();
                        $("#btn_user_status_" + user_id).html("<i class='fa fa-ban'></i>");
                        $("#btn_user_status_" + user_id).attr('onclick', '');
                        $("#btn_user_status_" + user_id).attr('onclick', 'javascript:UpdateUserStatus(' + user_id + ',0);');
                    } else {
                        $("#btn_user_status_" + user_id).removeClass();
                        $("#btn_user_status_" + user_id).addClass('btn btn-success btn-sm');
                        $("#btn_user_status_" + user_id).html();
                        $("#btn_user_status_" + user_id).html("<i class='fa fa-check-circle'></i>");
                        $("#btn_user_status_" + user_id).attr('onclick', '');
                        $("#btn_user_status_" + user_id).attr('onclick', 'javascript:UpdateUserStatus(' + user_id + ',1);');
                    }
                }
            }
        });
    }

    function GetPlatformUserProfile(user_id) {
        $("#preloader").removeClass('hide');
        $.ajax({
            url: '{{url("ajax/GetPlatformUserProfile")}}',
            data: {user_id: user_id},
            type: 'post',
            complete: function (response) {
                $("#preloader").addClass('hide');
                var output = jQuery.parseJSON(response.responseText);
                $("input[name='first_name']").val(output.first_name);
                $("input[name='last_name']").val(output.last_name);
                $("input[name='email_address']").val(output.email_address);
                $("input[name='mobile_phone']").val(output.mobile_phone);
                $("select[name='time_zone']").val(output.time_zone);
                $("#user_profile_modal").modal('show');
            }
        });
    }

    function UpdateUserProfile() {
        $("#preloader").removeClass('hide');
        var frmData = $("#frm_user_profile").serialize();
        $.ajax({
            url: '{{url("ajax/UpdateUserProfile")}}',
            data: frmData,
            type: 'post',
            complete: function (response) {
                var output = response.responseText;
                $("#preloader").addClass('hide');
                $("#message").html(output);
                window.setTimeout(function () {
                    $("#message").html('');
                }, 2000);
            }
        });
    }

    function UpdatePassword() {
        $("#preloader").removeClass('hide');
        var frmData = $("#frm_user_password").serialize();
        $.ajax({
            url: '{{url("ajax/UpdatePassword")}}',
            data: frmData,
            type: 'post',
            complete: function (response) {
                var output = response.responseText;
                $("#preloader").addClass('hide');
                $("#message").html(output);
                window.setTimeout(function () {
                    $("#message").html('');
                }, 2000);
            }
        });
    }
    
    function DeleteAlert(user_id){
        var action = "{{url('overview/deleteuser?uid=')}}" + user_id;
        $("#frm_delete").attr('action', action);
        $("#delete_alert").modal('show');
    }
</script>


<link rel="stylesheet" href="{{$assets_dir.'/js/datatables/css/dataTables.bootstrap.css'}}">

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/datatables/js/jquery.dataTables.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/extensions/table_tools/js/dataTables.tableTools.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/js/dataTables.bootstrap.js'}}"></script>
@endsection