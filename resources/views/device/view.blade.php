@extends('template.layout')
@section('content')
<div class="page-container">
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')   
        <hr />
        {{$device_limit_msg}}
        <div class="row">
            <div class="col-md-4">
                <h2 style="margin-top: 0px;">
                    <i class="entypo-signal"></i>My Devices
                </h2>
            </div>
            <div class="col-md-8 text-right">
                @if(Session::get('USER_TYPE') != '3')                
                    <a href="javascript:void(0);" id="btn_add_new_device" class="btn btn-red btn-icon icon-left btn-lg fourth-tour"><i class="entypo-plus-circled"></i> Add New Device</a>
                @endif
            </div>
        </div>        
        {!! Session::get('SESSION_MESSAGE') !!}
        {{Session::forget('SESSION_MESSAGE')}}
        <div class='row'>
        	<div class='col-md-12'>
        		<div class="btn-group">
		        	<button type="button" id="all_device" class="device_status btn btn-white active" onclick="javascript:void(0);"></button>
		        	<button type="button" id="active_device" class="device_status btn btn-white" onclick="javascript:void(0);"></button>
		        	<button type="button" id="inactive_device" class="device_status btn btn-white" onclick="javascript:void(0);"></button>
					<button type="button" id="online_device" class="device_status btn btn-white" onclick="javascript:void(0);"></button>
					<button type="button" id="offline_device" class="device_status btn btn-white" onclick="javascript:void(0);"></button>
					<button type="button" id="never_connected_device" class="device_status btn btn-white" onclick="javascript:void(0);"></button>
					<input type="hidden" id="hdn_active_device_ids"/>
					<input type="hidden" id="hdn_inactive_device_ids"/>
					<input type="hidden" id="hdn_online_device_ids"/>
					<input type="hidden" id="hdn_offline_device_ids"/>
					<input type="hidden" id="hdn_never_connected_device_ids"/>
					<input type="hidden" id="hdn_device_status"/>
				</div>
        	</div>
        </div>
        @if(Session::get('USER_TYPE') != '3')        
        <div class="row">
            <div class="col-md-12 search-box">
            	<input type="text" id="device_search_value" class="form-control input-lg" placeholder="Search your device here by device title, device mac, device model, device user or device username and hit ENTER">
            </div>
        </div>
        @endif        
        <div id="devices"></div>	
        {!! $footer !!}      
    </div>
    <!-- /.main-content-->
    <!-- $this->loadViewFile("modules/dashboard/view/chat.php");-->
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
                <form id="rootwizard-2" method="post"
                      action="{{url('device/create')}}"
                      class="form-wizard validate form-horizontal">
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
                            	<div class="form-group">
                                    <div class="col-md-4 text-right">
                                        <h4>Device Name</h4>
                                    </div>
    
                                    <div class="col-md-5">
                                        <input type="text" class="form-control device_input" id="new-device-name" name="device_name" data-validate="required">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                            	<div class="form-group">
                                    <div class="col-md-4 text-right">
                                        <h4>Supported Routers</h4>
                                    </div>
    
                                    <div class="col-md-5">
                                        <select name="supported_routers" id="supported-router" onchange="javascript:getFirmwaresInof(this.value);"
                                                class="form-control select2" data-validate="required">
                                            <option value="">--Select one--</option>
                                            @foreach($device_list as $key=>$value)
                                                <option  value="{{$key}}">{{$value['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group has-feedback">
                                    <div class="col-md-4 text-right">
                                        <h4>Router Mac Address</h4>
                                    </div>

                                    <div class="col-md-5">
                                        <input type="text" id="mac-address" class="form-control device_input required mac" onblur="javascript:checkDuplicateMac(this.value);" name="mac_address" data-mask="**-**-**-**-**-**"/>
                                        <span class="form-control-feedback"><img class="hide al_duplicate_mac" src="{{url('themes/neon/assets/images/ajax-loader.gif')}}"></span>
                                        <label class="msg_duplicate_mac" style="color: red;"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                            	<div class="form-group">
                                    <div class="col-md-4 text-right">
                                        <h4>Choose Location</h4>
                                    </div>    
                                    <div class="col-md-5">
                                        <select name="location" id="new-location" class="form-control select2" data-validate="required">
                                            <option value="">--Select Location--</option>                                            
                                                @foreach($location_list as $location)
                                                    <option value="{{$location->id}}">{{$location->name}}</option>
                                                @endforeach                                              
                                        </select>
                                    </div>
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
                                            <p><img class="img-responsive" id="router_new_image" /></p>
                                        </div>

                                        <div class="col-md-8">
                                            <br />
                                            <p>


                                            <h4>
                                                Device Name: <span id="device_name"></span>
                                            </h4>
                                            </p>
                                            <p>


                                            <h4>
                                                Model Type: <span id="model_type"></span>
                                            </h4>
                                            </p>
                                            <p>


                                            <h4>
                                                Mac Address: <span id="mac_address"></span>
                                            </h4>
                                            </p>
                                            <p>


                                            <h4>
                                                Location: <span id="name_location"></span>
                                            </h4>
                                            </p>
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
                                    <button type="submit" class="btn btn-info" name="btn_save_device">
                                        <i class="fa fa-save"></i> Complete Setup
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 6 (Long Modal)-->
<div class="modal custom-width" id="edit_device">
    <div class="modal-dialog" style="width: 50%;">
        <form id="rootwizard-2" autocomplete="off" method="post" action="{{url('device/update')}}" class="form-wizard form-wizard-custome validate">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">
                        Edit Device: <span id="d_name"></span>
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-{{Session::get('USER_TYPE') == '1' ? '4' : '6'}}">
	                        <div class="form-group">
		                        <label>Device Name</label>
		                        <input type="text" name="device_name" data-validate="required" class="form-control">
		                    </div>
                        </div>
                        @if(Session::get('USER_TYPE') == '1')                       
                        <div class="col-md-4">
                        	<div class="form-group has-feedback">
	                        	<label>Device MAC</label>
	                        	<input type="text" name="mac_address" class="form-control required mac" onblur="javascript:checkDuplicateMac(this.value);" data-mask="**-**-**-**-**-**"/>
	                        	<span class="form-control-feedback"><img class="hide al_duplicate_mac" src="{{$assets_dir.'/assets/images/ajax-loader.gif'}}"></span>
                                <label class="msg_duplicate_mac" style="color: red;"></label>
                        	</div>
                        </div>
                        @endif
                        <div class="col-md-{{Session::get('USER_TYPE') == '1' ? '4' : '6'}}">
                        	<div class="form-group">
	                        	<label>Assigned To</label>
	                        	<select name="location_id" id="location_id" data-validate="required" class="form-control">
                                    <option value="">--Select Location--</option>                                    
                                    @foreach($location_list as $location)
                                    	<option value="{{$location->id}}">{{$location->name}}</option>
                                    @endforeach                                                                     
                                </select>
                        	</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <h3>
                                <b>Device Information</b>
                            </h3>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p>Current SSID: <span id="d_ssid_name"></span>
                                    </p>
                                    <p>
                                        Current Active Campaign: <span id="d_campaign_name"></span>
                                    </p>
                                    <p>
                                        Last Contact: <span id="d_last_contact"></span>
                                    </p>
                                    <p>
                                        WAN IP: <span id="d_external_ip"></span>
                                    </p>
                                    <p>
                                        LAN IP: <span id="d_internal_ip"></span>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p>
                                        Vendor: <span id="d_vendor"></span>
                                    </p>
                                    <p>
                                        Model: <span id="d_model"></span>
                                    </p>
                                    <p>
                                        MAC Address: <span id="d_mac_address"></span>
                                    </p>
                                    <p>
                                        OS Date: <span id="os_date"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4" style="margin-top: 17px;">
                            <img id="router_image" class="img-responsive" />
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
                        <div id="firmwares" class="col-md-12"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="btn_update_device" class="btn btn-info" name="submit">
                        <i class="fa fa-save"></i> Update
                    </button>
                    <input type="hidden" class="btn btn-info" name="action" value="edit_hardware" /> <input type="hidden" class="btn btn-info" name="device_id" value="" />
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="changeStatusOn">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body modal-title"
                 style="font-weight: bold; color: black;"></div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button class="btn btn-danger changeStatusOn" data-dismiss="modal">Yes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="device_alert_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{url('device/delete')}}">
                <div id="modal_body" class="modal-body"
                     style="font-weight: bold; color: black;"></div>
                <div id="modal_footer" class="modal-footer text-center"></div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".delete_device").click(function () {
            var id = $(this).attr('id');
            $("#modal_title").html('Delete Confirmation');
            $("#modal_body").html('<i class="entypo-attention"></i> Are you sure you want to delete this device?');
            $("#modal_footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                    '<button type="submit" class="btn btn-red">Yes</button><input type="hidden" name="device_id" value="' + id + '"/>');
            $("#device_alert_modal").modal('show');
        });
        
        $("#btn_add_new_device").click(function () {        	
        	$("[name=device_name]").val('');
        	$("[name=supported-router]").val('');
        	$("[name=mac_address]").val('');
        	$("[name=location]").val('');
        	$("[name=device_id]").val(0);
        	$(".next").removeClass('hide');
        	$('.msg_duplicate_mac').text('');
            $('#add_new_device').modal('show');
        });
        

        getAllDevices();
        
        $('#device_search_value').bind('keypress', function(e) {
        	var code = e.keyCode || e.which;
        	 if(code == 13) {
        		 getAllDevices();
        	 }
        });

        $("#devices").on( "click", ".pagination a", function (e){
	        e.preventDefault();
	        var page = $(this).attr("data-page"); //get page number from link
	        var search_value = $('#device_search_value').val();
	        if(page){
	        	getAllDevices(page)
	        }
		});

		$(".device_status").click(function(){
			var elm_id = $(this).attr('id');
			if(elm_id == 'all_device'){
				$("#hdn_device_status").val('');
			}else{
				var ids = $('#hdn_'+elm_id+'_ids').val() === "" ? 0 : $('#hdn_'+elm_id+'_ids').val();
				$("#hdn_device_status").val(ids);
			}
			$(".device_status").removeClass('active');
	        $(this).addClass("active");
			getAllDevices();
		});
        
    });

    function getAllDevices(page){
        $("#preloader").removeClass('hide');
        var search_value = $('#device_search_value').val();
        var status = $("#hdn_device_status").val();
        $.ajax({
            url: '{{url("ajax/GetAllDevices")}}',
            type: 'POST',
            data: {search_value:search_value, status:status, page:page,_token:'{{csrf_token()}}'},
            success:function(output){
            	var objData = $.parseJSON( output );
            	$("#preloader").addClass('hide');
            	$("#devices").html(objData.markup);
            	$("#hdn_active_device_ids").val(objData.device_ids.active_ids);
            	$("#hdn_inactive_device_ids").val(objData.device_ids.inactive_ids);
            	$("#hdn_online_device_ids").val(objData.device_ids.online_ids);
            	$("#hdn_offline_device_ids").val(objData.device_ids.offline_ids);
            	$("#hdn_never_connected_device_ids").val(objData.device_ids.never_connected_ids);
            	$("#all_device").text('All ('+objData.device_count.all+')');
            	$("#active_device").text('Active ('+objData.device_count.active+')');
            	$("#inactive_device").text('Inactive ('+objData.device_count.inactive+')');
            	$("#online_device").text('Online ('+objData.device_status_count.online+')');
            	$("#offline_device").text('Offline ('+objData.device_status_count.offline+')');
            	$("#never_connected_device").text('Never Connected ('+objData.device_status_count.never_connected+')');
            	$('[data-toggle="tooltip"]').tooltip();
            	$(".boots-switch").bootstrapSwitch();
				@if(Session::get('did') && Session::get('did') != '' && is_numeric(Session::get('did')))               
				@endif
				{{Session::forget('did')}}
            }
            
        });
    }
    
    function changeStatus(id) {
        $.ajax({
            url: '{{url("ajax/ChangeStatusDevice")}}',
            type: 'POST',
            data: {id:id},
            success:function(result){
            	result = result.trim('\n');
                if(result == '1' ){
                	location.reload();
                }
            }
        });
    }

    function changeDeviceStatus(id, status) {
        var title = $("#device_title_"+id).text();
        if (status === "on") {
            var active_device = {{$active_device}};
            var allowed_device = {{$allowed_device}};
            var user_type = {{Session::get('USER_TYPE')}};
            if (user_type !== 1 && active_device >= allowed_device) {
                $("#modal_body").html('<i class="entypo-info-circled"></i> You are currently using {{$active_device}} out of the {{$allowed_device}} active devices included in your plan. To add more active devices to your account, <a href="{{url('user/editprofile&get=device')}}">click here</a>.');
                $("#modal_footer").html('<button class="btn btn-danger" data-dismiss="modal">Ok</button>');
                $("#device_alert_modal").modal('show');
            } else {
                $("div#changeStatusOn div.modal-title").html("<i class='entypo-attention'></i> Are you sure you want to activate " + title + " ?");
                $("div#changeStatusOn button.changeStatusOn").attr('onclick', 'changeStatus(' + id + ');');
                $("#changeStatusOn").modal("show");
            }
        }
        if (status === "off") {
            $("div#changeStatusOn div.modal-title").html("<i class='entypo-attention'></i> Are you sure you want to disable " + title + " ?");
            $("div#changeStatusOn button.changeStatusOn").attr('onclick', 'changeStatus(' + id + ');');
            $("#changeStatusOn").modal("show");
        }
    }

    //#add_new_device
    $("#tab_2").click(function () {
        $("ul.pager li.next").addClass("hide");
        $("ul.pager input").removeClass("hide");
        $("ul.pager li.previous").removeClass("hide");

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
            $("ul.pager li.previous").removeClass("hide");

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

    $("ul.pager li.previous").click(function () {
        $("ul.pager li.next").removeClass("hide");
        $("ul.pager input").addClass("hide");
        $("ul.pager li.previous").addClass("hide");
    });


    $("#tab_1").click(function () {
        $("ul.pager li.next").removeClass("hide");
        $("ul.pager input").addClass("hide");
        $("ul.pager li.previous").addClass("hide");
    });

    // #edit_device
    function editDevice(id) {
        $("#preloader").removeClass('hide');
        
        $.post(
                '{{url("ajax/EditDevice")}}',
                {
                    submit: "edit",
                    id: id
                },
        function (data, status) {
            if (status === 'success') {
                $("#preloader").addClass('hide');
                var js_arr = data.js_arr;
                if (js_arr['status'] === 0) {
                    $("#edit_device div#status_check_on").addClass("hide");
                    $("#edit_device div#status_check_off").removeClass("hide");
                } else {
                    $("#edit_device div#status_check_off").addClass("hide");
                    $("#edit_device div#status_check_on").removeClass("hide");
                }
                //$("[name=device_id]").val(id);
                $("#edit_device input[name='d_status']").val(js_arr['status']);
                $("#edit_device input[name='device_name']").val(js_arr['name']);
                $("#edit_device select[name='location_id']").val(js_arr['location_id']);
                //$("#d_ssid_name").text(js_arr['location_id']);
                $("span#d_name").text(js_arr['name']);
                $("span#d_mac_address").text(js_arr['mac_address']);
                $("#edit_device input[name='mac_address']").val(js_arr['mac_address']);
                $("span#d_ssid_name").text(js_arr['ssid']);
                $("span#d_external_ip").text(js_arr['wan']);
                $("span#d_internal_ip").text(js_arr['lan']);
                $("#d_campaign_name").html(js_arr['campaign_name']);
                $("#d_last_contact").text(js_arr['last_contact']);
                $("#os_date").text(js_arr['os_date']);

                if (js_arr['status'] === '0') {
                    $("#location_id").attr('disabled', '');
                }else{
                	$("#location_id").removeAttr('disabled');
                }

                $("#edit_device input[name='device_id']").val(js_arr['id']);
                //$("#command").html("wget 'http://radius.mywifi.io/install/?nasid=" + js_arr['nasid'] + "&type=dd-wrt' -q -O /tmp/setup.sh; chmod 755 /tmp/setup.sh; /tmp/setup.sh;");

                var firmwares_warning_msg = '<div class="alert alert-danger"><strong>Please be sure to use the correct firmware when flashing your device otherwise you risk your router becoming inoperable. Always update firmware with a hardwired ethernet cable.</strong></div>';
                $("#firmwares").html(firmwares_warning_msg + js_arr['firmwares']);
                $("#router_image").attr('src', js_arr['image']);
                $("#d_model").text(js_arr['model']);
                $("#d_vendor").text(js_arr['vendor']);
                $('.msg_duplicate_mac').text('');
                $("#btn_update_device").removeClass('hide');
                $('#edit_device').modal('show');
            } else {
                alert("NASID Missing.");
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

    //reload page-container
    function changeSuccess() {
        location.reload();
    }
</script>
<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/jquery.bootstrap.wizard.min.js'}}"></script>
@endsection