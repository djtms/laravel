@extends('template.layout')
@section('content')
<div class="page-container">
	<!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
         @include('template.headermenu')
        <hr />
		<div class="row">
			<div class="col-md-4">
				<h2 style="margin-top: 0px;">
					<i class="fa fa-user"></i> My Account
				</h2>
			</div>
			<div class="col-md-8"></div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div id="my_confirmation_message"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title seventh-tour">
							<strong>Personal Information</strong>
						</h3>
					</div>
					<div class="panel-body">
						<form role="form" name="frm_user_profile" id="frm_user_profile"
							class="validate" enctype="multipart/form-data">
							<div class="form-group">
								<label for="email">Email/Username</label> <input type="email"
									class="form-control" id="email" name="email"
									disabled="disabled"
									value="{{$user->email_address}}"
									placeholder="Email/Username">
							</div>
							<div class="form-group">
								<label for="first_name">First Name</label> <input type="text"
									class="form-control" id="first_name" name="first_name"
									data-validate="required"
									value="{{$user->first_name}}"
									placeholder="First Name">
							</div>
							<div class="form-group">
								<label for="last_name">Last Name</label> <input type="text"
									class="form-control" id="last_name" name="last_name"
									value="{{$user->last_name}}"
									placeholder="Last Name">
							</div>
							<div class="form-group">
								<label for="mobile_phone">Mobile Phone</label> <input
									type="text" class="form-control" id="mobile_phone"
									name="mobile_phone" data-validate="required"
									value="{{$user->mobile_phone}}"
									placeholder="Mobile Phone">
							</div>
							<div class="form-group">
								<label class="second-tour" for="time_zone">Time Zone</label>                        
		                        <select id="time_zone" name="time_zone"	data-validate="required" class="form-control select2">		                            
		                              @foreach ($timezones as $key =>$value)
		                                <option value="{{$key}}"  {{$user->time_zone == $key ? "selected='selected'" : ""}} >{{$value}}</option>
		                              @endforeach
		                        </select>
							</div>
							<div class="form-group">
								<label for="profile_photo">Profile Photo</label>
								<div>
									<div id="file-upload">
										<div
											class="fileinput fileinput-{{$profile_photo == '' ? 'new' : 'exists' }}"
											data-provides="fileinput">
											<div class="fileinput-new thumbnail"
												style="width: 200px; height: 150px;">
												<img
													src="{{$assets_dir.'/images/200x150.gif'}}"	alt="...">
											</div>
											<div class="fileinput-preview fileinput-exists thumbnail"
												style="max-width: 200px; max-height: 150px;"> 									                                     
										    <img src='{{Session::get("PROFILE_PHOTO")}}'/>
                                            </div>
											<div>
												<span class="btn btn-default btn-file"><span
													class="fileinput-new">Select image</span><span
													class="fileinput-exists">Change</span> <input type="file"
													name="profile_photo"></span> <a href="#"
													class="btn btn-default fileinput-exists"
													data-dismiss="fileinput">Remove</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h2>Change Password</h2>
							<hr>
							<div class="form-group">
								<label for="new_password">New Password</label> <input
									type="password" class="form-control" id="new_password"
									name="new_password" placeholder="Enter New Password">
							</div>
							<div class="form-group">
								<label for="confirm_password">Confirm Password</label> <input
									type="password" class="form-control" name="confirm_password"
									id="confirm_password" data-validate="equalTo[#new_password]"
									data-message-equal-to="Passwords doesn't match."
									placeholder="Re-Enter New Password">
							</div>
							<a name="profile_update"></a>
							<button name="profile_update" type="submit" class="btn btn-info"
								onclick="javascript:UpdateProfile();">
								<i class="fa fa-save"></i> Update
							</button>
						</form>
					</div>
				</div>
			</div>
			<div class='col-sm-6'>
			    @if(Session::get('USER_TYPE') != '1')                   
                    <div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title seventh-tour">
							<strong>Your Account</strong>
						</h3>
					</div>
					<div class="panel-body">
							@if($recurly_data['subscription_id'] != "")
							<h4>
							<strong>Current Subscription: {{"$".$recurly_data['plan_price']."/".$recurly_data['plan_interval']}}</strong>
						    </h4>                            
                            <h4>
							<strong>Devices: {{$recurly_data['allowed_quantity']}}</strong>
						</h4>
						<p>
							<a class="btn btn-sm btn-danger seventh-tour"
								href="javascript:updateaccount();"><i
								class='fa fa-shopping-cart'></i> Update account</a>
						</p>
					     @else
							<p>
							<a class="btn btn-lg btn-danger btn-block"
								href="{{url('user/billingsetup')}}"><i
								class='fa fa-shopping-cart'></i> Activate Account</a>
						  </p>
						@endif
						</div>
				</div>
                @endif
            </div>
		</div>
	</div>
</div>


