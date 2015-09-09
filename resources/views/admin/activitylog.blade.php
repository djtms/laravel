@extends('template.layout')
@section('content')
<div class="page-container">	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
         @include('template.headermenu')
        <hr />
		<div class="row">
			<div class="col-sm-4">
				<h2 style="margin-top: 0px;" class="last-tour">
					<i class="fa fa-files-o"></i> Activity Log
				</h2>
			</div>
			<div class="col-sm-8 text-right"></div>
		</div>
		<br>
		{!! Session::get('SESSION_MESSAGE') !!}
		{{ Session::forget('SESSION_MESSAGE')}}		
		<div class="row">
			<form id="activitylog_search_frm" name="frm_search" action="" method="post">
				<div class="col-md-4">
					<div class="form-group">
						<input type="text" name="search_value" value="{{$search_value}}" class="form-control input-lg" placeholder="Your MAC address here" />
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<select name="operator" class="form-control input-lg">
							<option {{$operator == '=' ? "selected='selected'" : ""}} value="=">=</option>
							<option {{$operator == 'LIKE%%' ? "selected='selected'" : ""}} value="LIKE%%">LIKE%%</option>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<select name="order" class="form-control input-lg">
							<option {{$order == 'ASC' ? "selected='selected'" : ""}} value="ASC">Ascending</option>
							<option {{$order == 'DESC' ? "selected='selected'" : ""}} value="DESC">Descending</option>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<input type="text" name="limit" value="{{$limit}}" class="form-control input-lg" />
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<button class="btn btn-default btn-lg btn-block">
							<i class="fa fa-search"></i> Search
						</button>
					</div>
				</div>
			</form>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Device Detail</h4>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>MAC Address</th>
										<th>Device Name</th>										
										<th>Location (NASID)</th>
										<th>Owner's Name</th>
										<th>Owner's Email Address</th>
										<th>Created @</th>
									</tr>
								</thead>
								<tbody>									   						    
									@if(!isset($device_info->status))									
										@foreach ($device_info as $value)
										<tr>
											<td>{{$value->mac_address}}</td>
											<td>{{$value->device_name}}</td>
											<td><a target="_blank" href="{{url('location/overview?loca='.$value->location_id)}}">{{$value->location_name.' ('.$value->identifier.')'}}</a></td>
											<td>{{$value->full_name}}</td>
											<td>{{$value->email_address}}</td>
											<td>{{$value->create_date}}</td>										
										</tr>
										@endforeach
									@else
									<tr><td class="empty_row" colspan="6">{!! $device_info->message!!}</td></tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Device Status Detail</h4>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th style="width: 25%">MAC</th>
										<th>Request Detail</th>
									</tr>
								</thead>
								<tbody>
									@if(!isset($device_status_info->status))
									@foreach ($device_status_info as $value)
									<tr>
										<td style="text-align: left;">
											<strong>MAC: </strong> {{$value->mac}}<br>
											<strong>MAC1: </strong> {{$value->mac1}}<br>
											<strong>MAC2: </strong> {{$value->mac2}}<br>
											<strong>MAC3: </strong> {{$value->mac3}}<br>
											<strong>MAC4: </strong> {{$value->mac4}}<br><br>
											<strong>Last Seen Online: </strong> {{$value->status_created_on}}
										</td>
										<td>{{$value->device_status_details}}</td>										
									</tr>
									@endforeach
									@else
									<tr><td class="empty_row" colspan="6">{!! $device_status_info->message!!}</td></tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Activity Log (Radius Server)</h4>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered" style="table-layout: fixed;">
								<thead>
									<tr>
										<th>Request Detail</th>
										<th style="width: 15%;">Created @</th>
									</tr>
								</thead>
								<tbody>
									@if(!isset($radius_device_info->status))
									@foreach ($radius_device_info as $value)
									<tr>
									<td style="word-break:break-word;">{{$value->info_details}}</td>
									<td>{{$value->created_on}}</td>
									</tr>	
									@endforeach
									@else
									<tr><td class="empty_row" colspan="2">{{$radius_device_info->message}}</td></tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Landing Page Statistics</h4>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Campaign</th>
										<th>Location</th>
										<th>Plan</th>
										<th>OS</th>
										<th>Model</th>
										<th>Client Mac</th>
										<th>Created @</th>
									</tr>
								</thead>
								<tbody>
									@if(!isset($social_user_device_info->status))
									@foreach ($social_user_device_info as $value)
									<tr>
										<td>{{$value->campaign}}</td>
										<td>{{$value->location}}</td>
										<td>{{$value->plan}}</td>
										<td>{!!$value->oslogo!!}</td>
										<td>{{$value->model}}</td>
										<td>{{$value->client_mac}}</td>
										<td>{{$value->created_at}}</td>										
									</tr>
									@endforeach
									@else
									<tr><td class="empty_row" colspan="7">{!! $social_user_device_info->message !!}</td></tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
        {!! $footer !!}
    </div>
	<!-- /.main-content-->
</div>
@endsection