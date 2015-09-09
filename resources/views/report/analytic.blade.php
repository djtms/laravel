@extends('template.layout')
@section('content')
<div class="page-container">
	<!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
		<div id="pdf-content"></div>
		<div class="row">
			<div class="col-md-8">
				<h2 style="margin-top: 0px;">
					<i class="entypo-chart-pie"></i>Analytics
				</h2>
			</div>
			<div class="col-md-4 text-right">
				<a href="{{url('report/list')}}"
					class="btn btn-danger btn-lg btn-icon icon-left">Social Users List<i
					class="entypo-list"></i></a> 
					<a href="javascript:loadReportData();"
					class="btn btn-primary btn-lg btn-icon icon-left hidden-print">Export <i
					class="entypo-export"></i></a>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<form name="date_range" id="date_range" action="" method="post">
					<div class="row">                    	
						<div class="col-md-3">
							<select class="form-control select2" id="sort_by"
								name="sort-by" onchange="sort(this);">
								@foreach($sort_by as $key=>$val)
									<option value="{{$key}}" {{Request::has('sort-by') && Request::input('sort-by') == $key ? "selected='selected'" : ""}}>{{$val}}</option>
								@endforeach
							</select>
						</div>

						<div class="col-md-5">
							@if(Request::has('sort-by') && Request::input('sort-by') == 'device')							
							<select class="form-control select2 device hide" id="device_mac"
								name='device_mac' onchange="javascript:generate_report();">                                
                                	<option value='0'>-- Select Device --</option>
                                    @foreach($device_all as $device)
                                        <option {{Request::has('device_mac') && $device->mac_address == Request::input('device_mac') ? "selected='selected'" : ""}} value="{{$device->mac_address}}">{{$device->name}}</option>
                                    @endforeach                              
                            </select> 
                            @endif
                            @if(Request::has('sort-by') && Request::input('sort-by') == 'location')                           
                            <select class="form-control select2 location hide" id="location_id"
								name='location_id' onchange="javascript:generate_report();">								
                                	<option value='0'>-- Select Location --</option>
                                    @foreach($location_all as $location)
                                        <option	{{Request::has('location_id') && $location->id == Request::input('location_id') ? "selected='selected'" : ""}}
									value="{{$location->id}}">{{$location->name}}</option>
                                    @endforeach                                
                            </select>
                           @endif
						</div>

						<div class="col-md-4">
							<button id="reportrange" name="btn_rangepicker" type="submit" class="btn btn-default btn-lg">
								<i class="fa fa-calendar"></i> <span>{{Request::has('hdn_from_date') ? date('M d, Y', strtotime(Request::input('hdn_from_date'))) : date('M d, Y', strtotime("-7 day", time())) }} - {{ Request::has('hdn_to_date') ? date('M d, Y', strtotime(Request::input('hdn_to_date'))) : date('M d, Y')}}</span>
								<b class="caret"></b>
							</button>
							<input type="hidden" name="hdn_from_date" id="hdn_from_date" value="{{!Request::has('hdn_from_date') ? date('Y-m-d', strtotime("-7 day", time())) : date('Y-m-d', strtotime(Request::input('hdn_from_date')))}}" />
							<input type="hidden" name="hdn_to_date" id="hdn_to_date" value="{{!Request::has('hdn_to_date') ? date('Y-m-d') : date('Y-m-d', strtotime(Request::input('hdn_to_date'))) }}" />
							<input type="hidden" name="hdn_datediff" id="hdn_datediff" value="" />						
                        </div>
                    </div>
				</form>
			</div>
		</div>
		<br />
		<!---example end-->
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default" data-collapsed="0">
					<!-- to apply shadow add class "panel-shadow" -->
					<!-- panel head -->
					<div class="panel-heading sixth-tour">
						<div class="panel-title">
							<strong><i class="entypo-network"></i> Real-Time Stats</strong>
						</div>
					</div>

					<!-- panel body -->
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<div class="col-sm-6">
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-sm-6">
													<span class="pie-large" style="margin: 20px; display: inline-block;"></span>
												</div>

												<div class="col-sm-6"
													style="margin-top: 50px; margin-bottom: 5px;">
													<p>
														<span class="badge badge-roundless" style="background: #7F6084; color: #fff; font-size: 14px;">New Users</span>
													</p>
													<p>
														<span class="badge badge-roundless" style="background: #6DBCEB; color: #fff; font-size: 14px;">Returning Users</span>
													</p>
												</div>
											</div>
										</div>
										<div class="col-md-12 text-center">
											<h3>Active Users</h3>
										</div>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="row">
										<div class="col-xs-4 text-right" style="padding-right: 0;">
											<h3 style="margin-bottom: 0; color: #fe9193;">{{$user['total_online_uesrs']}}</h3>
										</div>

										<div class="col-xs-8" style="padding: 0 5px;">
											<h4 style="margin-top: 21px; margin-bottom: 0;">Users Online Now</h4>
										</div>
									</div>

									<div class="row">
										<div class="col-xs-4 text-right" style="padding-right: 0;">
											<h3 style="margin-bottom: 0; color: #ee4749;">{{ $user['total_user']}}</h3>
										</div>

										<div class="col-xs-8" style="padding: 0 5px;">
											<h4 style="margin-top: 21px; margin-bottom: 0;">Total Users</h4>
										</div>
									</div>

									<div class="row">
										<div class="col-xs-4 text-right" style="padding-right: 0;">
											<h3 style="margin-bottom: 0; color: #c13638;">{{$user['avg']}}</h3>
										</div>

										<div class="col-xs-8" style="padding: 0 5px;">
											<h4 style="margin-top: 21px; margin-bottom: 0;">Avg. Usages
												Time</h4>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div style="margin-right: 15px;">
								<ul class="nav nav-tabs bordered">
									<!-- available classes "bordered", "right-aligned" -->
									<li class="active"><a href="#online" data-toggle="tab">Users
											Online Now</a></li>
									<li><a href="#return" data-toggle="tab">Top Returning Users</a></li>
								</ul>

								<div class="tab-content">
									<div class="tab-pane active" id="online">
										<div class="scrollable" data-height="150">
											<div class="row">
											{!! $user['online_uesrs'] !!}
											</div>
										</div>
									</div>

									<div class="tab-pane" id="return">
										<div class="scrollable" data-height="150">
											<div class="row">
											{!! $user['returning_user'] !!}
											</div>
										</div>
									</div>
								</div>

							</div>
							<!----Row ---->
						</div>
					</div>
				</div>
			</div>
		</div>

		<br />

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default" data-collapsed="0">
					<!-- to apply shadow add class "panel-shadow" -->
					<!-- panel head -->
					<div class="panel-heading">
						<div class="panel-title">
							<strong><i class="entypo-network"></i> Social Connections</strong>
						</div>
					</div>

					<!-- panel body -->
					<div class="row" style="padding-top: 15px; padding-bottom:15px;">
						<div class="col-md-5">
							<div class="row">
								<div class="col-sm-7" style="padding: 0;">
									<div id="donut_social_statistics" class="morrischart" style="height: 200px;"></div>
								</div>
								<div class="col-sm-4">
									<div class="row">
										<i class="fa fa-facebook-square"></i>
										<div class="pull-right text-right" style="margin-right: 10px">
											<span style="color: #3b5998; font-size: 18px;">{{$statistics['fbuser']}} Users</span>
										</div>
									</div>

									<div class="row">
										<i class="fa fa-twitter-square"></i>
										<div class="pull-right text-right" style="margin-right: 10px">
											<span style="color: #55acee; font-size: 18px;">{{$statistics['twuser']}} Users</span>
										</div>
									</div>

									<div class="row">
										<i class="fa fa-linkedin-square"></i>
										<div class="pull-right text-right" style="margin-right: 10px">
											<span style="color: #0976b4; font-size: 18px;">{{$statistics['liuser']}} Users</span>
										</div>
									</div>

									<div class="row">
										<i class="fa fa-google-plus-square"></i>
										<div class="pull-right text-right" style="margin-right: 10px">
											<span style="color: #dd4b39; font-size: 18px;">{{$statistics['gpuser']}} Users</span>
										</div>
									</div>
									
									<div class="row">
										<i class="fa fa-instagram"></i>
										<div class="pull-right text-right" style="margin-right: 10px">
											<span style="color: #3f729b; font-size: 18px;">{{$statistics['iguser']}} Users</span>
										</div>
									</div>

									<div class="row">
										<i class="fa fa-envelope-square"></i>
										<div class="pull-right text-right" style="margin-right: 10px">
											<span style="color: #F7931E; font-size: 18px;">{{$statistics['cuser']}} Users</span>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-7">
						<div id="social_statistics" class="morrischart" style="height: 200px"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<br />

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default" data-collapsed="0">
					<!-- to apply shadow add class "panel-shadow" -->
					<!-- panel head -->
					<div class="panel-heading">
						<div class="panel-title">
							<strong><i class="entypo-network"></i> Demographics</strong>
						</div>
					</div>

					<!-- panel body -->
					<div class="row" style="margin: 0;">
						<div class="col-sm-3">
							<div class="row">
								<div class="col-xs-4">
									<i class="fa fa-male"
										style="font-size: 80px; color: #47639E; margin-top: 18px;"></i>
								</div>
								<div class="col-xs-8">
									<div class="row">
										<div class="col-md-12">
											<h3 style="margin-bottom: 0;">Men</h3>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6" style="color: #47639E;">
											<h4 style="margin-bottom: 0; color: #47639E;">{{$fb_male_female['total_fb_male']}}</h4>
											<p>All Facebook</p>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-4">
									<i class="fa fa-female"
										style="font-size: 80px; color: #E4007D; margin-top: 18px;"></i>
								</div>
								<div class="col-xs-8">
									<div class="row">
										<div class="col-md-12">
											<h3 style="margin-bottom: 0;">Women</h3>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6" style="color: #E4007D;">
											<h4 style="margin-bottom: 0; color: #E4007D;">{{$fb_male_female['total_fb_female']}}</h4>
											<p>All Facebook</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-9">
							<div id="chart" style="height: 250px"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<!-- panel head -->
					<div class="panel-heading">
						<div class="panel-title">
							<strong><i class="entypo-network"></i> Campaign Statistics</strong>
						</div>
					</div>

					<!-- panel body -->
					<div class="row">
						<div class="col-md-12">
							<div id="campaign_statistics" style="height: 250px"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@if(Session::get('USER_TYPE') && Session::get('USER_TYPE') != '3')		
        {!!$reset_option !!}
		@endif
		
		{!! $footer !!}
    </div>
	<!--End Main container-->