@if($recurly_data['subscription_id'] != "")
<!-- Stripe Payment Form -->
<div class="modal custom-width" id="stripe_payment_form">
	<div class="modal-dialog" style="width: 60%">
		<div class="modal-content">
			<form action="" method="POST" id="payment_form" class="validate"
				name="payment_form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
					<h3 class="modal-title">
						<i class="fa fa-shopping-cart"></i> Pay With Card
					</h3>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="confirmation_message"></div>
							<div class="row">
								<div class="col-md-5">
									<h4 style="padding-bottom: 15px;">
										<b>Add Devices To My Account</b>
									</h4>
									<div class="row">
										<div class="col-md-4">
											<label style="font-size: 28px;" class="label label-success">$30</label>
											<!-- <input type="hidden" id="hdn_device_price" name="hdn_device_price" value="" /> -->
										</div>
										<div class="col-md-2 text-center">
											<label style="font-size: 28px;">X</label>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<div class="input-spinner">
													<button type="button" class="btn btn-default"
														style="height: 42px;" data-step="-1">-</button>
													<input type="text" name="number_of_device"
														id="number_of_device" class="form-control size-1"
														value="{{$recurly_data['allowed_quantity']}}"
														data-min="1" data-max="51"
														style="height: 42px; text-align: center; font-size: 20px;" />
													<button type="button" class="btn btn-default"
														style="height: 42px;" data-step="1">+</button>
												</div>												
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">Per month per device</div>
										<div class="col-md-4">Devices</div>
									</div>
									<hr>
									<div class="row" style="font-size: 17px; font-weight: bold;">
										<div class="col-md-3">
											<label>Total:</label>
										</div>
										<div class="col-md-9" style="text-align: right;">
											<label id="price" style="font-size: 25px; color: #00A651;">{{'$'.$recurly_data['plan_price']}}</label>
											<p>{{$recurly_data['plan_interval']}}</p>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-md-offset-1">
									<h4 style="padding-bottom: 15px;">
										<b>Payment Information</b>
									</h4>
									<div class="row">
										<div class="col-md-4">
											<input type="radio" class="icheck" id="rb_old_card" value="0"
												name="payment_type"> <label>Card on file</label>
										</div>
										<div class="col-md-8">
                                            @if ($recurly_data['last_four'] != "")
                                            <div class="input-group">
												<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
												<input type="text" disabled="disabled"
													value="{{$recurly_data['last_four']}}"
													class="form-control" />
											</div>
                                            @endif
                                        </div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<input type="radio" class="icheck" id="rb_new_card" value="1"
												name="payment_type"> <label>Use new card</label>
										</div>
									</div>

									<div class="row hide" id="new-card-info">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">First Name</label> <input
														type="text" name="b_first_name"
														value="{{$recurly_data['first_name']}}"
														class="form-control" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">Last Name</label> <input
														type="text" name="b_last_name"
														value="{{ $recurly_data['last_name']}}"
														class="form-control" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label">Card Number</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-credit-card"></i>
												</div>
												<input type="text" name="card_number" class="form-control" size="20" maxlength="16" data-stripe="number" />
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">Expiration (MM/YYYY)</label>
													<div class="input-group">
														<div class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</div>
														<input type="text" name="expiration" data-mask="mm/yyyy"
															class="form-control">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">CVC</label>
													<div class="input-group">
														<div class="input-group-addon">
															<i class="fa fa-cc"></i>
														</div>
														<input type="text" name="cvc" class="form-control"
															data-mask="999" data-stripe="cvc" />
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label">Address</label> <input
												type="text" name="b_address"
												value="{{$recurly_data['address']}}"
												class="form-control" />
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">City</label> <input
														type="text" name="b_city"
														value="{{$recurly_data['city']}}"
														class="form-control" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="control-label">State</label> <input
														type="text" name="b_state"
														value="{{$recurly_data['state']}}"
														class="form-control" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label class="control-label">Zip</label> <input type="text"
														name="b_zip" value="{{ $recurly_data['zip']}}"
														class="form-control" />
												</div>
											</div>
											<div class="col-md-8">
												<div class="form-group">
													<label class="control-label">Country</label> <input
														type="text" name="b_country"
														value="{{$recurly_data['country']}}"
														class="form-control" />
												</div>
											</div>
										</div>
									</div>
									<hr>
									<h4>
										<b>Plan Details</b>
									</h4>
									<div class="row">
										<div class="col-md-12" style="font-size: 16px; color: black;">
											<b>Current Plan</b><br>
                                            Your current plan allows {{$recurly_data['allowed_quantity']}} active devices<br>
                                            Cost: {{ '$' . $recurly_data['plan_price'] . '/' . $recurly_data['plan_interval'] }}<br>
                                            Your next billing date: 
                                        </div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
					<a id="process_payment" href="javascript:ProcessPayment();"
						class="btn btn-red"><i class="fa fa-shopping-cart"></i> Update
						Plan <img id="al_process_payment" class="hide"
						src='{{$assets_dir.'/images/ajax-loader.gif'}}' /></a> <input type="hidden"
						value="{{$recurly_data['allowed_quantity'] }}"
						id="hdn_current_device" name="hdn_current_device" /> <input
						type="hidden" value="{{$recurly_data['plan_price']}}"
						id="hdn_current_ammount" name="hdn_current_ammount" /> <input
						type="hidden" value=""
						name="hdn_customer_id" /> <input type="hidden"
						value="{{$recurly_data['subscription_id']}}"
						name="hdn_subscription_plan_id" />

				</div>
			</form>
		</div>
	</div>
