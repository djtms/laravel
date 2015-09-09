<style>

    .page-container .sidebar-menu .logo-env {
        padding-bottom: 20px;
        padding-left: 20px;
        padding-right: 35px;
        padding-top: 20px;
    }

    .page-container .sidebar-menu .logo-env > div.sidebar-collapse {
        margin-top:10%;
        position:relative;
        float:right;
    }

    .page-container .sidebar-menu .logo-env > div.sidebar-mobile-menu {
        margin-top:10px;
        position:relative;
        float:right;
    }

    /*Parent menu background color*/
    .page-container .sidebar-menu{
        background: {{$menu_background_color}};
    }

    /*Child menu background color*/
    .page-container .sidebar-menu #main-menu li ul > li > a{
        background: {{$menu_background_color}};
    }

    /*Parent menu hover color*/
    .page-container .sidebar-menu #main-menu li > a:hover{
        background: {{$menu_background_hover_color}};
    }

    /*Child menu hover color*/
    .page-container .sidebar-menu #main-menu li ul > li > a:hover{
        background: {{$menu_background_hover_color}};
    }

    /*Menu text color*/
    .page-container .sidebar-menu #main-menu li a{
        color: {{$menu_text_color}};
    }  
    /*Menu hover color*/
    .page-container .sidebar-menu #main-menu li a:hover{
        color: {{$menu_text_hover_color}};
    }

    .page-container.sidebar-collapsed .sidebar-menu #main-menu > li > a > span:not(.badge) {
        background-color: {{$menu_background_color}};
        color: {{$menu_text_color}};
        border-right:1px solid;
    }

    body .page-container .sidebar-menu {
        border-right: 1px solid #ebebec;
    }

    .permission.checkbox-replace label {
        /*display: inline-table!important;*/
    }

    .permission.checkbox-replace .cb-wrapper + label {
        position: relative;
        top: -8px;
    }
	
    .page-container .sidebar-menu #main-menu li.active > a {
            background-color: {{$menu_background_hover_color}};
            color: {{$menu_text_hover_color}};
    }


</style>


<div class="sidebar-menu">
    <header class="logo-env">
        <!-- logo -->
        <div class="logo">
            <a href="{{url('/dashboard/view')}}">
                <img src="{{$logo}}" width="180" alt="site-logo" />
            </a>
        </div>

        <!-- logo collapse icon -->
        <div class="sidebar-collapse">
            <a href="#" class="sidebar-collapse-icon with-animation"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                <i class="entypo-menu"></i>
            </a>
        </div>

        <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
        <div class="sidebar-mobile-menu visible-xs">
            <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                <i class="entypo-menu"></i>
            </a>
        </div>
    </header>             
    <ul id="main-menu">        
        @if(in_array('dashboard',$modules))
        <li class="{{ $controller == 'dashboard' ? 'active' : ''}}">
                <a href="{{url('dashboard/view')}}">
                    <i class="entypo-gauge"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        @endif
        @if (in_array('location',$modules))
            <li class="{{$controller == 'location' ? 'active' : ''}}">
                <a href="{{url('location/view')}}">
                    <i class="entypo-location"></i>
                    <span>Locations</span>
                </a>
            </li>
        @endif
        @if (in_array('device', $modules))
            <li class="{{$controller == 'device' ? 'active' : ''}}">
                <a href="{{url('device/view')}}">
                    <i class="entypo-signal"></i>
                    <span>Devices</span>
                </a>
            </li>
       @endif
        @if (in_array('campaign', $modules))
            <li class="{{$controller == 'campaign' ? 'active' : ''}}">
                <a href="{{url('campaign/view')}}">
                    <i class="entypo-publish"></i>
                    <span>Campaigns</span>
                </a>
            </li>
        @endif
        @if(in_array('report', $modules))
            <li class="{{$controller == 'report' ? 'active' : ''}}">
                <a href="{{url('report/analytic')}}">
                    <i class="entypo-chart-pie"></i>
                    <span>Analytics &amp; Reports</span>
                </a>
            </li>
        @endif
        @if (in_array('timeline', $modules))
        <li class="{{$controller == 'timeline' ? 'active' : ''}}">
        	<a href="{{url('timeline/view')}}">
                <i class="entypo-clock"></i>
                <span>Timeline</span>
            </a>
        </li>
        @endif
        @if(in_array('social_app', $modules))                             
            <li class="{{$controller == 'user' ? 'active' : ''}}">
                <a href="{{url('user/connectsocialaccount')}}">
                    <i class="entypo-share"></i>
                    <span>Connect Social Accounts</span>
                </a>
            </li>
        @endif                                
        @if (in_array('subuser', $modules)) 
            <li class="{{$controller == 'subuser' ? 'active' : ''}}">
                <a href="{{url('subuser/viewsubuser')}}">
                    <i class="entypo-users"></i>
                    <span>Sub Users</span>
                </a>
            </li>
        @endif
        @if(Session::get('USER_TYPE') != '3')
            <li class="{{$controller == 'options' ? 'active' : ''}}">
                <a href="{{url('options/view')}}">
                    <i class="entypo-tag"></i>
                    <span>Branding</span>
                </a>
            </li>
        @endif
        @if (Session::get('USER_TYPE') != '3') 
            <li class="{{$controller == 'integration' ? 'active' : ''}}">
                <a href="{{url('integration/view')}}">
                    <i class="entypo-flash"></i>
                    <span>Integrations</span>
                </a>
            </li>
        @endif
        @if (Session::get('USER_TYPE') == '1')
            <li class="{{$controller == 'admintools' ? 'active opened' : ''}}">
				<a href="javascript:void(0);">
					<i class="fa fa-cogs"></i>
					<span>Admin Tools</span>
				</a>
				<ul>
					<li>
						<a href="{{url('admintools/activitylog')}}">
		                    <i class="fa fa-files-o"></i>
		                    <span>Activity Log</span>
		                </a>
					</li>
					<li>
						<a href="{{url('admintools/modifyuser')}}">
							<i class="fa fa-cog"></i>
							<span>Modify User</span>
						</a>
					</li>
					<li>
						<a href="{{url('overview/view')}}">
							<i class="fa fa-users"></i>
							<span>Manage Users</span>
						</a>
					</li>
				</ul>
			</li>
       @endif
    </ul>
