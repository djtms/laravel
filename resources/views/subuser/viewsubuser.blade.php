@extends('template.layout')
@section('content')
<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
        <div class="row" style="margin-bottom:15px;">
            <div class="col-sm-4">
                <h2 style="margin-top:0px;"><i class="entypo-users"></i> Sub Users</h2>
            </div>
            <div style="" class="col-sm-8 text-right">
                <a  href="javascript:showSubUserForm();" class="btn btn-red btn-lg btn-icon icon-left">
                    Add New User
                    <i class="entypo-plus-circled"></i>
                </a>
            </div>
        </div>
         {!!Session::get('SESSION_MESSAGE')!!}
         {{Session::forget('SESSION_MESSAGE')}}
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="subuser" class="table table-bordered table-condensed subusertbl">
                        <thead>
                            <tr>
                                <th style="width: 12%; word-wrap: break-word;">Full Name</th>
                                <th style="width: 15%; word-wrap: break-word;">Email</th>
                                <th style="width: 27%; word-wrap: break-word;">Permitted Modules</th>
                                <th style="width: 26%; word-wrap: break-word;">Permitted Locations</th>
                                <th style="width: 26%; word-wrap: break-word;">Permitted Campaigns</th>
                                <th style="width: 10%; word-wrap: break-word;">Date Added</th>
                                <th style="width: 10%; word-wrap: break-word;">Actions</th>
                            </tr>
                        </thead>					
                        <tbody>
                          {!! $markup !!}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {!! $footer !!}
    </div>
<!-- $this->loadViewFile("modules/dashboard/view/chat.php");-->
</div>
<div class="modal" id="subuser_alert_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{url('subuser/delete')}}">
                <div id="modal_body" class="modal-body" style="font-weight:bold; color: black;"></div>
                <div id="modal_footer" class="modal-footer text-center"></div>
            </form>
        </div>
    </div>
</div>

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/jquery.geocomplete.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.bootstrap.wizard.min.js'}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".delete_subuser").click(function () {
            var id = $(this).attr('id');
            $("#modal_title").html('Delete Confirmation');
            $("#modal_body").html('<i class="entypo-attention"></i> Are you sure you want to delete this user?');
            $("#modal_footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                    '<button type="submit" class="btn btn-red">Yes</button><input type="hidden" name="subuser_id" value="' + id + '"/>');
            $("#subuser_alert_modal").modal('show');
        });

        $("#subuser").DataTable({
            "processing": true,
            "serverSide": false,
            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "autoWidth": false,
            //"ajax": " //echo link_to("ajax/GenerateOverview") ",
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
                {"orderable": false, "targets": 5}
            ]
        });
    });
</script>

<link rel="stylesheet" href="{{$assets_dir.'/js/datatables/css/dataTables.bootstrap.css'}}">

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/datatables/js/jquery.dataTables.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/extensions/table_tools/js/dataTables.tableTools.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/js/dataTables.bootstrap.js'}}"></script>
@endsection