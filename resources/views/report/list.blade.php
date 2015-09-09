@extends('template.layout')
@section('content')
<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
        <div class="row">
            <div class="col-md-4"><h2 style="margin-top:0px;"><i class="entypo-list"></i>Social Users</h2></div>			
            <div class="col-md-8 text-right">
                <a href="{{url('report/analytic')}}" class="btn btn-danger btn-lg btn-icon icon-left">Analytics Report<i class="entypo-chart-pie"></i></a>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered datatable dataTable" id="tbl-social-users">
                        <thead>
                            <tr class="replace-inputs">
                                <th width="7%">Media</th>
                                <th>Name</th>
                                <th width="15%">Email</th>
                                <th width="7%">Gender</th>
                                <th width="10%">Campaign</th>
                                <th width="15%">Location</th>                                
                                <th width="6%">Visits</th>
                                <th>Device OS</th>
                                <th>Device Type</th>
                                <th width="10%">Connected</th>
                            </tr>
                        </thead>

                        <tbody>                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {!!  $footer !!}
    </div><!--End Main container-->
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#tbl-social-users').dataTable({
            "processing": true,
            "serverSide": true,
            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "autoWidth": false,
            "ajax": "{{url("ajax/GetSocialUsers")}}",
            "language": {
                "sEmptyTable": "<span class='text-center empty_row'>No data available in table</span>",
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
                {"orderable": false, "targets": 0},
                {"orderable": false, "targets": 3},
                {"orderable": false, "targets": 6},
                {"orderable": false, "targets": 7},
                {"orderable": false, "targets": 8}
            ],
			"aoColumns": [
				{ "sClass": "text-center" },
				null,
				null,
				{ "sClass": "text-center" },
				null,
				null,
				{ "sClass": "text-center" },
				null,
				null,
				null
			]
        });

    });/***************END DOCUMENT****************/

    

</script>
<style>
	#tbl-social-users a {
		color:#3a5795!important;
	}
	
    .social_icon{
        font-size:21px;
    }
	
	.sorting_1 .entypo-mail {
		margin-left:6px!important;
	}
</style>

<link rel="stylesheet" href="{{$assets_dir.'/js/datatables/css/dataTables.bootstrap.css'}}">

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/datatables/js/jquery.dataTables.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/extensions/table_tools/js/dataTables.tableTools.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/js/dataTables.bootstrap.js'}}"></script>
@endsection