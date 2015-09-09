@extends('template.layout')

@section('content')
<div class="page-container">
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')        
        <hr />
        {!! Session::get('SESSION_MESSAGE') !!}
        {{Session::forget('SESSION_MESSAGE')}}
        <div class="row">
            <div id="my_tab" class="col-xs-12">
                <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                    <li class="{{$tab == 'overview' ? 'active' : '' }}"><a href="#overview" data-toggle="tab">Overview</a></li>
                    <li class="{{$tab == 'campaigns' ? 'active' : ''}}"><a href="#campaigns" data-toggle="tab">Campaigns</a></li>
                    <li class="{{$tab == 'hardware' ? 'active' : '' }}"><a href="#hardware" data-toggle="tab">Devices</a></li>
					@if(Session::get('USER_TYPE')!= '3')
                    <li class="{{$tab == 'options' ? 'active' : '' }}"><a href="#options" data-toggle="tab">Options</a></li>
                    <li class="{{$tab == 'users' ? 'active' : '' }}"><a href="#users" data-toggle="tab">Sub Users</a></li>
					@endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane {{$tab == 'overview' ? 'active' : '' }}" id="overview">                        
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="panel" >
                                    <div class="panel-heading">
                                        <div class="panel-title col-sm-8" style="padding-top:0px;">
                                            <h3>{{$location->name}}</h3>
                                            <p>{{$location->address}}</p>
                                        </div>

                                        <div class='col-sm-4 text-right'>
                                            <button type="button" class="btn btn-blue btn-icon btn-sm icon-left" id="edit" onclick="javascript:$('#edit_location').modal('show', {backdrop: 'static'});">
                                                Edit
                                                <i class="entypo-pencil"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class='col-sm-4'>                                                    
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" >                                                    
                                                    <div class="map_canvas_{{$loca}}" style="height: 330px; width: 100%; margin-top:10px"></div>
                                                    <input id="blue-water-a{{$loca}}" type="hidden" value="{{$location->location}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.col-4 -->

                            <div class="col-sm-6 col-lg-5">

                                <div class="panel panel-primary">
                                    <!-- panel head -->
                                    <div class="panel-heading">
                                        <div class="panel-title"><h3><i class="entypo-users"></i> Users Online Now</h3></div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                           {!! $online_user !!}
                                        </div>
                                    </div><!-- panel-body -->
                                </div><!-- panel -->

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-xs-4 col-sm-4" style="text-align:center;">
                                                <i class="fa fa-male" style="font-size: 65px; color: #47639E;"></i>
                                            </div>
                                            <div class="col-xs-8 col-sm-8">
                                                <h3 style="margin-top:0;">{{$male}}</h3>
                                                <p>Male WIFI users.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-xs-4 col-sm-4" style="text-align:center;">
                                                <i class="fa fa-female" style="font-size: 65px; color: #E4007D;"></i>
                                            </div>
                                            <div class="col-xs-8 col-sm-8">
                                                <h3 style="margin-top:0;">{{$female}}</h3>
                                                <p>Female WIFI users.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="donut-chart-demo" class="morrischart" style="height:100px;"></div>												
                                    </div>
                                </div>

                                <div class="row text-center">
                                    <div class="col-md-12">
                                        <a href="{{url('report/analytic')}}">
                                            <button type="button" class="btn btn-green btn-icon btn-lg icon-left" style="margin-bottom:10px;">
                                                More Analytics
                                                <i class="entypo-chart-pie"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="row landing-page">
                                    <h3>Landing Page Preview</h3>
                                </div>

                                <div class="row">
                                    <div class="backgroundiphone" >
                                        <div class="backgroundiphone-wrapper">
                                            <div class="backgroundiphone-content">
                                                <iframe src="{{url('landingpage/viewlandingpage?nasid=' . $location->identifier.'&called='.$device_mac)}}"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row text-center">
                                    <div class="col-md-12">
                                        <a id="edit_campaign" data-toggle="tab" class="btn btn-green btn-icon btn-lg icon-left" href="{{Request::server('REQUEST_URI')}}#campaigns">Edit Campaign <i class="entypo-pencil"></i></a>
                                    </div>
                                </div>	
                            </div>                          
                        </div>	
                    </div><!-- /#overview -->
                    <div class="tab-pane {{$tab == 'campaigns' ? 'active' : '' }}" id="campaigns">
                        <form id="rootwizard" method="post" action="" class="form-horizontal form-wizard validate" style="margin-top: 0px;">
                            <div class="row">
                                <div class="col-lg-4 campaigns-part">
                                    <h3 class="control-label" style="text-align:left;">Choose Default Campaign</h3>
                                </div>
                                <div class="col-xs-7 col-sm-4 col-md-4 col-lg-3 campaigns-part">
                                    <select id="ddl_default_campaign" class="form-control select2">
                                        <option value="">Choose Default Campaign</option>                                       
                                        @if($campaign)
                                            {{--*/$clg_id = 0/*--}}
                                            @if ($campaign_loca_status)                                                
                                                {{--*/$clg_id = $campaign_loca_status->id/*--}}
                                            @endif
                                            @foreach ($campaign as $row)
                                                @if($clg_id == $row->id)
                                                    <option selected='selected' value="{{$row->id}}">{{$row->name}}</option>
                                                @else
                                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                                @endif
                                            @endforeach                                       
                                        @endif
                                    </select>
                                    <input type="hidden" id="hdn_default_campaign" value="{{$clg_id}}"/>
                                </div>

                                <div class="col-xs-5 col-sm-3 col-md-2 campaigns-part">
                                    <a id="btnSetDefault" href="javascript:setDefaultCampaign({{$loca}});" class="btn btn-info btn-lg">Set as Default</a>
                                </div>
                                <div class="col-sm-5 col-md-6 col-lg-3 campaigns-part">
                                    <a id="btn_show_schedule_modal" href="javascript:void(0);" class="btn btn-green btn-icon btn-lg icon-left pull-right"><i class="entypo-plus-circled"></i> Schedule New Campaign</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-md-12'>
                                    <h3>
                                        Active Campaign:
                                        <b>											
                                           @if ($campaign_name != "Unknown")
                                               {!! $campaign_name !!}
                                           @else
                                                <span style='color:red'>Empty</span>
                                            @endif
                                        </b>
                                    </h3>
                                </div>
                            </div>
                            <div class="row">								
                                <div class='col-md-12'>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3><label class="control-label" for="full_name">Scheduled Campaign</label></h3>
                                        </div>
                                    </div>									
                                    <div class='row'>	
                                        <div class='col-md-12 table-responsive'>
                                            <table class="table table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th>Campaign</th>
                                                        <th>Repeat Type</th>
                                                        <th width="40%">Duration</th>
                                                        <th>Repeat Data</th>
                                                        <th>Until</th>
                                                        <th width="10%">Actions</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                @if(count($scheduledCampaign) > 0)                                                
	                                                @foreach ($scheduledCampaign as $schedule)
	                                                <tr>
	                                                	<td>{{$schedule['campaign_name']}}</td>
	                                                    <td>{{ucfirst(str_replace('_', ' ', $schedule['repeat_type']))}}</td>
	                                                    <td>{{$schedule['start_date'] . " - " . $schedule['end_date']}}</td>
	                                                    <td>{{$schedule['repeat_data']}}</td>
	                                                    <td>{{$schedule['repeat_until']}}</td>
	                                                    <td>
	                                                    	<a href="javascript:edit_schedule({{$schedule['id'] }});" class="btn btn-blue btn-sm" style="margin-top:5px;" ><i class="fa fa-pencil"></i></a>
	                                                        <a href="javascript:load_delete_schedule_popup({{$schedule['id']}});" style="margin-top:5px;" class="btn btn-red btn-sm"><i class="fa fa-trash-o"></i></a>
	                                                    </td>
	                                                </tr>
	                                                @endforeach
                                                @else
                                                <tr><td class="text-center" style="font-weight: bold; color: red;" colspan="6">No Campaign(s) Found in Scheduler.</td></tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                         
									<div class="calendar-env">
									<!-- Calendar Body -->
									<div class="calendar-body">
									<div id="calendar"></div>
									</div>
									</div>                                                           
                                </div>
                            </div>
                        </form>
                    </div><!-- /#campaigns -->
                    
                    <div class="tab-pane {{$tab == 'hardware' ? 'active' : '' }}" id="hardware">
						{{$device_limit_msg}}
                        @if (Session::get('USER_TYPE') != '3')
                            <div class="row text-right">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-red btn-icon icon-left btn-lg" style="margin-bottom:10px;" onclick="javascript:$('#choose_hardware').modal('show', {backdrop: 'static'});">
                                        Assign Device
                                        <i class="entypo-retweet"></i>
                                    </button>                                   
                                </div>
                            </div>
                        @endif
                        <br/>
                        <div id="my_devices" class="row">                          
                            @if (count($device_rows) > 0)
                                @foreach($device_rows as $device_row)
                                  {{--*/$selected_device[] = $device_row->id/*--}}
                                  {{--*/$last_update = "Unknown"/*--}}
                                  {{--*/$wan_ip = "Unknown"/*--}}
                                  {{--*/$lan_ip = "Unknown"/*--}}
                                  {{--*/$ssid = "Unknown"/*--}}
                                    @if ($device_row->device_status != "")
                                        {{--*/$device_status = json_decode($device_row->device_status)/*--}}
                                        {{--*/$ssid = $device_status->ssid ? $device_status->ssid : "Unknown"/*--}}
                                        {{--*/$lan_ip = $device_status->lan ? $device_status->lan : "Unknown"/*--}}
                                        {{--*/$wan_ip = $device_status->wan ? $device_status->wan : "Unknown"/*--}}
                                    @endif
                                    <div id="device_{{$device_row->id}}" class="col-sm-6 col-lg-4 devices">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <div class="panel-title">{{$device_row->name}}</div>
                                            </div>

                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-xs-8">
                                                        <p>Current SSID: {{$ssid}}</p>
                                                        <p>Mac Address: {{$device_row->mac_address}}</p>
                                                        <p>WAN IP: {{$wan_ip}}</p>
                                                        <p>LAN IP: {{$lan_ip}}</p>
                                                    </div>

                                                    <div class="col-xs-4">
                                                        <img style="width:100%;" src="{{$device_details[$device_row->model]['image'] }}" /> 
                                                    </div>
                                                </div>

                                                <div style="margin-top:15px;" class="row">
                                                    <div class="col-xs-8">
                                                        <a class="btn btn-red btn-icon icon-left btn-sm" href="javascript:Remove({{$device_row->id }});"><i class="fa fa-trash-o"></i>Remove</a>
                                                    </div>

                                                    <div id="edit-device" class="col-xs-4 text-right">
                                                        <a class="btn btn-blue btn-icon icon-left btn-sm" href="javascript:edit_device({{$device_row->id}});"><i class="fa fa-pencil"></i>Edit <img id="al_edit_device_{{$device_row->id}}" class="hide" src="{{url('themes/neon/assets/images/ajax-loader.gif')}}"/></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 @endforeach
                            @else                               
                                <div class="col-md-12">
                                    <div class="alert alert-warning"><strong><i class="entypo-attention"></i> No device(s) has assigned.</strong></div>
                                </div>
                            @endif
                        </div>
                    </div>	<!-- /#hardware -->
                    
					@if(Session::get('USER_TYPE') != '3')
                    <div class="tab-pane {{$tab == 'options' ? 'active' : '' }}" id="options">
                        <form class="" action="{{url('location/addlocationoptions?loca='. $loca)}}" method="POST">                           
                            <div class="row">
                                <div class="col-md-6">
	                                <div class="row">
	                                	<div class="col-md-10 col-md-offset-1">
	                                		<div class="form-group">
		                                        <div id="slider">
		                                            <label class="control-label">
		                                                <h4>
		                                                    Maximum Bandwidth Per User <input class="boots-switch" data-size="mini" data-on-color="success" id="chk_no_limit" value="25600" type="checkbox" {{$max_bandwidth == '' || $max_bandwidth == '25600' ? "checked='checked'" : "" }} >
		                                                    <label class="control-label" for="auto_login">Set no limit</label>
		                                                    <i data-original-title="Tooltip on top" title="Some help text here." data-placement="top" data-toggle="tooltip" class="entypo-help-circled"></i>
		                                                </h4>
		                                            </label>
		                                            <div id="max_bandwidth_slider" class="slider slider-blue" data-step="64" data-min="64" data-max="25600" data-postfix=" kbps" data-value="{{$max_bandwidth}}" data-fill="#max_bandwidth"></div>
		                                            <input type="hidden" id="max_bandwidth" name="max_bandwidth" value="{{$max_bandwidth}}"/>
		                                        </div>
		                                    </div>
	                                	</div>
	                                </div>                                    
                                </div>
                                <div class="col-md-6">
	                                <div class="row">
	                                	<div class="col-md-10 col-md-offset-1">
		                                	<div class="form-group">
		                                        <label class="control-label" style="padding-bottom: 5px;"><h4>Session time limit <i data-original-title="Tooltip on top" title="Some help text here." data-placement="top" data-toggle="tooltip" class="entypo-help-circled"></i></h4></label>
		                                        <div class="slider slider-red" data-step="1" data-min="1" data-max="24" data-postfix=" hours" data-value="{{$session_time_limit/3600}}" data-fill="#session_time_limit"></div>
		                                        <input type="hidden" id="session_time_limit" name="session_time_limit" value="{{$session_time_limit/3600}}"/>
		                                    </div>
	                                	</div>
	                                </div>
                                    
                                </div>
                            </div>                           
                            <div class="row">
                                <div class="col-sm-3 col-sm-offset-9 text-right">
                                    <button type="submit" class="btn btn-green btn-icon icon-left">
                                        Save
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div><!-- /#options -->
                    <div class="tab-pane {{$tab == 'users' ? 'active' : '' }}" id="users">
                        <div class="row">
                            <div class="col-md-7">
                                <h3>Current Users</h3>
                            </div>

                            <div class="col-md-5 text-right" >
                                <button type="button" class="btn btn-red btn-icon icon-left btn-lg" style="margin-bottom:10px;" onclick="javascript:load_assign_sub_user_modal();">
                                    Assign Sub User
                                    <i class="entypo-retweet"></i>
                                </button>
                                @if(Session::get('USER_TYPE') != '3')
                                    <button type="button" class="btn btn-green btn-icon icon-left btn-lg" style="margin-bottom:10px;" onclick="javascript:showSubUserForm();">
                                        Add Sub User
                                        <i class="entypo-plus-circled"></i>
                                    </button>
                               @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="width: 12%; word-wrap: break-word;">Full Name</th>
                                                <th style="width: 15%; word-wrap: break-word;">Email</th>
                                                <th style="width: 10%; word-wrap: break-word;">Actions</th>
                                            </tr>
                                        </thead>					
                                        <tbody>                                            
                                            @if ($users)
                                                @foreach($users as $user)                                                    
                                                    <tr>
                                                        <td style="width: 12%; word-wrap: break-word;">{{$user->full_name}}</td>
                                                        <td style="width: 15%; word-wrap: break-word;">{{$user->email_address}}</td>
                                                        <td style="width: 10%; word-wrap: break-word;">
                                                            <a href="{{url('location/removeuserfromlocation?uid='.$user->id.'&lid='.$loca)}}" class="btn btn-danger btn-sm" title="Remove this user."><i class="entypo-trash"></i></a>
                                                        </td>
                                                    </tr>                                                   
                                                @endforeach
                                            @else
                                                <tr><td colspan="6" class="text-center" style="font-weight: bold; color: red;">No user(s) found!</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>				
				@endif
			</div>
          </div>
       </div>

      {!! $footer !!}
   </div><!--Main content end-->    
</div>
<!-- Modal 6 (Long Modal)-->
 
<div class="modal" id="edit_location">
    <div class="modal-dialog" style="width: 65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Edit Location</h3>
            </div>

            <form id="rootwizard-2" autocomplete="off" method="post" action="{{url('location/update')}}" class="form-wizard validate">
                <div class="modal-body">                    
                    <div class="map_new row">
                        <div class="col-md-12">
                            <div class="panel-title" style="padding-right: 0; padding-bottom: 0;">
                                <h4>{{$location->name}}</h4>
                                <p>{{$location->address}}</p>
                            </div>
                            <div class="form-group">                                
                                <img src="https://maps.googleapis.com/maps/api/staticmap?center={{$location->location}}&zoom=8&size=600x100&scale=2&markers=size:mid%7Ccolor:red%7C{{$location->address}}" class="img-responsive img-thumbnail">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="company">Company Name</label>
                                <input type="text" class="form-control" name="name" data-validate="required" placeholder="Name" value="{{$location->name}}" />
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="company">Location (Latitude,Longitude)</label>
                                <input type="text" class="form-control" readonly="readonly"  name="location" data-validate="required" placeholder="Location" value="{{$location->location}}" />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="company">Formatted Address</label>
                                <input type="text" class="form-control" name="formatted_address" data-validate="required" placeholder="Formatted Address" value="{{$location->address}}" />
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="company">Country</label>
                                <input type="text" class="form-control" value="{{$location->country}}" name="country" placeholder="Country" />
                                <input type="hidden" class="form-control" name="country_short" placeholder="Country Code" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="company">State/Province</label>
                                <input type="text" class="form-control" value="{{$location->state}}" name="administrative_area_level_1" placeholder="State/Province" />
                            </div>
                        </div>
                    </div>
                    <div class="row">      

                        <div class="col-sm-6">
                            <div class="form-group"> 
                                <label class="control-label" for="company">Phone Number</label>
                                <input type="text" class="form-control" value="{{$location->phone_number}}" name="international_phone_number" data-validate="required" placeholder="International Phone Number" />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group"> 
                                <label class="control-label" for="location">Time Zone</label>                                
                                <select id="time_zone" name="time_zone" class="form-control">
                                    @foreach ($time_zones as $key => $value)
                                        <option value="{{$key}}" {{$location->time_zone == $key ? "selected='selected'" : "" }} >{{$value}}</option>
                                   @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">                         
                        <div class="col-sm-6">
                            <div class="form-group">  
                                <label class="control-label" for="company">URL</label>
                                <input type="text" class="form-control" name="url" placeholder="URL" value="{{$location->url}}" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="company">Website</label>
                                <input type="text" class="form-control" name="website" placeholder="Website" value="{{$location->website}}" />
                            </div>
                        </div>
                    </div>

                </div> <!-- Modal Body-->

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-info" name="submit" value="Save">
                    <input type="hidden" class="btn btn-info" name="action" value="edit_loca">
                    <input type="hidden" class="btn btn-info" name="id_loca" value="{{$location->id}}">
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal 6 (Long Modal)-->
<div class="modal custom-width" id="add_new_device">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Add New Device</h3>
            </div>

            <div class="modal-body">
                <form id="rootwizard-2" method="post" action="{{url('location/createnewdevice&lid=' .$loca)}}" class="form-wizard validate form-horizontal">
                    <div class="steps-progress">
                        <div class="progress-indicator"></div>
                    </div>

                    <ul>
                        <li id="tab_1" class="active"><a href="#tab1" data-toggle="tab"><span>1</span>Select Your Router Type</a></li>
                        <li id="tab_2"><a href="#tab2" data-toggle="tab"><span>2</span>Configuring Your Hardwave</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="row">
                                <div class="col-md-4 text-right"><h4>Device Name</h4></div>

                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="new-device-name" name="device_name" data-validate="required" >
                                </div>
                            </div>

                            <br />

                            <div class="row">
                                <div class="col-md-4 text-right"><h4>Supported Routers</h4></div>

                                <div class="col-md-5"> 
                                    <select name="supported_routers" id="supported-router" onchange="javascript:getFirmwaresInof(this.value);" class="form-control select2" data-validate="required">
                                        <option value="">--Select one--</option>
                                        @foreach ($device_details as $key => $val)
                                            <option value="{{$key}}">{{$val['title']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <br />

                            <div class="row">
                                <div class="col-md-4 text-right"><h4>Router Mac Address</h4></div>
                                <div class="col-md-5">
                                    <input type="text" id="mac-address" class="form-control required mac" name="mac_address" data-mask="**-**-**-**-**-**" onblur="javascript:checkDuplicateMac(this.value);"/>
                                    <span class="form-control-feedback"><img class="al_duplicate_mac hide" src="{{url('themes/neon/assets/images/ajax-loader.gif')}}"></span>
                                    <label class="msg_duplicate_mac" style="color:red;"></label>
                                </div>
                            </div>

                            <br />

                            <div class="row">
                                <div class="col-md-4 text-right"><h4>Choose Location</h4></div>

                                <div class="col-md-5"> 
                                    <select name="location" id="new-location" class="form-control select2" data-validate="required">
                                        <option value="" >--Select Location--</option>                                        
                                            @foreach ($location_list as $locat)                                              
                                                <option value="{{$locat->id}}>{{$locat->name}}"</option>
                                            @endforeach
                                    </select>  
                                </div>
                            </div>
                            <ul class="pager wizard text-right">
                                <li class="next"><a href="#">Next <i class="entypo-right-open"></i></a></li>
                            </ul>
                        </div>

                        <div class="tab-pane" id="tab2">
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <div class="row">
                                        <div class="col-md-4">									
                                            <p><img class="img-responsive" id="router_new_image"/></p>                                            
                                        </div>

                                        <div class="col-md-8">
                                            <br />
                                            <p><h4>Device Name: <span id="device_name"></span></h4></p>
                                            <p><h4>Model Type: <span id="model_type"></span></h4></p>
                                            <p><h4>Mac Address: <span id="mac_address"></span></h4></p>
                                            <p><h4>Location: <span id="name_location"></span></h4></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
		                        <div class="col-md-12">
		                            <h3 style="margin-top: 5px;">
		                                <b>Firmwares</b>
		                            </h3>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div id="add_new_firmwares" class="col-md-12"></div>
		                    </div>
                            <ul class="pager wizard text-right">
                            	<li>
                                	<button type="submit" class="btn btn-info" name="btn_save_device"><i class="fa fa-save"></i> Complete Setup</button>
                            	<li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>							
        </div>
    </div>
</div>


<!-- Modal 6 (Long Modal)-->
<div class="modal custom-width" id="choose_hardware">
    <div class="modal-dialog" style="width:50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Assign Device</h3>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="message"></div>
                        <form id="frm_assign_device" name="frm_assign_device" method="post" action="">
                            <div class="form-group">
                                <label class="control-label">Devices</label>
                                <select id="device_list" name="device_list[]" class="select2" multiple>                                    
                                    @if($device_list)
	                                    @foreach ($device_list as $device)
	                                           <option {{in_array($device->id, $selected_device) ? "selected='selected'" : "" }} value="{{$device->id }}">{{ $device->name}}</option>
	                                    @endforeach
                                    @else
                                        <option value="">No active device found!</option>
                                    @endif
                                </select>
                            </div>
                            <button type="button" class="btn btn-info btn-icon icon-left" onclick="javascript:AssignDevice();" name="btn_assign_device"><i class="fa fa-save"></i>Save <img class="hide" src="{{url('themes/neon/assets/images/ajax-loader.gif')}}" alt="ajax-loader" id="al_assign_device"/></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal 6 (Long Modal)-->
<div class="modal custom-width" id="edit_device">
    <div class="modal-dialog" style="width: 50%;">
        <form id="rootwizard-2" autocomplete="off" method="post" action="" class="form-wizard validate">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title">Edit Device: <span id="d_name"></span></h3>
                </div>
                <div class="modal-body">
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4"><h5>Device Name</h5></div>
                                <div class="col-md-8"><input type="text" name="device_name" data-validate="required" class="form-control"></div>
                            </div>
                        </div>                        
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4"><h5>Assigned To</h5></div>
                                <div class="col-sm-8">
                                    <select name="location_id" disabled="disabled" id="location_id" data-validate="required"  class="form-control">
                                        <option value="" >--Select Location--</option>
                                          @if($location_list)
                                            @foreach ($location_list as $locat)                                                
                                                <option value="{{$locat->id}}">{{$locat->name}}</option>
                                            @endforeach
                                           @endif                                
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><h3><b>Device Information</b></h3></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p>Current SSID: <span id="d_ssid_name"></span></p>
                            <p>Current Active Campaign: <span id="d_campaign_name"></span></p>
                            <p>Last Contact: <span id="d_last_contact"></span></p>
                            <p> WAN IP: <span id="d_external_ip"></span></p>
                            <p> LAN IP: <span id="d_internal_ip"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p>Vendor: <span id="d_vendor"></span></p>
                            <p>Model: <span id="d_model"></span></p>
                            <p>MAC Address: <span id="d_mac_address"></span></p>
                            <p>OS Date: <span id="os_date"></span></p>
                        </div>
                        <div class="col-md-4">
                            <img id="router_image" class="img-responsive"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><h3 style="margin-top:5px;"><b>Firmwares</b></h3></div>
                    </div>
                    <div class="row">
                        <div id="firmwares" class="col-md-12"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-info" name="submit" value="Save" />								
                    <input type="hidden" name="action" value="edit_device" />
                    <input type="hidden" name="hdn_device_id" value="" />
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal custom-width" id="assign_sub_user">
    <div class="modal-dialog" style="width:50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Assign Sub User</h3>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="message"></div>
                        <form id="frm_assign_device" name="frm_assign_subuser" method="post" action="{{url('location/assignsubuser')}}">
                            <div class="form-group">
                                <label class="control-label">Sub User</label>
                                <select id="sub_user_list" name="sub_user_list[]" class="select2" multiple>
                                    @if($sub_users)
                                        @foreach($sub_users as $user)
                                           <option {{ array_key_exists($user->id, $sub_user_list) ? "selected='selected'" : "" }} value="{{$user->id}}">{{$user->full_name}}</option>
                                         @endforeach
                                    @else
                                        <option value="">No sub user found!</option>
                                    @endif
                                </select>
                                <input type="hidden" name="hdn_location_id" value="{{$loca}}"/>
                            </div>
                            <button type="submit" class="btn btn-info btn-icon icon-left pull-right" name="btn_assign_device"><i class="fa fa-save"></i>Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Schedule Modal -->
<div class="modal custom-width" id="add_new_schedule">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <form name="frm_create_schedule" id="frm_create_schedule" action="{{url('location/saveschedule?lid=' . $loca)}}" method="post">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title">Schedule New Campaign</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- <div class="col-md-12">
                            <h3><label class="control-label" for="full_name">Schedule New Campaign</label></h3>
                        </div> -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ddl_active_campaign">Choose Campaign to Schedule</label>
                                <select class="form-control" id="ddl_active_campaign" name="ddl_active_campaign" required>
                                    <option value="">-- Select One --</option>                                    
                                    @if(isset($campaign))
                                        {{--*/$clg_id = 0/*--}}
                                        @if ($campaign_loca_status)                                            
                                            {{--*/$clg_id = $campaign_loca_status->id/*--}} 
                                        @endif                                           
                                        @foreach ($campaign as $row)
                                            @if($clg_id != $row->id)
                                                <option value="{{$row->id}}">{{$row->name}}</option>
                                            @endif
                                        @endforeach
                                    @endif                                   
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="repeat_type">Repeat Type</label>
                                <select name="repeat_type" id="repeat_type" class="form-control" onchange="javascript:loadOption(this.value)">
                                    <option value="all_day">All Day</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 all-day">
						      <div class="form-group">
						        <label class="control-label">From</label>
						        <div class="row">
						        <div class="col-sm-6">
						        	<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									  	<input type="text" name="start_date" class="form-control datepicker">
									</div>
						        </div>
						        <div class="col-sm-6 time">
						        	<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
									  	<input type="text" name="start_time" class="form-control timepicker">
									</div>
						        </div>
						        </div>
						      </div>
						      <div class="form-group">
						        <label class="control-label">To</label>
						        <div class="row">
						        <div class="col-sm-6">
						        	<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									  	<input type="text" name="end_date" class="form-control datepicker">
									</div>
						        </div>
						        <div class="col-sm-6 time">
						        	<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
									  	<input type="text" name="end_time" class="form-control timepicker" data-format="hh:mm:ss">
									</div>
						        </div>
						        </div>
						      </div>
						      <div class="form-group">
						        <div class="row">
						          <div class="checkbox">
						            <label>
						              <input type="checkbox" class="icheck" name="chk_allday" id="chk_allday"> All Day
						            </label>
						          </div>
						        </div>
						      </div>
                        </div>
                        <div class="col-md-12 hide daily">
                        	<div class="row">
                        		<div class="col-md-12"><label>Time</label></div>
                        	</div>
                        	<div class="row">
                        		<div class="col-md-6">
                        			<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
									  	<input type="text" name="daily_start_time" class="form-control timepicker" data-format="hh:mm:ss">
									</div>
                        		</div>
                        		<div class="col-md-6">
                        			<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
									  	<input type="text" name="daily_end_time" class="form-control timepicker" data-format="hh:mm:ss">
									</div>
                        		</div>
                        	</div>
                        </div><br>
                        <div class="col-md-12 hide weekly">
                            <div class="form-group">
                                <label for="selectmultiple">Days of week</label>
                                <select name="days_of_week[]" id="selectmultiple" class="select2 form-control" multiple>
                                    @foreach ($week_array as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 hide monthly">
                            <div class="form-group">
                                <label for="days_of_month">Days of month</label>
                                <select class='form-control' name="days_of_month">
                                    @foreach($days_of_month as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 until hide">
                            <div class="form-group">
                                <label for="until">Until</label>
                                <input type="text" class="form-control datepicker" name="until" data-format="yyyy-mm-dd">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <div class="pull-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            <input type="hidden" name="action" value="create_schedule" />
                            <input type="hidden" name="hdn_location_schedule_id" id="hdn_location_schedule_id" value="" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Alert Change Status Device Success-->
<div class="modal custom-width text-center" id="changeStatusDeviceSuccess" >
    <div class="modal-dialog" style="width: 30%; ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Device has successfully added.</h4>				
                <br /><br />
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Dialog -->
<div class="modal" id="deleteConfirm" >
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body modal-title" style="font-weight:bold; color: black;"></div>
            <div class="modal-footer text-center">
                <input type="hidden" id="hdn_schedule_id" value=""/>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button data-dismiss="modal" id="delete" class="btn btn-danger" onclick="delete_schedule();">Yes</button>
            </div>			
        </div>
    </div>
</div>

<!-- Delete Success Dialog -->
<div class="modal custom-width text-center" id="deleteSuccess" >
    <div class="modal-dialog" style="width: 35%; ">
        <div class="modal-content">			
            <div class="modal-header">
                <h5 class="modal-title">Device Successfully Removed</h5>
                <br><br>
                <button type="button" class="btn btn-info" data-dismiss="modal" onclick="changeSuccess()">OK</button>				
            </div>			
        </div>
    </div>
</div>

<div class="modal" id="alert_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p id="alert_text" style="font-weight: bold; color: black;"></p>
                <p><button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Ok</button></p>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal" id="remove_alert">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" style="font-weight:bold; color:black;">
                <i class="entypo-attention"></i> Are you sure You want to remove?
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button id="btn_remove_device" type="button" class="btn btn-danger">Yes <img src="{{url('themes/neon/assets/images/ajax-loader.gif')}}" class="hide" id="al_remove_device" alt="ajax-loader"/></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    $(document).ready(function ($) {    	
    	$("#btnSetDefault").removeAttr('disabled');
    	if($("#ddl_default_campaign").val() === $("#hdn_default_campaign").val()){
    		$("#btnSetDefault").attr('disabled','');
    	}

    	$("#ddl_default_campaign").on("change", function (e) {
    		$("#btnSetDefault").removeAttr('disabled');
        	if($(this).val() === $("#hdn_default_campaign").val()){
        		$("#btnSetDefault").attr('disabled','');
        	}
		});

    	$('.timepicker').timepicker();
    	$('.datepicker').datepicker({format:'yyyy-mm-dd'});
    	$(".all-day .time").removeClass('hide');
    	$('#chk_allday').on('ifChanged', function(event){
    		if ($(this).is(':checked') === true) {
    			$(".time").addClass('hide');
    		} else {
    			$(".time").removeClass('hide');
    		}
    	});
    	
    	$(".daily .time").addClass('hide');
    	
    	

        var donut_chart_demo = $("#donut-chart-demo");
        donut_chart_demo.parent().show();

        var donut_chart = Morris.Donut({
            element: 'donut-chart-demo',
            data: [
                {label: "Facebook", value: {{$fbuser}} },
                {label: "Twitter", value: {{$twuser}} },
                {label: "LinkedIn", value: {{$liuser}} },
                {label: "Google+", value: {{$gpuser}} },
                {label: "Instagram", value: {{$iguser}} },
                {label: "Custom", value: {{$customer}} }
            ],
            colors: ['#3b5998', '#55acee', '#0976b4', '#dd4b39', '#3f729b', '#00A651']
        });

        donut_chart_demo.parent().attr('style', '');

        $("#blue-water-a{{$loca}}").geocomplete({
            map: ".map_canvas_{{$loca}}"
        });

        $("#blue-water-a{{$loca}}").trigger("geocode");

        $("#chk_default_campaign").change(function () {
            if ($(this).is(':checked'))
                $("#div_scheduler").addClass('hide');
            else
                $("#div_scheduler").removeClass('hide');
        });

        $("#edit_campaign").click(function () {
            $("#my_tab>ul>li.active").removeClass("active");
            $('#my_tab ul li:nth-child(2)').addClass('active');
        });

        $("#btn_show_schedule_modal").click(function () {
        	var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var current_date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;

            var current_time = d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();

            $("#campaign").val("");
            $("#repeat_type").val("all_day");
            $("#repeat_type").trigger('change');
            $("select[name='days_of_week[]']").select2("data", "");
            $("input[name='start_date']").val(current_date);
            $("input[name='end_date']").val(current_date);
            $("input[name='start_time']").val(current_time);
            $("input[name='end_time']").val(current_time);
            $('#chk_allday').iCheck('uncheck');            
            $('#chk_daily_allday').iCheck('uncheck');
            $("input[name='until']").val("");
            $("select[name='days_of_month']").val("");
            $("#hdn_location_schedule_id").val("");
            
            $("#add_new_schedule").modal('show');
        });
        $('#chk_no_limit').on('switchChange.bootstrapSwitch', function(event, state) {
            if (state) {
                $("#max_bandwidth_slider").slider("value", 25600);
                $("#max_bandwidth_slider .ui-label").text('25600 kbps');
                $("#max_bandwidth").val('25600 kbps');
                $("#max_bandwidth_slider").slider("option", "disabled", true);
            } else {
                $("#max_bandwidth_slider").slider("value", 64);
                $("#max_bandwidth_slider .ui-label").text('64 kbps');
                $("#max_bandwidth").val('64 kbps');
                $("#max_bandwidth_slider").slider("enable");
            }
        });

        $("#edit").click(function () {
            setTimeout(function () {
                $("#geocomplete").geocomplete({
                    map: ".map_canvas_edit",
                    details: "form",
                    types: ["geocode", "establishment"],
                });
                $("#geocomplete").trigger("geocode");

                $("#rootwizard-2 input[name='name']").val('{{$location->name}}');
                        $("#rootwizard-2 input[name='formatted_address']").val("{{$location->address}}");
                        name = $("#rootwizard-2 input[name='name']").val();
                add = $("#rootwizard-2 input[name='formatted_address']").val();
                $(".map_new p").text(add);
                $(".map_new h3").text(name);
            }, 1500);
        });

        $("#textcolor.colorpicker").on('input keydown keypress keyup blur change changeColor', function () {
            color = $("div#change_width input[name='textcolor']").val();
            if ($("div#change_width input[name='textcolor']").val() !== $("div#change_width input[name='textcolor_hidden']").val()) {
                // alert(color+"!");
                $("div#change_width input[name='textcolor_hidden']").val(color);
                $("div#show_image div.show_image").css({
                    "color": color
                });
            }
        });

        $("#backgroundcolor.colorpicker").on('input keydown keypress keyup blur change changeColor', function () {
            color = $("div#change_width input[name='backgroundcolor']").val();
            if ($("div#change_width input[name='backgroundcolor']").val() !== $("div#change_width input[name='backgroundcolor_hidden']").val()) {
                $("div#change_width input[name='backgroundcolor_hidden']").val(color);
                $("div#show_image div.change_bg").css({
                    "background-color": color
                });
            }
        });

        $("#tab_2").click(function () {
            $("ul.pager li.next").addClass("hide");
            $("ul.pager input").removeClass("hide");

            device_name = $("#add_new_device input[name='device_name']").val();
            supported_routers = $("#add_new_device select[name='supported_routers']").find("option:selected").text();
            mac_address = $("#add_new_device input[name='mac_address']").val();
            location_name_1 = $("#new-location").find("option:selected").text();

            $("span#device_name").text(device_name);
            $("span#model_type").text(supported_routers);
            $("span#mac_address").text(mac_address);
            $("span#name_location").text(location_name_1);
        });

        $("ul.pager li.next").click(function () {
            if ($("#add_new_device input[name='device_name']").val() !== "" && $("#add_new_device select[name='supported_routers']").val() !== "" && $("#add_new_device input[name='mac_address']").val() !== "" && $("#add_new_device select[name='location']").val() !== "") {
                $("ul.pager li.next").addClass("hide");
                $("ul.pager input").removeClass("hide");

                device_name = $("#add_new_device input[name='device_name']").val();
                supported_routers = $("#add_new_device select[name='supported_routers']").find("option:selected").text();
                mac_address = $("#add_new_device input[name='mac_address']").val();
                location_name_1 = $("#new-location").find("option:selected").text();

                $("span#device_name").text(device_name);
                $("span#model_type").text(supported_routers);
                $("span#mac_address").text(mac_address);
                $("span#name_location").text(location_name_1);
            }
        });

        $("#tab_1").click(function () {
            $("ul.pager li.next").removeClass("hide");
            $("ul.pager input").addClass("hide");
        });

    }); /*END DOCUMENT.READY*/

    function setNoLimit(cb) {
        if (cb.checked) {
            $("#max_bandwidth_slider").slider("value", 25600);
            $("#max_bandwidth_slider .ui-label").text('25600 kbps');
            $("#max_bandwidth").val('25600 kbps');
            $("#max_bandwidth_slider").slider("option", "disabled", true);
        } else {
            $("#max_bandwidth_slider").slider("value", 64);
            $("#max_bandwidth_slider .ui-label").text('64 kbps');
            $("#max_bandwidth").val('64 kbps');
            $("#max_bandwidth_slider").slider("enable");
        }
    }

    function loadOption(value) {
        switch (value) {
            case'all_day':
            	$('#chk_allday').iCheck('uncheck');
            	$('.time').removeClass('hide');
            	var fullDate = new Date();console.log(fullDate);
            	var twoDigitMonth = fullDate.getMonth()+"";if(twoDigitMonth.length==1)  twoDigitMonth="0" +twoDigitMonth;
            	var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1) twoDigitDate="0" +twoDigitDate;
                var current_date = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;
                $("input[name='start_time']").val('00:00:00');
                $("input[name='end_time']").val('00:00:00');
                $("input[name='start_date']").val(current_date);
                $("input[name='end_date']").val(current_date);
                $(".all-day").removeClass('hide');
                $(".daily").addClass('hide');
                $(".weekly").addClass('hide');
                $(".monthly").addClass('hide');
                $(".until").addClass('hide');
                break;
            case'daily':
            	$(".daily").removeClass('hide');
            	$('#chk_daily_allday').iCheck('uncheck');
            	$('.daily .time').removeClass('hide');
                $(".all-day").addClass('hide');
                $(".weekly").addClass('hide');
                $(".monthly").addClass('hide');
                $(".until").removeClass('hide');
                break;
            case'weekly':
                $(".all-day").addClass('hide');
                $(".daily").addClass('hide');
                $(".weekly").removeClass('hide');
                $(".monthly").addClass('hide');
                $(".until").removeClass('hide');
                break;
            case'monthly':
                $(".all-day").addClass('hide');
                $(".daily").addClass('hide');
                $(".weekly").addClass('hide');
                $(".monthly").removeClass('hide');
                $(".until").removeClass('hide');
                break;
        }
    }

    //Tab Overview
    function load_map() {
        $("#geocomplete").geocomplete({
            map: ".map_canvas_edit",
            details: "form",
            types: ["geocode", "establishment"],
        });
        ("#geocomplete").trigger("geocode");
        setTimeout(function () {
            name = $("#rootwizard-2 input[name='name']").val();
            add = $("#rootwizard-2 input[name='formatted_address']").val();
            $(".map_new p").text(add);
            $(".map_new h3").text(name);
        }, 1500);
    }
    ;

    function load_text() {
        name = $("#rootwizard-2 input[name='name']").val();
        add = $("#rootwizard-2 input[name='formatted_address']").val();
        $(".map_new p").text(add);
        $(".map_new h3").text(name);
    }   

    //Tab Campaign
    function show(id, form) {
        if (form === 'b') {
            if (document.getElementById('b_' + id + '').checked) {
                $("div.s-" + id + "").removeClass('hide');
                $("div#show_image div.img_" + id + "").removeClass('hide');
                $("div.b-" + id + " input[name='b_" + id + "']").val('1');
            } else {
                $("div.s-" + id + "").addClass('hide');
                $("div#show_image div.img_" + id + "").addClass('hide');
                $("div.b-" + id + " input[name='b_" + id + "']").val('0');
            }
        }

        if (form === 'e') {
            if (document.getElementById('check-' + id + '').checked) {
                $("div#" + id + "-form").removeClass('hide');
            } else {
                $("div#" + id + "-form").addClass('hide');
            }
        }

        if (form === 's') {
            if (id === "s_date") {
                if ($("input[name='all_day']").is(':checked')) {
                    $('div.date_time div.date-and-time').addClass('hide');
                    $('div.date_time div.date').removeClass('hide');

                    start_time = $('div.date_time div.date-and-time input[name="start_date"]').val();
                    end_time = $('div.date_time div.date-and-time input[name="end_date"]').val();
                    $('div.date_time div.date input[name="start_date_2"]').val(start_time);
                    $('div.date_time div.date input[name="end_date_2"]').val(end_time);
                } else {
                    $('div.date_time div.date-and-time').removeClass('hide');
                    $('div.date_time div.date').addClass('hide');

                    start_time_2 = $('div.date_time div.date input[name="start_date_2"]').val();
                    end_time_2 = $('div.date_time div.date input[name="end_date_2"]').val();
                    $('div.date_time div.date-and-time input[name="start_date"]').val(start_time_2);
                    $('div.date_time div.date-and-time input[name="end_date"]').val(end_time_2);
                }
            }

            if (id === "date") {
                repeat = $('div#repeat select[name="repeat"]').val();
                if (repeat === "no") {
                    //.$('div#repeat div.until').addClass('hide');
                    $('div#repeat_every').addClass("hide");
                } else {
                    //.$('div#repeat div.until').removeClass("hide");
                    $('div#repeat_every').removeClass("hide");
                    $('div#repeat_every div.repeat_every').addClass("hide");
                    $('div#repeat_every div.' + repeat + '_select').removeClass("hide");

                    if ($('div#repeat_every select[name="repeat_every"]').val() === '1') {
                        $('div#repeat_every div.' + repeat + '').removeClass("hide");
                        $('div#repeat_every div.' + repeat + '_select').removeClass("hide");
                        $('div#repeat_every div.' + repeat + 's').addClass("hide");
                    } else {
                        $('div#repeat_every div.' + repeat + '').addClass("hide");
                        $('div#repeat_every div.' + repeat + 's').removeClass("hide");
                    }
                }
            }

            if (id === "time") {
                var str = $("select[name='choose_date[]']").val();

                if (str.indexOf('8') !== '-1') {
                    $("div#repeat_every div.weekly_select input[name='week_show']").val('8');
                } else {
                    $("div#repeat_every div.weekly_select input[name='week_show']").val(str);
                }

                if ($("div#repeat_every div.weekly_select input[name='week_show']").val() === '1,2,3,4,5,6,7') {
                    $("div#repeat_every div.weekly_select input[name='week_show']").val('8');
                }
            }

            //edit schedule
            if (id === "edit_s_date") {
                if ($("#edit_schedule input[name='all_day']").is(':checked')) {
                    $('#edit_schedule div.date_time div.date-and-time').addClass('hide');
                    $('#edit_schedule div.date_time div.date').removeClass('hide');

                    start_time = $('#edit_schedule  div.date_time div.date-and-time input[name="start_date"]').val();
                    end_time = $('#edit_schedule  div.date_time div.date-and-time input[name="end_date"]').val();
                    $('#edit_schedule  div.date_time div.date input[name="start_date_2"]').val(start_time);
                    $('#edit_schedule  div.date_time div.date input[name="end_date_2"]').val(end_time);
                } else {
                    $('#edit_schedule  div.date_time div.date-and-time').removeClass('hide');
                    $('#edit_schedule  div.date_time div.date').addClass('hide');

                    start_time_2 = $('#edit_schedule  div.date_time div.date input[name="start_date_2"]').val();
                    end_time_2 = $('#edit_schedule  div.date_time div.date input[name="end_date_2"]').val();
                    $('#edit_schedule  div.date_time div.date-and-time input[name="start_date"]').val(start_time_2);
                    $('#edit_schedule  div.date_time div.date-and-time input[name="end_date"]').val(end_time_2);
                }
            }

            if (id === "edit_date") {
                repeat = $('div#edit_repeat select[name="repeat"]').val();
                if (repeat === "no") {
                    ///$('div#edit_repeat div.until').addClass('hide');
                    $('div#edit_repeat_every').addClass("hide");
                } else {
                    //$('div#edit_repeat div.until').removeClass("hide");
                    $('div#edit_repeat_every').removeClass("hide");
                    $('div#edit_repeat_every div.repeat_every').addClass("hide");
                    $('div#edit_repeat_every div.' + repeat + '_select').removeClass("hide");

                    if ($('div#edit_repeat_every select[name="repeat_every"]').val() === '1') {
                        $('div#edit_repeat_every div.' + repeat + '').removeClass("hide");
                        $('div#edit_repeat_every div.' + repeat + '_select').removeClass("hide");
                        $('div#edit_repeat_every div.' + repeat + 's').addClass("hide");
                    } else {
                        $('div#edit_repeat_every div.' + repeat + '').addClass("hide");
                        $('div#edit_repeat_every div.' + repeat + 's').removeClass("hide");
                    }
                }
            }

            if (id === "edit_time") {
                var str = $("div#edit_repeat_every select[name='choose_date[]']").val();

                if (str.indexOf('8') !== '-1') {
                    $("div#edit_repeat_every div.weekly_select input[name='week_show']").val('8');
                } else {
                    $("div#edit_repeat_every div.weekly_select input[name='week_show']").val(str);
                }

                if ($("div#edit_repeat_every div.weekly_select input[name='week_show']").val() === '1,2,3,4,5,6,7') {
                    $("div#edit_repeat_every div.weekly_select input[name='week_show']").val('8');
                }
            }

            if (id === "show_select") {
                $('div#edit_repeat_every div.weekly_select').removeClass("hide");
                $('div#edit_repeat_every div.weekly_select_2').addClass("hide");
            }
        }
    }

    function edit_schedule(id) {
        $("#preloader").removeClass('hide');
        $.ajax({
            url: '{{url("ajax/GetScheduleById")}}',
            data: {id: id},
            type: 'POST',
            success: function (output) {
                $("#preloader").addClass('hide');
                var data = jQuery.parseJSON(output);
                $("#ddl_active_campaign").val(data.campaign_id);
                $("#repeat_type").val(data.repeat_type);
                $("#hdn_location_schedule_id").val(data.id);
                switch (data.repeat_type) {
                    case'all_day':
                        $("input[name='start_date']").val(data.start_date !== "" ? data.start_date : "0000-00-00");
                        $("input[name='start_time']").val(data.start_time !== "" ? data.start_time : "00:00:00");
                        $("input[name='end_date']").val(data.end_date !== "" ? data.end_date : "0000-00-00");
                        $("input[name='end_time']").val(data.end_time !== "" ? data.end_time : "00:00:00");

                        if(data.repeat_data === ''){
                        	$('#chk_allday').iCheck('uncheck');
                        }else{
                        	$('#chk_allday').iCheck('check');
                        }

                        $(".all-day").removeClass('hide');
                        $(".weekly").addClass('hide');
                        $(".monthly").addClass('hide');
                        $(".until").addClass('hide');
                        break;
                    case'daily':                    	
                    	$("input[name='daily_start_time']").val(data.start_time);
                    	$("input[name='daily_end_time']").val(data.end_time);
                        $("input[name='until']").val(data.repeat_until);
                        $(".daily").removeClass('hide');
                        $(".all-day").addClass('hide');
                        $(".weekly").addClass('hide');
                        $(".monthly").addClass('hide');
                        $(".until").removeClass('hide');
                        break;
                    case'weekly':
                        if (data.days_array !== "") {
                            var days = jQuery.parseJSON(data.days_array);
                            $("select[name='days_of_week[]']").select2("data", days);
                        }
                        $("input[name='until']").val(data.repeat_until);
                        $(".all-day").addClass('hide');
                        $(".weekly").removeClass('hide');
                        $(".monthly").addClass('hide');
                        $(".until").removeClass('hide');
                        break;
                    case'monthly':
                        $("select[name='days_of_month']").val(data.repeat_data);
                        $("input[name='until']").val(data.repeat_until);
                        $(".all-day").addClass('hide');
                        $(".weekly").addClass('hide');
                        $(".monthly").removeClass('hide');
                        $(".until").removeClass('hide');
                        break;
                }
                $("#add_new_schedule").modal('show');
            }
        });
    }

    function load_delete_schedule_popup(id) {
        $("#hdn_schedule_id").val(id);
        $("div#deleteConfirm div.modal-title").html("<i class='entypo-attention'></i> Are you sure you want to delete?");
        $("#deleteConfirm").modal('show');
    }
    function delete_schedule() {
    	$("#preloader").removeClass('hide');
        var id = $('#hdn_schedule_id').val();
        $.ajax({
            url: '{{url("ajax/DeleteSchedule")}}',
            data: {id: id},
            type: 'POST',
            complete: function (output) {
            	$("#preloader").addClass('hide');
                location.reload();
            }
        });
    }

    //reload page-container
    function changeSuccess() {
        location.reload();
    }

    function change_tab(id) {
        a = $("div#show_image input[name='change_tab']").val();

        if ($("div#change_width input[name='campaign_name']").val() !== '' && $("div#change_width input[name='ssid_name']").val() !== '') {
            if (id === "next") {
                id = a - 1 + 2;
                if (id > 4) {
                    id = 4;
                }
            }

            if (id === "pre") {
                id = a - 1;
                if (id < 1) {
                    id = 1;
                }
            }
        } else {
            id = a;
        }

        $("div#show_image div.tab_" + a + "c").addClass("hide");
        $("div#show_image div.tab_" + id + "c").removeClass("hide");
        $("div#show_image input[name='change_tab']").val(id);

        if (id === 4) {
            $("div#change_width").removeClass("col-md-7");
            $("div#change_width").addClass("col-md-12");
            $("div#show_image").addClass("hide");
        } else {
            $("div#change_width").removeClass("col-md-12");
            $("div#change_width").addClass("col-md-7");
            $("div#show_image").removeClass("hide");
        }
    }

    //End tab Campaign

    function edit_user(id) {
        $.post(
                '{{url("ajax/EditUser")}}',
                {
                    submit: "edit",
                    id: id
                },
        function (data, status) {
            if (status === 'success') {
                var js_arr = data.js_arr;
                $("#edit_user input[name='user_id']").val(js_arr['id']);
                $("#edit_user input[name='first_name']").val(js_arr['first_name']);
                $("#edit_user input[name='last_name']").val(js_arr['last_name']);
                $("#edit_user input[name='email_username']").val(js_arr['email_username']);
                $("#edit_user input[name='mobile_phone']").val(js_arr['mobile_phone']);
                $("#edit_user select[name='access_level']").val(js_arr['access_level']);
                $("#edit_user select[name='location_id']").val(js_arr['location_id']);

            } else {
                alert("Edit " + status + "!");
            }
        },
                "json"
                );
    }

    //Edit Hardware
    function edit_device(id) {
        $("#preloader").removeClass('hide');
        $.post(
                '{{url("ajax/EditDevice")}}',
                {
                    submit: "edit",
                    id: id
                },
        function (data, status) {
            $("#preloader").addClass('hide');
            if (status === 'success') {
                var js_arr = data.js_arr;
                if (js_arr['status'] === 0) {
                    $("#edit_device div#status_check_on").addClass("hide");
                    $("#edit_device div#status_check_off").removeClass("hide");
                } else {
                    $("#edit_device div#status_check_off").addClass("hide");
                    $("#edit_device div#status_check_on").removeClass("hide");
                }

                $("#edit_device input[name='d_status']").val(js_arr['status']);
                $("#edit_device input[name='device_name']").val(js_arr['name']);
                $("#edit_device select[name='location_id']").val(js_arr['location_id']);
                $("span#d_name").text(js_arr['name']);
                $("span#d_mac_address").text(js_arr['mac_address']);
                $("span#d_ssid_name").text(js_arr['ssid']);
                $("span#d_external_ip").text(js_arr['wan']);
                $("span#d_internal_ip").text(js_arr['lan']);
                $("#d_model").text(js_arr['model']);
                $("#edit_device input[name='hdn_device_id']").val(js_arr['id']);
                $("#d_campaign_name").html(js_arr['campaign_name']);
                $("#d_last_contact").text(js_arr['last_contact']);
                $("#os_date").text(js_arr['os_date']);
                $("#d_vendor").text(js_arr['vendor']);
                $("#router_image").attr('src', js_arr['image']);
                var firmwares_warning_msg = '<div class="alert alert-danger"><strong>Please be sure to use the correct firmware when flashing your device otherwise you risk your router becoming inoperable. Always update firmware with a hardwired ethernet cable.</strong></div>';
                $("#firmwares").html(firmwares_warning_msg + js_arr['firmwares']);

                //$("#delete").attr('onclick', 'deleteDevice(' + js_arr['id'] + ');');
                //$("#command").html("wget 'http://radius.mywifi.io/install/?nasid=" + js_arr['nasid'] + "&type=dd-wrt' -q -O /tmp/setup.sh; chmod 755 /tmp/setup.sh; /tmp/setup.sh;");
                //  
                $('#edit_device').modal('show', {backdrop: 'static'});
            } else {
                //alert("Edit " + status + "!");
            }
        },
                "json"
                );
    }

    function changeStatusEdit() {
        if ($("#edit_device input[name='d_status']").val() === '0') {
            $("#edit_device input[name='d_status']").val('1');
        } else {
            $("#edit_device input[name='d_status']").val('0');
        }
    }

    //Delete Hardware
    function deleteDevice(id) {
        $.post(
                '{{url("ajax/DeleteDevice")}}',
                {
                    submit: "delete",
                    id: id
                },
        function (data, status) {
            if (status === 'success') {
                alert("Delete " + status + "!");
                location.reload();
            } else {
                alert("Delete " + status + "!");
            }
        },
                "json"
                );
    }

    

    function load_edit_schedule_modal() {
        $('#edit_schedule').modal('show', {backdrop: 'static'});
    }
    function load_add_new_campaign_modal() {
        $('#add_new_campaign').modal('show', {backdrop: 'static'});
    }

    function setDefaultCampaign(location_id) {
        $("#preloader").removeClass('hide');
        var campaign_id = $("#ddl_default_campaign").val();
        if (campaign_id > 0) {
            $.ajax({
                url: '{{url("ajax/SetAsDefaultCampaign")}}',
                data: {location_id: location_id, campaign_id: campaign_id},
                type: 'post',
                complete: function (response) {
                    var output = response.responseText;
                    if (parseInt(output) === 1) {
                        $("#alert_text").html('Default camaign has been set, redirecting....');
                        $("#alert_modal").modal('show');
                        window.setTimeout('location.reload()', 2000);
                    } else {
                        $("#alert_text").html('Default camaign has not set.');
                        $("#alert_modal").modal('show');
                    }
                }
            });
        } else {
            $("#alert_text").html('You did not select any campaign.');
            $("#alert_modal").modal('show');
        }
        $("#preloader").addClass('hide');
    }
    //Get User Profile Data
    function getUserProfile(id) {
        //show_profile_modal
        $("#profile-picture_" + id).addClass('hide');
        $("#al_get_profile_" + id).removeClass('hide');
        $.ajax({
            url: '{{url("ajax/GetUserProfile")}}',
            data: {id: id},
            type: 'post',
            complete: function (output) {
                $("#profile-picture_" + id).removeClass('hide');
                $("#al_get_profile_" + id).addClass('hide');
                var my_data = JSON.parse(output.responseText);
                $(".full_name").html(my_data.full_name);
                $("#email").html(my_data.email);
                $("#last_connected").html(my_data.last_connected);
                $("#location").html(my_data.location);
                $("#country").html(my_data.country);
                $("#gender").html(my_data.gender);
                $("#birthday").html(my_data.birthday);
                $("#joined_date").html(my_data.joined_date);
                $("#time_zone").html(my_data.time_zone);
                $("#profile_picture").attr('src', my_data.profile_picture);

                if (my_data.netowrk_type === "FBuser") {
                    $('.social-icon').removeAttr('style');
                    $('.social-icon').attr('style', 'font-size: 20px;');
                    $('#fb_icon').removeAttr('style');
                    $('#fb_icon').attr('style', 'color: #47639E; font-size:20px;');
                }
                if (my_data.netowrk_type === "TWuser") {
                    $('.social-icon').removeAttr('style');
                    $('.social-icon').attr('style', 'font-size: 20px;');
                    $('#tw_icon').removeAttr('style');
                    $('#tw_icon').attr('style', 'color: #00ABF0; font-size:20px;');
                }
                if (my_data.netowrk_type === "LIuser") {
                    $('.social-icon').removeAttr('style');
                    $('.social-icon').attr('style', 'font-size: 20px;');
                    $('#li_icon').removeAttr('style');
                    $('#li_icon').attr('style', 'color: #017EB4; font-size:20px;');
                }
                if (my_data.netowrk_type === "GPuser") {
                    $('.social-icon').removeAttr('style');
                    $('.social-icon').attr('style', 'font-size: 20px;');
                    $('#gp_icon').removeAttr('style');
                    $('#gp_icon').attr('style', 'color: #C0382A; font-size:20px;');
                }

                $("#user_profile_modal").modal('show');
            }
        });
    }

    function CheckSubscriptionPlan() {
        $("#al_addnew_device").removeClass('hide');
        var created_device = $("#created_device").val();
        $.ajax({
            url: '{{url("ajax/CheckSubscriptionPlan")}}',
            data: {created_device: created_device},
            type: 'post',
            complete: function (output) {
                $("#al_addnew_device").addClass('hide');
                var response = $.trim(output.responseText);
                //console.log(output);
                if (response === "false") {
                    $('#subscription_alert').modal('show');
                } else if (response === "free_plan") {
                    $('#free_subscription_modal').modal('show');
                } else {
                    $('#add_new_device').modal('show');
                }
            }
        });
    }

    function AssignDevice() {
        $("#al_assign_device").removeClass('hide');
        var frmData = $("#frm_assign_device").serialize();
        var location_id = '{{$loca}}';
        $.ajax({
            url: '{{url("ajax/AssignDevice")}}',
            data: frmData + '&location_id=' + location_id,
            type: 'post',
            complete: function (output) {
                $("#al_assign_device").addClass('hide');
                if (output.responseText) {
                    var result = jQuery.parseJSON(output.responseText);
                    if (result.success) {
                        $("#message").html('<div role="alert" class="alert alert-success alert-dismissible fade in"><button data-dismiss="alert" class="close" type="button"><span aria-hidden="true"><i class="entypo-cancel-circled"></i></span><span class="sr-only">Close</span></button>' + result.success + '</div>');
                        window.setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        $("#message").html('<div role="alert" class="alert alert-danger alert-dismissible fade in"><button data-dismiss="alert" class="close" type="button"><span aria-hidden="true"><i class="entypo-cancel-circled"></i></span><span class="sr-only">Close</span></button>' + result.error + '</div>');
                    }
                }
            }
        });
    }

    function Remove(device_id) {
        $("#btn_remove_device").attr('onclick', 'javascript:RemovingDevice(' + device_id + ')');
        $("#remove_alert").modal('show');
    }

    function RemovingDevice(id) {
        $("#al_remove_device").removeClass('hide');
        var param = "device_id";
        var value = id;
        if (parseInt(id) === 0) {
            param = "location_id";
            value = '{{$loca}}';
        }
        $.ajax({
            url: '{{url("ajax/RemovingDevice")}}',
            data: {param: param, value: value},
            type: 'post',
            complete: function (output) {
                $("#al_remove_device").addClass('hide');
                $("#remove_alert").modal('hide');
                var result = output.responseText;
                if (parseInt(result) > 0) {
                    if (parseInt(id) === 0) {
                        $("#alert_text").html('Devices has been successfully removed.');
                    } else {
                        $("#device_" + id).remove();
                        $("#alert_text").html('Device has been successfully removed.');
                    }
                    $("#alert_modal").modal('show');
                    window.setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    $("#alert_text").html('Device has not been successfully removed.');
                    $("#alert_modal").modal('show');
                }
            }
        });
    }

    function load_assign_sub_user_modal() {
        $("#assign_sub_user").modal('show');
    }
</script>
<!-- Bottom Scripts -->
<link rel="stylesheet" href="https://fullcalendar.io/js/fullcalendar-2.3.1/fullcalendar.min.css">
<script src="{{$assets_dir.'/js/jquery.geocomplete.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.bootstrap.wizard.min.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.nicescroll.min.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.multi-select.js'}}"></script>
<script src="{{$assets_dir.'/js/bootstrap-tagsinput.min.js'}}"></script>
<script src="{{$assets_dir.'/js/fullcalendar/fullcalendar.min.js'}}"></script>
@endsection