</div>

<div class="modal custom-width" id="free_subscription_conformation">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><i class="fa fa-warning"></i> Free Subscription Confirmation</h3>
            </div>                   
            <div class="modal-body">
                <div id="free_subscription_conformation_msg"></div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-info" type="button">Ok</button>
            </div>							
        </div>
    </div>
</div>

<!-- Free Subscription Modal -->
<div class="modal custom-width" id="free_subscription_modal">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><i class="fa fa-warning"></i> Get Free Subscription</h3>
            </div>                   
            <div class="modal-body">
                <div class="alert alert-warning">You do not have any subscription plan, click here to get <a href="javascript:GetFreeSubscription();" class="btn btn-info">Free Subscription <img class="hide al_free_subscription" src="{{$assets_dir.'/images/ajax-loader.gif'}}" alt="ajax-loader"/></a></div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
            </div>							
        </div>
    </div>
</div>

<!-- Subscription Alert Modal -->
<div class="modal custom-width" id="subscription_alert">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">

                <h3 class="modal-title"><i class="fa fa-warning"></i> Device Limit Reached</h3>
            </div>                   
            <div class="modal-body">
                <div class="alert alert-success"><i class="fa fa-info-circle"></i> Please click Manage Plan to add more devices to your account.</div>
            </div>
            <div class="modal-footer">
                <a href="javascript:ManagePlan();" class="btn btn-success pull-right"><i class="fa fa-shopping-cart"></i> Manage Plan <img id="al_manage_plan" class="hide" src="{{$assets_dir.'/images/ajax-loader.gif'}}"/></a>
            </div>							
        </div>
    </div>
</div>