</div>
@endif

<script>
    $(document).ready(function () {

    	/*Recurly Plans*/
    	var plans = '{!! json_encode($recurly_data['plan_list'])!!}';
    	var plan_array = jQuery.parseJSON(plans);
    	/*END*/
        
        $('#file-upload').on('change.bs.fileinput', function (event) {
            event.stopPropagation();
            var photo_name = '{{$profile_photo}}';
            if (photo_name !== '') {
                var option_name = 'profile_photo_{{$owner}}';
                var photo_path = photo_name;
                $.ajax({
                    url: '{{url("ajax/DeletePicture")}}',
                    type: 'POST',
                    data: {option_name: option_name, photo_path: photo_path},
                    complete: function (output) {

                    }
                });
            }
        });
        
   @if (Request::has('get') && Request::input('get') == "device")
	   window.setTimeout(function(){$('#stripe_payment_form').modal('show');},1500);
   @endif
                
        $("#number_of_device").keyup(function (e) {
            var qty = this.value;
            if (!isNaN(qty)) {
	            if(qty > 0 && qty <= 51){
	                var plan_id = 'mywifi_'+qty;
					var plan_price = plan_array[plan_id];
					$("#price").html('$' + plan_price);
	            }
	            $("#process_payment").removeClass('hide');
            } else {
                $("#process_payment").addClass('hide');
                alert('Only numeric value is allowed!');
            }
        });
        
        $('input:radio[name=payment_type]').on('ifChanged', function(event){
        	var value = $(this).val();
            if (value === '1') {
                $("#new-card-info").removeClass('hide');
                //$("#notice").html('<i class="fa fa-info-circle"></i> You will be charged from your given credit card.');
            } else {
                $("#new-card-info").addClass('hide');
                //$("#notice").html('<i class="fa fa-info-circle"></i> You will be charged from your existing credit card.');
            }
		});
        
    });
    function UpdateProfile() {
        $("#frm_user_profile").on('submit', (function (e) {
            $("#preloader").removeClass('hide');
            //var frmData = $("#frm_user_profile").serialize();
            e.preventDefault();
            $.ajax({
                url: '{{url("ajax/UpdateProfile")}}',
                type: 'POST',
                data: new FormData(this), // Data sent to server, a set of key/value pairs representing form fields and values 
                contentType: false, // The content type used when sending data to the server. Default is: "application/x-www-form-urlencoded"
                cache: false, // To unable request pages to be cached
                processData: false, // To send DOMDocument or non processed data file it is set to false (i.e. data should not be in the form of string)
                success: function (result) {
                    $("#preloader").addClass('hide');
                    var obj = jQuery.parseJSON(result);
                    //var output = response.responseText;
                    $("#my_confirmation_message").html(obj.message);
                    if (obj.status === 'success') {
                        $("#timezone-alert").remove();
                        $("#user_full_name").text(obj.full_name);
                        if ($.cookie("current_class") === 'second-tour') {
                            next_tour();
                        }else{
                        	$("body").scrollTop(0);
                        }
                    }else{$("body").scrollTop(0);}
                }
            });
        }));
    }

    function updateaccount() {
        var last_four = '{{$recurly_data['last_four']}}';
        if (last_four != "") {
        	$('#rb_new_card').iCheck('uncheck');
        	$('#rb_old_card').iCheck('check');
            $("#new-card-info").addClass('hide');
        } else {
            $("#rb_new_card").iCheck('check');
            $("#rb_old_card").iCheck('uncheck');
            $("#new-card-info").removeClass('hide');
        }

        $("#stripe_payment_form").modal('show');
    }

    function ProcessPayment() {
        $("#al_process_payment").removeClass('hide');
        var redirect_url = '{{url("user/editprofile")}}';
        var formData = $("#payment_form").serialize();
        $.ajax({
            url: '{{url("ajax/ProcessPayment")}}',
            data: formData,
            type: 'post',
            complete: function (response) {
                $("#al_process_payment").addClass('hide');
                var obj = $.parseJSON(response.responseText);
                $('#confirmation_message').removeAttr('class');
                $('#confirmation_message').attr('class','alert alert-' + obj.style);
                $('#confirmation_message').html(obj.message);
                if (obj.style === 'success') {
                    window.setTimeout(function () {
                        //location.reload();
                        window.location = redirect_url;
                    }, 1500);
                }
            }
        });
    }
</script>
<script src="{{$assets_dir.'/js/fileinput.js'}}"></script>
@endsection