</div>
<div class="modal fade custom-width" id="social_data_modal">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><span>Showing User ({{Request::has('hdn_from_date') ? date('M d, Y', strtotime(Request::input('hdn_from_date'))) : date('M d, Y', strtotime("-7 day", time())) }} - {{ Request::has('hdn_to_date') ? date('M d, Y', strtotime(Request::input('hdn_to_date'))) : date('M d, Y') }})</span></h3>
			</div>

			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered datatable dataTable" id="social_data">
						<thead>
							<tr class="replace-inputs">
								<th>Name</th>
								<th>Email</th>
								<th>Gender</th>
								<th>Location</th>
								<th>Social Network</th>
								<th>Added Datetime</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    	var currentdatetime = new Date();
        console.log(currentdatetime);
        if ($("#reportrange").length > 0) {
            $('#reportrange').daterangepicker(
                    {
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                            'Last 7 Days': [moment().subtract('days', 6), moment()],
                            'Last 30 Days': [moment().subtract('days', 29), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                        },
                        opens: 'left',
                        format: 'YYYY-MM-DD',
                        startDate: moment().subtract('days', 6),
                        endDate: moment()
                    },
            function (start, end) {
                $('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
                $("#hdn_from_date").val(start.format('YYYY-MM-D'));
                $("#hdn_to_date").val(end.format('YYYY-MM-D'));
                $("#report_modal_title").text(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
                $("#hdn_datediff").val(Math.ceil((Date.parse(end) - Date.parse(start)) / (1000 * 60 * 60 * 24)) - 1);
                $("#date_range").submit();
            }
            );
        }

        // Sparkline Charts
        $(".pie-large").sparkline([{{$user['new_user']}}, {{$user['total_returning_user']}}], {
            type: 'pie',
            width: '100px',
            height: '100px',
            sliceColors: ['#7F6084', '#6DBCEB']
        });

        //Lin Chart
        Morris.Line({
            element: 'social_statistics',
            data: [{!!$statistics['graph_data'] != ""? $statistics['graph_data'] :""!!}],
            xkey: 'y',
            ykeys: ['a', 'b', 'c', 'd', 'e', 'f'],
            labels: ['Facebook', 'Twitter', 'LinkedIn', 'Google+', 'Instagram', 'Custom User'],
            lineColors: ['#3b5998', '#55acee', '#0976b4', '#dd4b39', '#3f729b', '#F7931E'],
            pointFillColors: ['#3b5998', '#55acee', '#0976b4', '#dd4b39', '#3f729b', '#F7931E'],
        	resize: true
		});

        // Bar Charts
        Morris.Bar({
            element: 'chart',
            axes: true,
            data: [{!! $fb_male_female['total_male_female_graph_data'] != "" ? $fb_male_female['total_male_female_graph_data'] :""!!}],
            xkey: 'x',
            ykeys: ['a', 'b'],
            labels: ['All Men', 'All Women'],
            barColors: ['#47639E', '#E4007D']
        });


        // Donut Chart
        Morris.Donut({
            element: 'donut_social_statistics',
            data: [
                {label: "Facebook", value: {{ $statistics['fbuser']}} },
                {label: "Twitter", value: {{ $statistics['twuser']}} },
                {label: "Linkedin", value: {{$statistics['liuser']}} },
                {label: "Google+", value:{{ $statistics['gpuser']}} },
                {label: "Instagram", value:{{$statistics['iguser']}} },
                {label: "Custom", value: {{$statistics['cuser']}}}
            ],
            colors: ['#3b5998', '#55acee', '#0976b4', '#dd4b39', '#0976b4', '#F7931E']
        });

	     //Bar Charts
        Morris.Bar({
			element: 'campaign_statistics',
			data: [{!! $campaign_statistics !!}],
			xkey: 'x',
			ykeys: ['y', 'z', 'a'],
			labels: ['Total Page View', 'Unique Visitors', 'Returning Visitors'],
			stacked: true,
			barColors: ['#5DA5DA', '#B2912F', '#307D99']
		});

        if ($("#sort_by").val() === 'location') {
            $("select.location").removeClass("hide");
            $("select.device").addClass("hide");
        }
        if ($("#sort_by").val() === 'device') {
            $("select.device").removeClass("hide");
            $("select.location").addClass("hide");
        }

        $("#tbl_campaign_statistics").dataTable();
        
    });


    function sort(id) {
        if (id.value === 'device') {
            $("select.device").removeClass("hide");
            $("select.location").addClass("hide");
        }

        if (id.value === 'location') {
            $("select.location").removeClass("hide");
            $("select.device").addClass("hide");
        }

        generate_report();
    }
    $(function () {
        eval($('#code').text());
        prettyPrint();
    });

    function generate_report() {
        $("#date_range").submit();
    }

    function loadReportData() {
        $("#preloader").removeClass('hide');
        var start_date = $("#hdn_from_date").val();
        var end_date = $("#hdn_to_date").val();
        var sort_by = $("#sort_by").val();
        var location_id = $("#location_id").val();
        var device_mac = $("#device_mac").val();
        $.ajax({
            url:'{{url("ajax/loadreportdata")}}',
            type:'POST',
            data:{start_date:start_date, end_date:end_date, sort_by:sort_by, location_id:location_id,device_mac:device_mac},
            success:function(result){
            	$("#preloader").addClass('hide');
                $("#social_data tbody").append(result);
                $("#social_data").dataTable({
                	"dom": "<'row'<'col-xs-6 col-left'l><'col-xs-6 col-right'<'export-data'T>f>r>t<'row'<'col-xs-6 col-left'i><'col-xs-6 col-right'p>>",
                    "tableTools": {
                        "sSwfPath": "{{$assets_dir.'/js/datatables/extensions/table_tools/swf/copy_csv_xls_pdf.swf'}}"
                    },
                    "columnDefs": [
                        {"orderable": false, "targets": 2},
                        {"orderable": false, "targets": 3},
                        {"orderable": false, "targets": 4},
                        {"orderable": false, "targets": 5}
                    ],
                    "bDestroy": true
                });
                $("#social_data_modal").modal('show');
            }
        });
    }

</script>
<link rel="stylesheet" href="{{$assets_dir.'/js/daterangepicker/daterangepicker-bs3.css'}}">
<link rel="stylesheet" href="{{$assets_dir.'/js/datatables/css/dataTables.bootstrap.css'}}">

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/datatables/js/jquery.dataTables.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/extensions/table_tools/js/dataTables.tableTools.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/js/dataTables.bootstrap.js'}}"></script>
@endsection