<!-- Sub User Modal -->
<div class="modal" id="sub_user_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Sub Users</h4>
            </div>
            <div class="modal-body">
                <div class="row">				
                    <div class="col-md-12">
                        <div id="sub_user_confirmation_message"></div>
                        <form id="sub_user_form" name="sub_user_form" method="post" action="" class="form-wizard validate">
                            <div class="steps-progress">
                                <div class="progress-indicator"></div>
                            </div>
                            <ul>
                                <li class='active'><a class="tab" href="#tab-1" data-toggle="tab"><span>1</span>Sub Users Details</a></li>
                                <li><a class="tab" href="#tab-2" data-toggle="tab"><span>2</span>Permission</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <h3>Sub Users Details</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name" class="control-label">First Name</label>
                                                <input placeholder="Your first name" id="first_name" name="first_name" value="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name" class="control-label">Last Name</label>
                                                <input placeholder="Your last name" id="last_name" name="last_name" value="" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="phone" class="control-label">Mobile Phone</label>
                                                <input placeholder="Your phone number" data-validate="number" id="phone" value="" name="phone" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label class="control-label">Email Address</label>									
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="entypo-mail"></i>
                                            </div>										
                                            <input type="text" value="" placeholder="Your email" data-validate="email" id="email" name="email" class="form-control">
                                        </div>
                                        <span class="form-control-feedback"><img id="al_email_exist" class="hide" src="{{$assets_dir.'/images/ajax-loader.gif'}}"></span>
                                        <label id="msg_email_exist" style="color:red;"></label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Choose Password</label>											
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="entypo-key"></i>
                                                    </div>												
                                                    <input type="password" placeholder="Enter strong password" id="password" name="password" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">						
                                            <div class="form-group">
                                                <label class="control-label">Repeat Password</label>											
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="entypo-cw"></i>
                                                    </div>												
                                                    <input type="password" placeholder="Confirm password" data-message-equal-to="Passwords doesn't match." data-validate="equalTo[#password]" id="confirm_password" name="confirm_password" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-2">
                                    <h3>Permission</h3>
                                    <ul class="icheck-list">                                   
                                    @foreach ($module_array as $key => $value)                                    
                                    <li>
								        <input id="checkbox_{{$key}}" type="checkbox" class="icheck chk_module" name="user_permission[]" value="{{ $key }}">
								        <label for="checkbox_div_{{ $key }}">{{ $value }}</label>
								        
								        @if ($key == 'location')								        
									        	<div id='location_list' class='hide'>
									        		<select id='location' class='select2' name='location[]' multiple='multiple'>
									        			@foreach($location_list as $location)
									        				<option value={{$location->id}}>{{$location->name}}</option>
									        			@endforeach
									        		</select>
									        	</div>									    								       
								        @endif
								        @if ($key == 'campaign')							        	
												<div id='campaign_list' class='hide'>
													<select id='campaign' class='select2' name='campaign[]' multiple='multiple'>
													@foreach($campaigns as $campaign)
														<option value="{{$campaign->id}}">{{$campaign->name}}</option>
												    @endforeach
													</select>
												</div>										
								       @endif
								    </li>
                                    @endforeach
                                    </ul>
                                    <div class="form-group">
                                        <input type="hidden" name="hdn_action" id="hdn_action" value=""/>
                                        <input type="hidden" name="hdn_sub_user_id" id="hdn_sub_user_id" value=""/>
                                    </div>
                                </div>
                                <ul class="pager wizard">
                                    <li class="previous">
                                        <a class="left-open" href="#"><i class="entypo-left-open"></i> Previous</a>
                                    </li>

                                    <li class="next">
                                        <a class="right-open" href="#">Next <i class="entypo-right-open"></i></a>
                                    </li>
                                    <li class="pull-right">
                                        <button type="button" id="btn_save_subuser" name="btn_save_subuser" class="btn btn-info hide" onClick="javascript:save_sub_user();"><i class="fa fa-save"></i> Save Sub User <img id="al_sub_user" src="{{$assets_dir.'/images/ajax-loader.gif'}}" class="hide"/></button>
                                    </li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal social_user_details_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center" style="transform: translate(0, 25%);">
                        <p id="su_profile_picture"></p>
                        <h4 class="modal-title su_full_name" style="font-weight: bold; padding: 10px;"></h4>
                        <div class="custom-font-size" id="social_media_icon" style="padding: 10px;"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12" style="margin-top:70px; text-align:left!important;">
                      <div class="col-md-6">
                        <p><label><b>Full Name:</b> <span class="su_full_name"></span></label></p>
                        <p><label><b>User Name:</b> <span id="su_username"></span></label></p>
                        <p><label><b>Email:</b> <span id="su_email"></span></label></p>
                        <p><label><b>Device MAC:</b> <span id="su_device_mac"></span></label></p>
                        <p><label><b>Client MAC:</b> <span id="su_client_mac"></span></label></p>
                        <p><label><b>Device OS:</b> <span id="su_device_os"></span></label></p>
                        <p><label><b>Device Type:</b> <span id="su_device_type"></span></label></p>
                      </div>
                      <div class="col-md-6">
                        <p><label><b>Location:</b> <span id="su_location"></span></label></p>
                        <p><label><b>Campaign:</b> <span id="su_campaign"></span></label></p>
                        <p><label><b>Gender:</b> <span id="su_gender"></span></label></p>
                        <p><label><b>Time Zone:</b> <span id="su_time_zone"></span></label></p>
                        <p><label><b>Connected at:</b> <span id="su_last_connected"></span></label></p>
 </div>                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    $(document).ready(function () {
    	$('.chk_module').on('ifChanged', function(event){
            var loc_id = $(this).val();
            if (loc_id === 'location') {
                if ($(this).is(':checked') === true) {
                    $("#location_list").removeClass('hide');
                } else {
                    $("#location_list").addClass('hide');
                    $("#location").select2("data", '');
                }
            }
            if (loc_id === 'campaign') {
                if ($(this).is(':checked') === true) {
                    $("#campaign_list").removeClass('hide');
                } else {
                    $("#campaign_list").addClass('hide');
                    $("#campaign").select2("data", '');
                }
            }
        });
        $(".change_button").click(function () {
            var value = $("#device_quantity").val();
            $("#allowed_device").html(value);
        });
        

        $(".right-open").click(function () {
            $(this).addClass('hide');
            $("#btn_save_subuser").removeClass('hide');
        });
        $(".left-open").click(function () {
            $(".right-open").removeClass('hide');
            $("#btn_save_subuser").addClass('hide');
        });
        $(".tab").click(function () {
            var obj = $(this);
            var span_value = obj.find('span').html();
            if (span_value === '1') {
                $(".right-open").removeClass('hide');
                $("#btn_save_subuser").addClass('hide');
            } else {
                $(".right-open").addClass('hide');
                $("#btn_save_subuser").removeClass('hide');
            }
        });


        $("#email").blur(function () {
            $("#al_email_exist").removeClass('hide');
            var email = $("#email").val();
            $.ajax({
                url: '{{url("ajax/CheckEmailExist")}}',
                data: {email: email},
                type: 'post',
                complete: function (output) {
                    $("#al_email_exist").addClass('hide');
                    var isExist = output.responseText;
                    if (isExist === '1') {
                        $("#msg_email_exist").html('This email is already in use. Please try a different email address.');
                        $(".next").addClass('hide');
                        //$("#btn_save_subuser").addClass('hide');
                    } else {
                        $("#msg_email_exist").html('');
                        $(".next").removeClass('hide');
                        //$("#btn_save_subuser").removeClass('hide');
                    }
                }
            });
        });    
    });/*END DOCUMENT*/
    
    function getFirmwaresInof(model){
        $.ajax({
            url: '{{url("ajax/Getfirmwaresinof")}}',
            type: 'POST',
            data: {model:model},
            success: function(result){
            	var obj = jQuery.parseJSON(result);
            	$("#router_new_image").attr('src', obj.device_image);
            	var firmwares_warning_msg = '<div class="alert alert-danger"><strong>Please be sure to use the correct firmware when flashing your device otherwise you risk your router becoming inoperable. Always update firmware with a hardwired ethernet cable.</strong></div>';
                $("#add_new_firmwares").html(firmwares_warning_msg + obj.device_firmwares);
            }
        });
    }

    function showSubUserForm() {
        resetForm();
        $("#location_list").addClass('hide');
        $("#campaign_list").addClass('hide');
        $("#hdn_sub_user_id").val('');
        $("#hdn_action").val('add');
        $('#sub_user_modal').modal('show');
    }

    function save_sub_user() {
        var frmData = $("#sub_user_form").serialize();
        $("#al_sub_user").removeClass('hide');
        $.ajax({
            url: '{{url("ajax/SaveSubUser")}}',
            data: frmData,
            type: 'post',
            complete: function (output) {
                $("#al_sub_user").addClass('hide');
                var obj = jQuery.parseJSON(output.responseText);
                $("#sub_user_confirmation_message").addClass('alert alert-' + obj.style);
                $("#sub_user_confirmation_message").html(obj.message);
                if (obj.style === 'success') {
                    window.setTimeout('location.reload()', 1000);
                }
            }
        });
    }

    function editsubuser(id) {
        $("#preloader").removeClass('hide');
        resetForm();
        $.ajax({
            url: "{{url('ajax/GetSubUserById')}}",
            data: {id: id},
            type: "post",
            success: function (output) {
                $("#preloader").addClass('hide');
                $("#hdn_sub_user_id").val(id);
                $("#hdn_action").val('edit');
                var obj = jQuery.parseJSON(output);
                $("#first_name").val(obj.first_name);
                $("#last_name").val(obj.last_name);
                $("#email").val(obj.email);
                $("#phone").val(obj.phone);
                $("#email").attr('disabled', 'disabled');

                if (obj.module_array !== undefined && obj.module_array !== "") {
                    var module_array = jQuery.parseJSON(obj.module_array);
                                        
                    $.each(module_array, function( index, value ) {
                    	$('#checkbox_' + value).iCheck('check');
                    	if(value==='location'){
                    		$("#location").select2("data", $.parseJSON(obj.location_data));
                        	$("#location_list").removeClass('hide');
                        }
                    	if(value==='campaign'){
                    		$("#campaign").select2("data", $.parseJSON(obj.campaign_data));
                        	$("#campaign_list").removeClass('hide');
                        }
                    });
                    /*$('input:checkbox[class=chk_module]').each(function () {
                        var my_val = $(this).val();
                        var status = jQuery.inArray(my_val, module_array);
                        if (status >= 0) {
                            $('#chk_module_' + my_val).prop('checked', true);
                            $('#checkbox_div_' + my_val).addClass('checked');
                            if (parseInt(my_val) === 3) {
                                $("#location_list").removeClass('hide');
                            }
                        }
                    });*/
                }

                /*if (obj.location_data !== undefined && obj.location_data !== "") {
                    $("#location").select2("data", jQuery.parseJSON(obj.location_data));
                    $("#location").removeClass('hide');
                } else {
                    $("#location").select2("data", '');
                    $("#location_list").addClass('hide');
                }*/
                $("#password").removeAttr('data-validate');
                $("#confirm_password").removeAttr('data-validate');
                $('#sub_user_modal').modal('show');
            }
        });
    }

    function resetForm() {
        $("#sub_user_form").find(':input').each(function () {
            switch (this.type) {
                case 'password':
                    $(this).val('');
                    break;
                case 'select-multiple':
                case 'select-one':
                case 'email':
                    $(this).val('');
                    $("#email").removeAttr('disabled');
                    break;
                case 'text':
                    $(this).val('');
                    break;
                case 'checkbox':
                    $(".chk_module").removeAttr('checked');
                    $(".checkbox ").removeClass('checked');
                    break;
            }
        });
    }

    function deletesubuser(id) {
        $("#al_sub_user_action_" + id).removeClass('hide');
        alert('delete is not working yet. will be soon.. :)');
        $("#al_sub_user_action_" + id).addClass('hide');
    }

    function ManagePlan() {
        $("#subscription_alert").modal('hide');
        var is_card = {{Session::get('plan_info')['is_card'] == null ? "0" : Session::get('plan_info')['is_card']}};
        if (parseInt(is_card) === 1) {
            $("#rb_old_card").attr('checked', 'checked');
            $("#rb_new_card").removeAttr('checked');
            $("#new-card-info").addClass('hide');
        } else {
            $("#rb_new_card").attr('checked', 'checked');
            $("#rb_old_card").removeAttr('checked');
            $("#new-card-info").removeClass('hide');
        }
        $("#stripe_payment_form").modal('show');
    }



    function GetSocialUserDetail(id) {
        $("#preloader").removeClass('hide');
        $.ajax({
            url: '{{url("ajax/GetSocialUserDetail")}}',
            data: {id: id},
            type: 'POST',
            complete: function (output) {
                var fbprofile = "https://www.facebook.com/";
                var liprofile = "https://www.linkedin.com/";
                var twprofile = "https://twitter.com/";
                var gpprofile = "https://plus.google.com/";
                var igprofile = "https://instagram.com/";
                
                var user = jQuery.parseJSON(output.responseText);
                var picture_url = user.picture_url;
                var email = user.email === "" ? "n/a" : user.email;
                var gender = user.gender === "" ? "n/a" : user.gender;
                var timezone = user.timezone === "" ? user.location_timezone : user.timezone;
                var username = user.username === "" ? "n/a" : user.username;
                var campaign = user.campaign === "" ? "n/a" : user.campaign;
                var device_mac = user.device_mac === "" ? "n/a" : user.device_mac;
                var client_mac = user.client_mac === "" ? "n/a" : user.client_mac;
                var device_os = user.device_os === "" ? "Unknown" : user.os_name;
                var device_type = user.device_type === "" ? "Unknown" : user.device;
                //var last_connected = user.system_timezone !== "" ? user.last_connected + ' ('+user.system_timezone+')' : user.last_connected;
                if (picture_url === "") {
                    picture_url = "{{$assets_dir.'/images/no_photo150x150.jpg'}}";
                }

                var profile_photo = '<a id="su_picture_link" target="_blank" href=""><img class="img-circle thumbnail-highlight" src="'+picture_url+'" alt="profile-picture" width="150px"></a>';

                $(".su_full_name").html(user.full_name);
                $("#su_profile_picture").html(profile_photo);
                $("#su_email").html(email);
                $("#su_username").html(username);
                $("#su_device_mac").html(device_mac);
                $("#su_client_mac").html(client_mac);
                $("#su_device_os").html(device_os);
                $("#su_device_type").html(device_type);
                $("#su_last_connected").html(user.added_datetime);
                $("#su_location").html(user.location);
                $("#su_campaign").html(campaign);
                $("#su_gender").html(gender);
                $("#su_time_zone").html(timezone);

				var social_media_icon = "<a id='mi_fb' href='javascript:void(0);' target='_blank'><i class='fa fa-facebook-square'></i></a>"+
										"<a id='mi_tw' href='javascript:void(0);' target='_blank'><i class='fa fa-twitter-square'></i></a>"+
										"<a id='mi_li' href='javascript:void(0);' target='_blank'><i class='fa fa-linkedin-square'></i></a>"+
										"<a id='mi_gp' href='javascript:void(0);' target='_blank'><i class='fa fa-google-plus-square'></i></a>"+
										"<a id='mi_ig' href='javascript:void(0);' target='_blank'><i class='fa fa-instagram'></i></a>"+
										"<a id='mi_en' href='javascript:void(0);' target='_blank'><i class='fa fa-envelope-square'></i></a>";

										$("#social_media_icon").html(social_media_icon);
                $("#social_media_icon a .fa").attr('style', 'color:#C5DADD;');
                switch (user.media) {
                    case 'FBuser':
                        $("#mi_fb").removeAttr('href');
                        $("#mi_fb").attr('href', fbprofile+user.social_network_id);
                        $("#su_picture_link").removeAttr('href');
                        $("#su_picture_link").attr('href', fbprofile+user.social_network_id);
                        $("#mi_fb .fa").attr('style', 'color:#3b5998;');
                        break;
                    case 'TWuser':
                    	$("#mi_tw").removeAttr('href');
                        $("#mi_tw").attr('href', twprofile+user.username);
                        $("#su_picture_link").removeAttr('href');
                        $("#su_picture_link").attr('href', twprofile+user.username);
                        $("#mi_tw .fa").attr('style', 'color:#55acee;');
                        break;
                    case 'LIuser':
                    	$("#mi_li").removeAttr('href');
                        $("#mi_li").attr('href', liprofile+'profile/view?id='+user.social_network_id);
                        $("#su_picture_link").removeAttr('href');
                        $("#su_picture_link").attr('href', liprofile+'profile/view?id='+user.social_network_id);
                        $("#mi_li .fa").attr('style', 'color:#0976b4;');
                        break;
                    case 'GPuser':
                    	$("#mi_gp").removeAttr('href');
                        $("#mi_gp").attr('href', gpprofile+user.social_network_id);
                        $("#su_picture_link").removeAttr('href');
                        $("#su_picture_link").attr('href', gpprofile+user.social_network_id);
                        $("#mi_gp .fa").attr('style', 'color:#dd4b39;');
                        break;
                    case 'IGuser':
                    	$("#mi_ig").removeAttr('href');
                        $("#mi_ig").attr('href', igprofile+user.username);
                        $("#su_picture_link").removeAttr('href');
                        $("#su_picture_link").attr('href', igprofile+user.username);
                        $("#mi_ig .fa").attr('style', 'color:#3f729b;');
                        break;
                    case 'Cuser':
                        $("#mi_en .fa").attr('style', 'color:#F7931E;');
                        $("#su_picture_link").removeAttr('href');
                        $("#su_picture_link").attr('href', 'javascript:void(0);');
                        break;
                }
                $("#preloader").addClass('hide');
                $(".social_user_details_modal").modal('show');
            }
        });
    }
</script>

