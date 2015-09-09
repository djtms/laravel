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
					<i class="fa fa-cog"></i> Modify User
				</h2>
			</div>
			<div class="col-sm-8 text-right"></div>
		</div>
		<br>
        {!! Session::get('SESSION_MESSAGE') !!} 
		{!! Session::forget('SESSION_MESSAGE') !!}
		<form id="modifyuser_search_frm" name="frm_search" action="" method="post">
			<div class="row">
				<div class="col-md-12 search-box">			
					<input type="text" id="search_value" name="search_value" value="{{$search_value}}" class="form-control input-lg" placeholder="Search using user first name, last name or email and hit ENTER">
				</div>
			</div>
		</form>
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Full Name</th>
								<th>Email Address</th>										
								<th>Active Plan</th>
								<th>Active Device</th>
								<th width="10%">Allowed Device</th>
							</tr>
						</thead>
						<tbody>						    
							@if(!isset($users->status))
							@foreach ($users as $user)
							<tr>
								<td>{{$user->full_name}}</td>
								<td>{{$user->email_address}}</td>
								<td>{{$user->plan_name}}</td>
								<td>{{$user->active_device}}</td>
								<td>
									<form name="" method="POST" action="{{url('admintools/modifyuser')}}">
									<div class="input-group">
									    <input type="hidden" name="act" value="update"/>
							      		<input type="text" class="form-control" name="allowed_quantity" value="{{$user->allowed_quantity}}" {{$user->plan_name == "" ? "disabled='disabled'" : ""}}/>
							      		<div class="input-group-btn"><button type="submit" class="btn btn-default" name="update" {{$user->plan_name == "" ? "disabled='disabled'" : ""}} data-toggle="tooltip" data-placement="left" data-original-title="Update user's allowed device quantity"><i class="fa fa-save"></i></button></div>
							      		<input type="hidden" name="hdn_email" value="{{$user->email_address}}">
							      		<input type="hidden" name="hdn_user_id" value="{{$user->id}}">
							      		<input type="hidden" name="hdn_old_qty" value="{{$user->allowed_quantity}}">
							    	</div>
									</form>
								</td>
							</tr>
							@endforeach
							@else
							<tr><td class="empty_row" colspan="5">{!! $users->message !!}</td></tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>		
      {!!  $footer !!}
    </div>
	<!-- /.main-content-->
</div>
@endsection