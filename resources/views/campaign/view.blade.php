@extends('template.layout')
@section('content')
<input type="hidden" id="token" value="{{csrf_token()}}"/>
<div class="page-container">	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
		<div class="row">
			<div class="col-sm-4">
				<h2 style="margin-top: 0px;">
					<i class="entypo-publish"></i>Campaigns
				</h2>
			</div>
			<div class="col-sm-8 text-right">
				<button type="button"
					class="btn btn-red btn-icon icon-left btn-lg fifth-tour"
					onclick="javascript:addnew();">
					Add New Campaign <i class="entypo-plus-circled"></i>
				</button>
			</div>
		</div>
		 {!! Session::get('SESSION_MESSAGE')!!}
		 {{Session::forget('SESSION_MESSAGE')}}
        @if (Session::get('USER_TYPE') != '3')
        <div class="row">
			<div class="col-md-12 search-box">
				<input type="text" id="campaign_search_value" class="form-control input-lg" placeholder="Search your campaign here by campaign title, campaign user or campaign username and hit ENTER">
			</div>
		</div>
        @endif
        <br />
		<div id="campaigns">
			<!-- content will be loaded here -->
		</div>
        
        {!! $footer !!}
    </div>
	<!-- /.main-content-->
</div>
<div class="modal" id="campaign_alert_modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form action="{{url('campaign/delete')}}"
				method="POST">
				<div id="modal_body" class="modal-body" style="font-weight: bold; color: black;"></div>
				<div id="modal_footer" class="modal-footer" style="text-align: center;"></div>
			</form>
		</div>
	</div>
</div>

<div class="modal" id="clone_campaign_alert_modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div id="modal_body" class="modal-body" style="font-weight: bold; color: black;">
				<i class="entypo-attention"></i> Are you sure you want to clone this campaign?
			</div>
			<div class="modal-footer" style="text-align: center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				<button id="btn_clone_campaign" type="button" class="btn btn-red">Yes</button>
			</div>
		</div>
	</div>
</div>
<div class="modal custom-width" id="add_new_campaign">
	<div class="modal-dialog modal-size">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="rootwizard" name="campaign_form" method="post" action="" class="validate form-wizard frm-campaign">
                        {!! csrf_field() !!}
						<div class="steps-progress" style="margin-left: 23%; margin-right: 24%;">
							<div class="progress-indicator" style="width: 100%;"></div>
						</div>
						<ul id="campaign_steps">
							<li class="active"><a href="#tab1" class="tab12" titte="tab1" data-toggle="tab"><span>1</span>Step 1</a></li>
							<li class=""><a href="#tab2" class="tab12" titte="tab2" data-toggle="tab"><span>2</span>Step 2</a></li>
							<li class=""><a href="#tab3" class="tab12" titte="tab3" data-toggle="tab"><span>3</span>Step 3</a></li>
							<li class=""><a href="#tab4" class="tab12" titte="tab4" data-toggle="tab"><span>4</span>Step 4</a></li>
						</ul>
						<div class="col-md-12">
							<div class="tab-content">
								<div id="tab1" class="tab-pane active">
									<div class="row">
										<div class="col-md-7">
											<div class="row">
												<div class="col-sm-12">
													<div class="form-group">
														<label for="field-1"
															class="col-sm-5 control-label campaign-left">Campaign
															Name</label>
														<div class="col-sm-7">
															<input type="hidden" name="id-campaign" id="id-campaign"
																value="" /> <input type="text" class="form-control"
																id="campaign-name" name="campaign-name" required=""
																placeholder="My Test Campaign">
														</div>
													</div>
<br><br>
													<div class="form-group">
														<label for="field-1"
															class="col-sm-5 control-label campaign-left">SSID Name</label>
														<div class="col-sm-7">
															<input type="text" class="form-control" name="ssid-name" id="ssid-name" maxlength="32" placeholder="Enter your SSID here" data-mask="^[a-zA-Z0-9_-\s]*$" data-is-regex="true" required>
															<p class="instruction_message">Only alphanumeric, space and underscore are allowed.</p>
														</div>
													</div>

												</div>
											</div>
											<div class="row">
												<div class="col-xs-8 social-icon" style="text-align: left;">
													<i class="fa fa-facebook-square"></i> <label>Enable Facebook Login</label>
												</div>
												<div class="col-xs-4">
													<div class="make-switch pull-right"
														style="margin-top: 6px;">
														<input type="checkbox" data-on-color="success"
															data-size="small" onchange="show('facebook_log');"
															id="face_log">
													</div>
												</div>
											</div>
											<div class="row your_app">
												<div class="col-sm-5 app-title"><label>Choose Your App</label></div>
												<div class="col-sm-7">

													<div class="form-group">
														<div class="col-xs-12 text-right">
															<input type="hidden" id="app-facebook-hidden" name="app-facebook-hidden" value="0" />
															<select class="form-control" id="app-facebook" name="app-facebook">
																@if(count($fb_apps) > 0)
	                                                            <option value="">Select Your App</option>
																@foreach ( $fb_apps as $facebook )
																<option value="{{$facebook->id}}">{{$facebook->app_name}}</option>
																@endforeach
																@else
																<option value="">-- No apps found --</option>
																@endif
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-8 social-icon" style="text-align: left;">
													<i class="fa fa-twitter-square"></i> <label>Enable Twitter Login</label>
												</div>
												<div class="col-xs-4">
													<div class="make-switch pull-right"
														style="margin-top: 6px;">
														<input type="checkbox" data-on-color="success"
															data-size="small" onchange="show('twitter');"
															id="show-twitter">
													</div>
												</div>
											</div>
											<div class="row your_twitter">
												<div class="col-sm-5 app-title"><label>Choose Your App</label></div>
												<div class="col-sm-7">

													<div class="form-group">
														<div class="col-xs-12 text-right">
															<input type="hidden" id="app-twitter-hidden" name="app-twitter-hidden" value="0" />
															<select class="form-control" id="app-twitter" name="app-twitter">
																@if (count($tw_apps) > 0)
	                                                            <option value="">Select Your App</option>
																@foreach ( $tw_apps as $twitter )
																<option value="{{$twitter->id}}">{{$twitter->app_name}}</option>
																@endforeach
																@else
																<option value="">-- No apps found --</option>
																@endif
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-8 social-icon" style="text-align: left;">
													<i class="fa fa-google-plus-square"></i> <label> Enable Google+ </label>
												</div>
												<div class="col-xs-4">
													<div class="make-switch pull-right"
														style="margin-top: 4px;">
														<input type="checkbox" data-on-color="success"
															data-size="small" onchange="show('google');"
															id="show-google">
													</div>
												</div>
											</div>
											<div class="row your_google">
												<div class="col-sm-5 app-title"><label>Choose Your App</label></div>
												<div class="col-sm-7">

													<div class="form-group">
														<div class="col-xs-12 text-right">
															<input type="hidden" id="app-google-hidden" name="app-google-hidden" value="0" /> 
															<select class="form-control" id="app-google" name="app-google">
																@if (count($gp_apps) > 0)
	                                                            <option value="">Select Your App</option>
																@foreach ( $gp_apps as $gplus )
																<option value={{$gplus->id}}>{{$gplus->app_name}}</option>
																@endforeach
																@else
																<option value="">-- No apps found --</option>
																@endif
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-8 social-icon" style="text-align: left;">
													<i class="fa fa-linkedin-square"></i> <label> Enable LinkedIn</label>
												</div>
												<div class="col-xs-4">
													<div class="make-switch pull-right"
														style="margin-top: 4px;">
														<input type="checkbox" data-on-color="success"
															data-size="small" onchange="show('linked');"
															id="show-linked">
													</div>
												</div>
											</div>
											<div class="row your_linked">
												<div class="col-sm-5 app-title"><label>Choose Your App</label></div>

												<div class="col-sm-7">

													<div class="form-group">
														<div class="col-xs-12 text-right">
															<input type="hidden" id="app-linkedin-hidden" name="app-linkedin-hidden" value="0" />
															<select class="form-control" id="app-linkedin" name="app-linkedin">
	                                                            @if (count($li_apps) > 0)
	                                                            <option value="">Select Your App</option>
																@foreach ($li_apps as $linkedin )
																<option value="{{$linkedin->id}}">{{$linkedin->app_name}}</option>
																@endforeach
																@else
																<option value="">-- No apps found --</option>
																@endif
                                                            </select>
														</div>
													</div>
												</div>
											</div>
											
											
											<div class="row">
												<div class="col-xs-8 social-icon" style="text-align: left;">
													<i class="fa fa-instagram"></i> <label> Enable Instagram </label>
												</div>
												<div class="col-xs-4 text-right">
													<input type="checkbox" class="boots-switch" data-on-color="success" data-size="small" id="chk_instagram">
												</div>
											</div>
											<div id="instagram_option" class="row hide">
												<div class="col-sm-5 app-title"><label>Choose Your App</label></div>
												<div class="col-sm-7">
													<div class="form-group">
														<div class="col-xs-12 text-right">
															<input type="hidden" id="app-instagram-hidden" name="app-instagram-hidden" value="0" /> 
															<select class="form-control" id="app-instagram" name="app-instagram">
																@if (count($ig_apps) > 0)
	                                                            <option value="">Select Your App</option>
																	@foreach ($ig_apps as $igapps)
																	<option value="{{$igapps->id}}">{{$igapps->app_name}}</option>																
																	@endforeach
																@else
																<option value="">-- No apps found --</option>
																@endif
															</select>
														</div>
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-xs-8 social-icon" style="text-align: left;">
													<i class="fa fa-vk"></i> <label> Enable Vkontakte </label>
												</div>
												<div class="col-xs-4 text-right">
													<input type="checkbox" class="boots-switch" data-on-color="success" data-size="small" id="chk_vkontakte">
												</div>
											</div>
											<div id="vkontakte_option" class="row hide">
												<div class="col-sm-5 app-title"><label>Choose Your App</label></div>
												<div class="col-sm-7">
													<div class="form-group">
														<div class="col-xs-12 text-right">
															<input type="hidden" id="app-vkontakte-hidden" name="app-vkontakte-hidden" value="0" /> 
															<select class="form-control" id="app-vkontakte" name="app-vkontakte">
																@if (count($ig_apps) > 0)
	                                                            <option value="">Select Your App</option>
																@foreach ( $vk_apps as $vkapps )
																<option value="{{$vkapps->id}}">{{$vkapps->app_name}}</option>
																@endforeach
																@else
																<option value="">-- No apps found --</option>
																@endif
															</select>
														</div>
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-xs-8 social-icon">
													<i class="fa fa-envelope-square"></i> <label>Enable Email Login</label>
												</div>
												<div class="col-xs-4">
													<div class="make-switch pull-right"
														style="margin-top: 4px;">
														<input type="checkbox" data-on-color="success"
															name='email_login' data-size="small"
															onchange="show('email');" id="show-email">
													</div>
												</div>
											</div>
											<div class="row form-group your_email">
												<div class="col-xs-3 col-sm-5"><label>Form input Fields</label></div>
												<div class="col-xs-6 col-sm-7">
													<select name="selectmultiple[]" id="selectmultiple" class="select2 form-control" multiple style="visibility: visible; display: block;">
														<option value="Name">Name</option>
														<option value="Email">Email</option>
														<option value="Country">Country</option>
														<option value="City">City</option>
														<option value="Phone Number">Phone Number</option>
														<option value="Gender">Gender</option>
														<option value="Year Born">Year Born</option>
													</select>
												</div>
											</div>
										</div>
                                        
										<div class="col-md-5">
											<div class="backgroundiphone-big">
												<div class="backgroundiphone-wrapper">
													<div class="content-html scrollable" id="content-html1">
													<div class="second_layer">
														<div class="row ddl_language_option conect-step hide">
															<div class="col-md-12">
																<select class="form-control">
																	<option>-- Select Language --</option>
																</select>
															</div>
														</div>
														<div class="row conect-step">
															<div class="col-xs-4 text-left"
																style="padding-right: 0px">
																<div class='dynamic-switch switch-box'>
																	<input class="terms_switch boots-switch"
																		data-size="mini" data-on-color="success"
																		data-on-text='On' data-off-text='Off' type="checkbox">
																</div>
															</div>
															<div class="col-xs-8 text-right">
																<a class="terms_text"> Tap To Agree To Term </a>
															</div>
														</div>
														<div class="row conect-step">
															<div class="col-xs-12 conect-step-col">
																<div id="conect-facebook-step1">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-facebook"> <i
																			class="fa fa-facebook"></i> <span
																			class="media_btn_facebook">Connect with Facebook</span>
																		</a>
																	</div>
																</div>

																<div id="conect-twiter-step1">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-twitter"> <i
																			class="fa fa-twitter"></i> <span
																			class="media_btn_twitter">Connect with Twitter</span>
																		</a>
																	</div>

																</div>

																<div id="conect-google-step1">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-google-plus"> <i
																			class="fa fa-google-plus"></i> <span
																			class="media_btn_google">Connect with Google</span>
																		</a>
																	</div>
																</div>

																<div id="conect-linked-step1">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-linkedin"> <i
																			class="fa fa-linkedin"></i> <span
																			class="media_btn_linkedin">Connect with LinkedIn</span>
																		</a>
																	</div>
																</div>
																
																<div id="conect-instagram-step1" style="display:none;">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-instagram">
																			<i class="fa fa-instagram" style="color: #fff"></i> <span class="media_btn_instagram">Connect with Instagram</span>
																		</a>
																	</div>
																</div>
																
																<div id="conect-vkontakte-step1" style="display:none;">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-vk">
																			<i class="fa fa-vk" style="color: #fff"></i> <span class="media_btn_vkontakte">Connect with Vkontakte</span>
																		</a>
																	</div>
																</div>

																<div id="conect-email-step1">
																	<div class="conect-social-icon">
																		<a class="btn btn-block btn-social btn-openid"> <i
																			class="fa fa-envelope-o"></i> <span
																			class="media_btn_email">Connect with Email</span>
																		</a>
																	</div>
																</div>
															</div>
															<!------/.col-12---->
														</div>
														<!------/.conect-step ---->
														</div>
													</div>
												</div>
												<!------/.backgroundiphone-wrapper ---->
											</div>
											<!------/.backgroundiphone ---->
										</div>
									</div><!------/.row ---->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <ul class="pager wizard">
                                                <input type="hidden" id="steptab" />
                                                <li class="previous"><a href="#" id="left-open" class="btn-lg"><i class="entypo-left-open"></i> Previous</a></li>
                                                <li class="next"><a href="#" class="btn-lg btn-next" id="right-open">Next
                                                        <i class="entypo-right-open"></i>
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div><!------/.row ---->
                                    </div>
								<!------/.tab-pane tab1---->

								<div id="tab2" class="tab-pane">
									<div class="row">
										<div class="col-md-7">										
											<div class="panel-group" id="accordion-test">		
												<div class="panel panel-gradient">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#accordion-test" href="#pageSettings">
																Page Settings
															</a>
														</h4>
													</div>
													<div id="pageSettings" class="panel-collapse collapse in">
														<div class="panel-body">
															<div class="form-group">
																<label>Default Language</label>
																<select class="form-control" onchange="javascript:GetLanguageDetails(this.value);" id="language" name="language">
																 @foreach ( $language_list as $language )
																<option value="{{$language->lang_code}}">{{$language->lang}}</option>
																@endforeach
	                                                            </select>
															</div>
															<div class="form-inline">
																<div class="form-group">
															    	<label>Multi-Language</label>
															    	<input type="checkbox" name="chk_language_option" class="boots-switch" data-on-color="success" data-size="mini" onchange="show('languageoption');" id="chk_language_option">
														    	</div>
														  	</div>
															<div class="form-group">
																<label>Text Color</label>
																<div class="input-group">
																	<input type="text" id="textcolor" name="textcolor" class="form-control colorpicker" data-format="hex" />
																	<div class="input-group-addon"><i class="color-preview"></i></div>
																</div>
															</div>
															<div class="form-group">
																<label>Background Color</label>
																<div class="input-group">
																	<input type="text" id="backgroundcolor" class="form-control colorpicker" data-format="hex" />
																	<div class="input-group-addon"><i class="color-preview"></i></div>
																</div>
																<input type="hidden" id="backgroundcolor-hidden" name="backgroundcolor" />
															</div>
															<div class="form-group">
																<label>Background Image</label>
																<div class="row">
																<div id="imageupload" class="col-md-10">
																	<input type="file" id="background-image" class="form-control" onchange="javascript:fileUploading();" />
																	<div class="status"></div>
																	<input type="hidden" id="backgroundimage-hidden" name="backgroundimage" />
																</div>
																<div class="col-md-2 ">
																	<button class="btn btn-danger btn-sm" type="button" onclick="javascript:deleteCampaignBGImage();"><i class="fa fa-trash-o"></i></button>
																</div>
																</div>
															</div>
															
														  	<br>
														  	<div class="form-inline">
														  		<div class="form-group">
															    	<label>Enable Background Layer</label>
															    	<input type="checkbox" name="chk_layer" class="boots-switch" data-on-color="success" data-size="mini" id="chk_layer">
														  		</div>
														  	</div>
														  	<div id="layer_options" class="hide">
														  		<div class="form-group">
																	<label>Layer Color</label>
																	<div class="input-group">
																		<input type="text" id="layer_color" name="layer_color" class="form-control colorpicker" data-format="rgb" value="rgb(255,255,255)" />
																		<div class="input-group-addon"><i class="color-preview"></i></div>
																	</div>
																</div>
																<div class="form-group">
																	<label>Opacity</label>
																	<div id="opacity_slider" class="slider slider-green" data-min="1" data-max="10" data-value="1" data-fill="#opacity_slider_value"></div>
																	<input type="hidden" id="opacity_slider_value" name="opacity_slider_value" value="0"/>
																</div>
																<div class="form-group">
																	<label>Round Corners</label>
																	<div id="radius_slider" class="slider slider-green" data-min="0" data-max="30" data-value="0" data-fill="#radius_slider_value"></div>
																	<input type="hidden" id="radius_slider_value" name="radius_slider_value" value="0"/>
																</div>
													  		</div>
														</div>
													</div>
												</div>
												
												<div class="panel panel-gradient">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#accordion-test" href="#header">
																Header
															</a>
														</h4>
													</div>
													<div id="header" class="panel-collapse collapse">
														<div class="panel-body">
															<div class="form-group">
																<label>Header</label>
																<textarea class="form-control richeditor" id="editor1" name="editor1"></textarea>
															</div>
														</div>
													</div>
												</div>
												
												<div class="panel panel-gradient">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#accordion-test" href="#footer">
																Footer
															</a>
														</h4>
													</div>
													<div id="footer" class="panel-collapse collapse">
														<div class="panel-body">
															<div class="form-group">
																<label>Footer</label>
																<textarea class="form-control richeditor" name="editor2" id="editor2"></textarea>
															</div>
														</div>
													</div>
												</div>
												
												<div class="panel panel-gradient">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#accordion-test" href="#analyticsRetargetingScript">
																Analytics &amp; Retargeting Script
															</a>
														</h4>
													</div>
													<div id="analyticsRetargetingScript" class="panel-collapse collapse">
														<div class="panel-body">
															<div class="form-group">
																<label>Header Script (<i>inserted before &lt;/head&gt;</i>)</label>
																<textarea rows="5" id="analytics_header_script" name="analytics_header_script" class="form-control"></textarea>
															</div>
															<div class="form-group">
																<label>Footer Script (<i>inserted before &lt;/body&gt;</i>)</label>
																<textarea rows="5" id="analytics_footer_script" name="analytics_footer_script" class="form-control"></textarea>
															</div>
														</div>
													</div>
												</div>
											</div>
											
											<div id="panel_standard_terms_privacy" class="panel panel-gradient" data-collapsed="1">
												<div class="panel-heading">
													<div class="panel-title">Custom Terms &amp; Conditions</div>													
													<div class="panel-options">
														<a><input type="checkbox" class="boots-switch" data-on-color="success" data-size="small" name="chk_standard_terms_privacy" id="chk_standard_terms_privacy"></a>
													</div>
												</div>												
												<!-- panel body -->
												<div class="panel-body">
													<textarea class="form-control richeditor" name="txarea_standard_terms_privacy" id="txarea_standard_terms_privacy">{{$standard_terms_privacy}}</textarea>
												</div>												
											</div>
											<!------/.row ---->
										</div>
										<div class="col-md-5">
											<div class="backgroundiphone-big">
												<div class="backgroundiphone-wrapper">

													<div class="content-html scrollable">
													<div class="second_layer">
														<div class="row ddl_language_option conect-step hide">
															<div class="col-md-12">
																<select class="form-control">
																	<option>-- Select Language --</option>
																</select>
															</div>
														</div>
														<div id="content-html2">
															<div id="content-header" style="padding: 0 15px;"></div>
															<div class="row conect-step" style="">
																<div class="col-xs-4" style="padding-right: 0px">
																	<div class="text-center dynamic-switch switch-box">
																		<input class="terms_switch boots-switch"
																			data-size="mini" data-on-color="success"
																			data-on-text='On' data-off-text='Off' type="checkbox">
																	</div>
																</div>
																<div class="col-xs-8 text-right">
																	<a class="terms_text"> Tap To Agree To Term </a>
																</div>
															</div>


															<input type="hidden" id="content-tapstep2-hidden"
																value="1" /> <input type="hidden"
																id="content-tapstep2-hidden2" value="1" />

															<div id="content-step21">
																<div class="conect-step row">
																	<div class="col-md-12">
																		<div id="conect-facebook-step2">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-facebook"> <i
																					class="fa fa-facebook"></i> <span
																					class="media_btn_facebook">Connect with Facebook</span>
																				</a>
																			</div>
																		</div>

																		<div id="conect-twiter-step2">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-twitter"> <i
																					class="fa fa-twitter"></i> <span
																					class="media_btn_twitter">Connect with Twitter</span>
																				</a>
																			</div>

																		</div>

																		<div id="conect-google-step2">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-google-plus">
																					<i class="fa fa-google-plus"></i> <span
																					class="media_btn_google">Connect with Google</span>
																				</a>
																			</div>
																		</div>

																		<div id="conect-linked-step2">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-linkedin"> <i
																					class="fa fa-linkedin"></i> <span
																					class="media_btn_linkedin">Connect with LinkedIn</span>
																				</a>
																			</div>
																		</div>
																		
																		<div id="conect-instagram-step2" style="display:none;">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-instagram">
																					<i class="fa fa-instagram" style="color: #fff"></i> <span
																					class="media_btn_instagram">Connect with Instagram</span>
																				</a>
																			</div>
																		</div>
																		
																		<div id="conect-vkontakte-step2" style="display:none;">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-vk">
																					<i class="fa fa-vk" style="color: #fff"></i> <span
																					class="media_btn_vkontakte">Connect with Vkontakte</span>
																				</a>
																			</div>
																		</div>

																		<div id="conect-email-step2">
																			<div class="conect-social-icon">
																				<a class="btn btn-block btn-social btn-openid"> <i
																					class="fa fa-envelope-o"></i> <span
																					class="media_btn_email">Connect with Email</span>
																				</a>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<!------/.content-step21 ---->

															<div id="content-step22"></div>
														</div>

														<div id="content-footer" style="padding: 0 15px;"></div>
														</div>
													</div>

												</div>
												<!------/.backgroundiphone-wrapper ---->
											</div>
											<!------/.backgroundiphone ---->
										</div>
									</div><!------/.row ---->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <ul class="pager wizard">
                                                <input type="hidden" id="steptab" />
                                                <li class="previous"><a href="#" id="left-open" class="btn-lg"><i
                                                        class="entypo-left-open"></i> Previous</a></li>
                                                <li class="next"><a href="#" class="btn-next btn-lg" id="right-open">Next
                                                        <i class="entypo-right-open"></i>
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
								</div>
								<!------/.tab-2 ++++++++++++++++++ ---->
								
								<div id="tab3" class="tab-pane">
									<div class="row">
										<div class="col-md-7">
											<div class="row">
												<div class="col-md-7"><label>Successful Login Redirect URL</label></div>
												<div class="col-md-5">
													<input type="text" class="form-control" id="success_login_url" name="success_login_url" placeholder="http:/www.facebook.com/yourpage">
												</div>
											</div>
											<div class="row">
												<div class="col-md-12" style="padding-bottom: 12px;"><label>Or</label></div>
											</div>
											<div class="row">
												<div class="col-md-7">
													<label>Custom Thank You Page</label>
												</div>
												<div class="col-md-5">
													<div class="make-switch">
														<input type="checkbox" data-on-color="success" data-size="small" onchange="show('editor4');" id="check-editor4" name="check-editor4">
													</div>
												</div>
											</div>
											<div class="row" style="padding-top: 10px;">
												<div class="col-sm-12" id="editor4-form">
													<textarea class="form-control richeditor" name="editor4" id="editor4"> </textarea>
													<br>
													<label>Redirection Time of Thank You Page</label>
													<div class="input-group">
														<input type="text" class="form-control" name="redirection_time_of_thankyou_page" id="redirection_time_of_thankyou_page" data-validate="number"/>
														<span class="input-group-addon">in seconds</span>
													</div>
													<br>
													<label>Conversion Tracking Code</label>
													<textarea class="form-control" rows="5" name="txarea_conersion_tracking_code" id="txarea_conersion_tracking_code"> </textarea>
												</div>
											</div>
											<br>
											<div class="row">
												<div class="col-md-7">
													<label>Enable Autoresponder</label>
												</div>
												<div class="col-xs-4">
													<input type="checkbox" name="chk_autoresponder" class="boots-switch" data-on-color="success" data-size="small" onchange="show('autoresponder');" id="chk_autoresponder">
												</div>
											</div>
											<div class="row form-group autoresponder_option hide" style="padding-top: 10px;">
												<div class="col-sm-5"><label>Autoresponder</label></div>
												<div class="col-sm-7">
													<select name="autoresponder" id="autoresponder" onchange="javascript:GetAutoresponderList(this.value, '');" class="form-control">
														<option value="">-- Select One --</option>
														<option value="getresponse">GetResponse</option>
														<option value="icontact">iContact</option>
														<option value="mailchimp">MailChimp</option>
														<option value="activecampaign">ActiveCampaign</option>
														<option value="sendreach">SendReach</option>
													</select>
												</div>
											</div>
											<div class="autoresponder_option row hide"
												id="autoresponder_list">
												<div class='col-xs-3 col-sm-5 text-left'><label>List/Campaign</label></div>
												<div class='col-xs-6 col-sm-7'>
													<select name='autoresponder_list' id='ddl_autoresponder_list' class='form-control'></select>
												</div>
											</div>
											<br>
											<div class="row">
												<div class="col-md-7">
													<label>Enable Email Reporting</label>
												</div>
												<div class="col-xs-4">
													<input type="checkbox" name="chk_emailreporting"
														class="boots-switch" data-on-color="success"
														data-size="small" onchange="show('emailreporting');"
														id="chk_emailreporting">
												</div>
											</div>
											<div id="emailreporting_option" class="row hide">
												<div class="col-sm-12">
													<div class="form-group">
														<label class="form-label">Email</label>
														<input type="text" class="form-control" id="emailreporting_email" name="emailreporting_email" data-role="tagsinput">
													</div>
													<div class="form-group">
														<label class="form-label">Frequency of Reports</label>
														<div class="row">
															<div class="col-md-4">
																<input id="rf_daily" class="icheck" type="radio" name="frequency_of_report" value="daily">
																<label for="minimal-radio-1">Daily</label>
															</div>
															<div class="col-md-4">
																<input id="rf_weekly" class="icheck" type="radio" name="frequency_of_report" value="weekly">
																<label for="minimal-radio-1">Weekly</label>
															</div>
															<div class="col-md-4">
																<input id="rf_monthly" class="icheck" type="radio" name="frequency_of_report" value="monthly">
																<label for="minimal-radio-1">Monthly</label>
															</div>
														</div>
													</div>
												</div>
											</div>
											<br>
											<div class="row">
												<div class="col-md-7">
													<label>Enable TripAdvisor Widget</label>
												</div>
												<div class="col-xs-4">
													<input type="checkbox" name="chk_tripadvisor" class="boots-switch" data-on-color="success"
														data-size="small" id="chk_tripadvisor">
												</div>
											</div>
											<div id="tripadvisor_option" class="row hide" style="padding-top: 10px;">
												<div class="col-sm-12">
													<div class="form-group">
														<textarea class="form-control" id="tripadvisor" name="tripadvisor" rows="15"></textarea>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="backgroundiphone-big">
												<div class="backgroundiphone-wrapper">
													<div class="content-html scrollable" id="content-html3"></div>
												</div>
												<!------/.backgroundiphone-wrapper ---->
											</div>
											<!------/.backgroundiphone ---->
										</div>
									</div>
									<!------/.row ---->
                                    <div class="row">
									<div class="col-sm-12">
										<ul class="pager wizard">
											<input type="hidden" id="steptab" />
											<li class="previous"><a href="#" id="left-open" class="btn-lg"><i
													class="entypo-left-open"></i> Previous</a></li>
											<li class="next"><a href="#" class="btn-next btn-lg" id="right-open">Next
													<i class="entypo-right-open"></i>
											</a></li>
										</ul>
									</div>
                                    </div>
								</div>
								<!------/.tab-pane3 +++++++++++++++++++++++++++---->

								<div id="tab4" class="tab-pane">
									<div class="row">
										<div class="col-md-7">
											<div class="col-sm-12">
												<div class="row" style="margin-bottom: 10px;">
													<div class="col-sm-8 col-md-9">
														<label>Auto-Email Users When connecting (All Social Networks)</label>
													</div>
													<div class="col-sm-4 col-md-3">
														<div class="make-switch">
															<input data-size="small" data-on-color="success"
																class="boots-switch" type="checkbox"
																onchange="show('editor5');" id="check-editor5"
																name="check-editor5">
														</div>
													</div>
												</div>
												<div class="row" id="row-editor5">
													<div class="col-sm-6">
														<div class="form-group"
															style="margin-left: 0px; margin-right: 0px;">
															<label class="form-label">Sender Name</label> <input
																type="text" class="form-control" id="sender_name"
																name="sender_name" placeholder="Sender Name">
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group"
															style="margin-left: 0px; margin-right: 0px;">
															<label class="form-label">Sender Email</label> <input
																type="email" class="form-control" id="sender_email"
																name="sender_email" placeholder="Sender Email">
														</div>
													</div>
												</div>
												<div class="row" id="row-editor5">
													<div class="col-sm-12">
														<div class="form-group"
															style="margin-left: 0px; margin-right: 0px;">
															<label class="form-label">Subject</label> <input
																type="text" class="form-control" id="subject"
																name="subject" placeholder="Subject">
														</div>
													</div>
												</div>
												<div class="row" id="editor5-form">
													<div class="col-md-12">
														<label class="form-label">Message</label>
														<textarea class="form-control richeditor" name="editor5" id="editor5"> </textarea>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<h3>After Successful Login</h3>
											</div>

											<div class="col-md-12">
												<div class="row" style="margin-bottom: 10px;">													
													<div class="col-sm-8 col-md-9">
														<label>Enable Post Status on User's Wall (Facebook)</label>
													</div>
													<div class="col-sm-4 col-md-3">
														<div class="make-switch">
															<input type="checkbox" data-on-color="success"
																data-size="small" onchange="show('editor6');"
																id="check-editor6" name="check-editor6">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12" id="editor6-form">
														<div class="form-group">
															<label class="form-label">Name</label> <input type="text"
																class="form-control" id="post_name" name="post_name"
																placeholder="Name">
														</div>
														<div class="form-group">
															<label class="form-label">Link</label> <input type="text"
																class="form-control" id="post_link" name="post_link"
																placeholder="Link">
														</div>
														<div class="form-group">
															<label class="form-label">Caption</label> <input
																type="text" class="form-control" id="post_caption"
																name="post_caption" placeholder="Caption">
														</div>
														<div class="form-group">
															<label class="form-label">Description</label>
															<textarea class="form-control" name="post_description"
																id="post_description" maxlength="500" rows="10"></textarea>
															<label><b>Character Limit <span id="current_char_length">0</span>/500
															</b></label>
														</div>
													</div>
												</div>
												<br />
											</div>
											<div class="col-md-12">
												<div class="row" style="margin-bottom: 10px;">
													<div class="col-md-9">
														<label>Enable Facebook Page Like Box</label>
													</div>
													<div class="col-md-3">
														<input type="checkbox" class="boots-switch"
															data-on-color="success" data-size="small"
															id="chk_facebook_like" name="chk_facebook_like">
													</div>
												</div>
												<div class="row">
												<div class="col-md-12 hide" id="div_facebook_like_option">
													<div class="form-group">
														<label class="form-label">Facebook Page</label>
														<div class="input-group">
															<div class="input-group-addon">http://facebook.com/</div>
															<input type="text" class="form-control"
																id="facebook_page" name="facebook_page"
																onblur="javascript:show_facebook_like_box(this.value);"
																placeholder="Your Facebook Page"> <span
																class="input-group-btn"><button class="btn btn-default" title="Check Facebook Page" type="button">
																	<i class="fa fa-refresh"></i>
																</button></span>
														</div>
														<span id="error_msg"></span>
													</div>
												</div>
												</div>
											</div>
										</div>
										<div class="col-md-5">
											<div class="backgroundiphone-big">
												<div class="backgroundiphone-wrapper">
													<div class="content-html scrollable">
														<div id="content-html4" style="margin: 10px;">
															<div id="fb_share_button"></div>
															<div id="facebook_like_box">
															</div>
														</div>
														<div id="content-footer4" style="padding: 0 15px;"></div>
													</div>
												</div>
												<!------/.backgroundiphone-wrapper ---->
											</div>
											<!------/.backgroundiphone ---->
										</div>
									</div><!------/.row ---->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <ul class="pager wizard">
                                                <input type="hidden" id="steptab" />
                                                <li class="previous"><a href="#" id="left-open" class="btn-lg"><i
                                                        class="entypo-left-open"></i> Previous</a></li>
                                                <li class="finish"><button type="submit" id="save_campaign"
                                                        name="save_campaign"
                                                        class="btn btn-info btn-lg pull-right">
                                                        <i class="fa fa-save"></i> Save</button></li>
                                            </ul>
                                        </div>
                                    </div>
								</div>
								<!------/.tab-pane4 ---->
							</div>
							<!------/.tab-content ---->
						</div>
						<!------/.col-7 ---->
						<div class="clearfix"></div>
					</form>
				</div>
				<!------/.row---->
			</div>
			<!-- modal-body -->
		</div>
		<!-- modal-content -->
	</div>
</div>
<!-- add_new_campaign -->

<script type="text/javascript">
    $(document).ready(function () {
        $(".boots-switch").bootstrapSwitch();
        
        $('#chk_facebook_like').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $("#div_facebook_like_option").removeClass('hide');
                $("#facebook_page").attr('required', '')
            } else {
                $("#div_facebook_like_option").addClass('hide');
                $("#facebook_page").val('');
                $("#content-html4 #facebook_like_box").html('');
                $("#facebook_page").removeAttr('required');
            }
        });
        $('#chk_conversion_tracking').on('switchChange.bootstrapSwitch', function (event, state) {
        	$("#div_conversion_tracking").removeClass('hide');
            if (state) {
                $("#div_conversion_tracking").removeClass('hide');
                $("#txarea_conersion_tracking_code").attr('required', '');
            } else {
            	$("#div_conversion_tracking").addClass('hide');
                $("#txarea_conersion_tracking_code").removeAttr('required');
            }
        });
        $('#chk_standard_terms_privacy').on('switchChange.bootstrapSwitch', function (event, state) {
        	$("#panel_standard_terms_privacy").removeAttr('data-collapsed');
            if (state) {
                $("#panel_standard_terms_privacy").attr('data-collapsed', '0');
            } else {
            	$("#panel_standard_terms_privacy").attr('data-collapsed', '1');
            }
        });
        $('#chk_instagram').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $("#instagram_option").removeClass('hide');
                $('#conect-instagram-step2').show();
                $('#conect-instagram-step1').show();
                $('#app-instagram-hidden').attr('value', 1);
                $("#app-instagram").attr('required', '');
            } else {
            	$("#instagram_option").addClass('hide');
            	$('#conect-instagram-step2').hide();
                $('#conect-instagram-step1').hide();
                $('#app-instagram-hidden').attr('value', 0);
                $("#app-instagram").removeAttr('required');
            }
        });
        $('#chk_vkontakte').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $("#vkontakte_option").removeClass('hide');
                $('#conect-vkontakte-step2').show();
                $('#conect-vkontakte-step1').show();
                $('#app-vkontakte-hidden').attr('value', 1);
                $("#app-vkontakte").attr('required', '');
            } else {
            	$("#vkontakte_option").addClass('hide');
            	$('#conect-vkontakte-step2').hide();
                $('#conect-vkontakte-step1').hide();
                $('#app-vkontakte-hidden').attr('value', 0);
                $("#app-vkontakte").removeAttr('required');
            }
        });
        $('#chk_tripadvisor').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
            	$("#tripadvisor_option").removeClass('hide');
            }else{
            	$("#tripadvisor_option").addClass('hide');
            }
        });
        $('#chk_layer').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
            	$("#layer_options").removeClass('hide');
            	$("#layer_color").attr('required', '');
            	$(".second_layer").attr('style', 'background-color:rgba(255,255,255, 0.5); border-radius:0px;');
            	$("#layer_color").val('rgb(255,255,255)');
            	$("#opacity_slider").slider("value", 5);
            	$("#opacity_slider .ui-label").text(5);
            	$("#opacity_slider_value").val(5);
            	$("#radius_slider").slider("value", 0);
            	$("#radius_slider .ui-label").text(0);
            	$("#radius_slider_value").val(0);
            }else{
            	$("#layer_options").addClass('hide');
            	$("#layer_color").removeAttr('required');
            	$(".second_layer").removeAttr('style');
            }
        });
        
        $('#layer_color').on('changeColor', function () {
        	var opacity = $( "#opacity_slider" ).slider( "option", "value" )/10;
        	var radius = $( "#radius_slider" ).slider( "option", "value" );
        	var color = $("#layer_color").val();
            var color1 = color.replace("rgb(", "");
        	var color2 = color1.replace(")", "");
            $(".second_layer").attr('style', 'background-color:rgba('+color2+', '+opacity+'); border-radius:'+radius+'px;');
        });
        $( "#opacity_slider" ).on( "slidechange", function( event, ui ) {
            var opacity = ui.value/10;
            var color = $("#layer_color").val();
            var color1 = color.replace("rgb(", "");
        	var color2 = color1.replace(")", "");
        	var radius = $( "#radius_slider" ).slider( "option", "value" );
            $(".second_layer").attr('style', 'background-color:rgba('+color2+', '+opacity+'); border-radius:'+radius+'px;');
        } );
        $( "#radius_slider" ).on( "slidechange", function( event, ui ) {
        	var opacity = $( "#opacity_slider" ).slider( "option", "value" )/10;
        	var color = $("#layer_color").val();
            var color1 = color.replace("rgb(", "");
        	var color2 = color1.replace(")", "");
        	var radius = ui.value;
            $(".second_layer").attr('style', 'background-color:rgba('+color2+', '+opacity+'); border-radius:'+radius+'px;');
        } );
        
        
        $('#textcolor.colorpicker').on('input keydown keypress keyup change blur changeColor', function () {
            var textcolor = $('#textcolor').val();
            $('.content-html').css('color', textcolor);
            $('.terms_text').css('color', textcolor);
        });
        $('#backgroundcolor.colorpicker').on('input keydown keypress keyup change blur changeColor', function () {
            var textcolor = $('#backgroundcolor').val();
            $('.content-html').css('background-color', textcolor);
            $('#backgroundcolor-hidden').attr('value', textcolor);
            $('#backgroundimage-hidden').attr('value', '');
            $('.content-html').css('background-image', "");
        });
        
        var file_type = '';
        var btnUpload = $('#imageupload');
        var status = $('.status');
        new AjaxUpload(btnUpload, {
            action: '{{url("ajax/uploadcampaignbackgroundimage")}}',
            name: 'uploadfile',
            onSubmit: function (file, ext) {
                file_type = ext;
                status.html('Uploading....');
            },
            onComplete: function (file, output) {
            	status.html('');
                var obj = jQuery.parseJSON(output)
                if(obj.status === 'succeed'){                    
                    $('.content-html').css('background-image', "url('" + obj.filename + "')");
                    $('.content-html').css('background-color', '');
                    $('#backgroundimage-hidden').attr('value', obj.filename);
                    $('#backgroundcolor-hidden').attr('value', '');
                }else{
                    alert(obj.message);
                }
            }
        });
        
        
        $("#post_description").keyup(function () {
            $("#current_char_length").text($("#post_description").val().length);
        });
        $('div.your_twitter').addClass('hide');
        $('div.your_google').addClass('hide');
        $('div.your_linked').addClass('hide');
        $('#instagram_option').addClass('hide');
        $('div.your_email').addClass('hide');
        $("div.your_page").addClass("hide");
        $("div.your_app").addClass("hide");
        $("div#editor3-form").addClass("hide");
        $("div#editor5-form").addClass("hide");
        $("div#editor6-form").addClass("hide");
        $("div#row-editor6").addClass("hide");
        $("div#file-editor6").addClass("hide");
        $("div#row-editor5").addClass("hide");
		
		$("#editor1").on('editable.contentChanged', function (e, editor) {
			$('#content-header').html($(this).editable("getHTML"));
		});
		
		$("#editor2").on('editable.contentChanged', function (e, editor) {
			$('#content-footer').html($(this).editable("getHTML"));
		});
		
		$("#editor4").on('editable.contentChanged', function (e, editor) {
			$('#content-html3').html($(this).editable("getHTML"));
		});
        getAllCampaigns();
		$('#campaign_search_value').bind('keypress', function(e) {
			var code = e.keyCode || e.which;
			 if(code == 13) {
				 getAllCampaigns(this.value);
			 }
		});
		$("#campaigns").on( "click", ".pagination a", function (e){
	        e.preventDefault();
	        var page = $(this).attr("data-page"); //get page number from link
	        var search_value = $('#campaign_search_value').val();
	        if(page){
	        	getAllCampaigns(search_value, page)
	        }
		});
		(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
    });
    function deleteCampaignBGImage(){
        var file_name = $("#backgroundimage-hidden").val();
        if(file_name !== ""){
	        $.ajax({
	            url:'{{url("ajax/DeleteCBGImage")}}',
	            type:'POST',
	            data: {file_name:file_name},
	            success:function(output){
		            output = output.trim('\n');
	                if(output == 'succeed'){
	                    $("#backgroundimage-hidden").val('');
	                    $('.content-html').css('background-image', '');
	                }
	            }
	        });
        }else{
            alert('No background image exist.');
        }
    }
    function deleteCampaign(id){
    	$("#modal_title").html('Delete Confirmation');
        $("#modal_body").html('<i class="entypo-attention"></i> Are you sure you want to delete this campaign?');
        $("#modal_footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '<button type="submit" class="btn btn-red">Yes</button>' +
                '<input type="hidden" name="campaign_id" value="' + id + '"/>');
        $("#campaign_alert_modal").modal('show');
    }
    function getAllCampaigns(search_value, page){
		$("#preloader").removeClass('hide');
		var token = $("#token").val();
		$.ajax({
			url: '{{url("ajax/GetAllCampaigns")}}',
			type: 'POST',
			data: {search_value:search_value, page:page,_token:token},
			success:function(output){
				$("#preloader").addClass('hide');
             	$("#campaigns").html(output);
             	$('[data-toggle="tooltip"]').tooltip();
             	$(".boots-switch").bootstrapSwitch();
             	$(".scrollable").slimScroll();
             	
             	@if ($camp_id && $camp_id != "" && is_numeric($camp_id))
					{{$cid = $camp_id}}
					window.setTimeout(function(){editcampaign({{$cid}})},1500)
				@endif
             }
         });
    }
    function show_facebook_like_box(page_name) {
        if (page_name !== "") {
            var url = "{{url('api/GetFacebookLikeBox&fb_page_name=')}}";
            var iframe = "<iframe src='" + url + page_name + "' frameborder='0' style='height:250px;' allowTransparency='true'></iframe>";
            $("#content-html4 #facebook_like_box").html(iframe);
        } else {
            $("#content-html4 #facebook_like_box").html('');
        }
    }
    function GetLanguageDetails(lang_code) {
        $.ajax({
            url: '{{url("ajax/GetLanguageDetails")}}',
            type: 'POST',
            data: {lang_code: lang_code,_token:'{{csrf_token()}}'},
            complete: function (output) {
                var lang = jQuery.parseJSON(output.responseText);
                $(".media_btn_facebook").text(lang.facebook);
                $(".media_btn_twitter").text(lang.twitter);
                $(".media_btn_linkedin").text(lang.linkedin);
                $(".media_btn_google").text(lang.google);
				$(".media_btn_instagram").text(lang.instagram);
                $(".media_btn_email").text(lang.email);
                $(".terms_text").text(lang.agree);
                var data = lang.agree_btn_text;
                var arr = data.split('/');
                $(".dynamic-switch .bootstrap-switch-handle-on").text(arr[0]);
                $(".dynamic-switch .bootstrap-switch-handle-off").text(arr[1]);
            }
        });
    }
    function show(id) {
        if (id === 'editor3') {
            if (document.getElementById('check-editor3').checked) {
                $("div#editor3-form").removeClass("hide");
                $('#content-tapstep2-hidden').attr('value', 0);
            } else {
                $("div#editor3-form").addClass("hide");
                $('#content-tapstep2-hidden').attr('value', 1);
				$('#editor3-form').addClass('hide');
            }
        }
        if (id === 'editor4') {
            if (document.getElementById('check-editor4').checked) {
                $("div#editor4-form").removeClass("hide");
                $('#content-html3').html($('#editor4').editable("getHTML"));
            } else {
                $("div#editor4-form").addClass("hide");
                $('#content-html3').html('');
            }
        }
        if (id === 'editor5') {
            if (document.getElementById('check-editor5').checked) {
                $("#sender_name").attr('required','');
                $("#sender_email").attr('required','');
                $("#subject").attr('required','');
                $("#message").attr('required','');
                $("div#editor5-form").removeClass("hide");
                $("div#row-editor5").removeClass("hide");
            } else {
            	$("#sender_name").removeAttr('required');
                $("#sender_email").removeAttr('required');
                $("#subject").removeAttr('required');
                $("#message").removeAttr('required');
                $("div#editor5-form").addClass("hide");
                $("div#row-editor5").addClass("hide");
            }
        }
        if (id === 'editor6') {
            if (document.getElementById('check-editor6').checked) {
                $("div#editor6-form").removeClass("hide");
                $("div#row-editor6").removeClass("hide");
                $("div#file-editor6").removeClass("hide");
                $("#content-html4 #fb_share_button").html('<a href="javascript:void(0);" class="btn btn-lg btn-block" style="background-color: #5472C7; color:#FFFFFF;"><i class="entypo-share"></i> <b>Share on Facebook</b></a><br>');
                $("#post_name").attr('required', '');
                $("#post_description").attr('required', '');
                $("#post_caption").attr('required', '');
            } else {
                $("div#editor6-form").addClass("hide");
                $("div#row-editor6").addClass("hide");
                $("div#file-editor6").addClass("hide");
                $("#content-html4 #fb_share_button").html('');
                $("#post_name").removeAttr('required', '');
                $("#post_description").removeAttr('required', '');
                $("#post_caption").removeAttr('required', '');
            }
        }
        if (id === 'facebook_log') {
            if (document.getElementById('face_log').checked) {
                $('#app-facebook-hidden').attr('value', 1);
                $("div.your_app").removeClass("hide");
                $("#conect-facebook-step2").show();
                $('#conect-facebook-step1').show();
                $("#app-facebook").attr('required', '');
            } else {
                $("div.your_app").addClass("hide");
                $('#conect-facebook-step1').hide();
                $("#conect-facebook-step2").hide();
                $('#app-facebook-hidden').attr('value', 0);
                $("#app-facebook").removeAttr('required', '');
            }
        }
        if (id === 'twitter') {
            if (document.getElementById('show-twitter').checked) {
                $('div.your_twitter').removeClass('hide');
                $('#conect-twiter-step2').show();
                $('#conect-twiter-step1').show();
                $('#app-twitter-hidden').attr('value', 1);
                $("#app-twitter").attr('required', '');
            } else {
                $('div.your_twitter').addClass('hide');
                $('#conect-twiter-step1').hide();
                $('#conect-twiter-step2').hide();
                $('#app-twitter-hidden').attr('value', 0);
                $("#app-twitter").removeAttr('required', '');
            }
        }
        if (id === 'google') {
            if (document.getElementById('show-google').checked) {
                $('div.your_google').removeClass('hide');
                $('#conect-google-step2').show();
                $('#conect-google-step1').show();
                $('#app-google-hidden').attr('value', 1);
                $("#app-google").attr('required', '');
            } else {
                $('div.your_google').addClass('hide');
                $('#conect-google-step2').hide();
                $('#conect-google-step1').hide();
                $('#app-google-hidden').attr('value', 0);
                $("#app-google").removeAttr('required', '');
            }
        }
        if (id === 'linked') {
            if (document.getElementById('show-linked').checked) {
                $('div.your_linked').removeClass('hide');
                $('#conect-linked-step1').show();
                $('#conect-linked-step2').show();
                $('#app-linkedin-hidden').attr('value', 1);
                $("#app-linkedin").attr('required', '');
            } else {
                $('div.your_linked').addClass('hide');
                $('#conect-linked-step1').hide();
                $('#conect-linked-step2').hide();
                $('#app-linkedin-hidden').attr('value', 0);
                $("#app-linkedin").removeAttr('required', '');
            }
        }
        if (id === 'email') {
            if (document.getElementById('show-email').checked) {
                $('div.your_email').removeClass('hide');
                $('#conect-email-step1').show();
                $('#conect-email-step2').show();
                $("#selectmultiple").attr('required', '');
            } else {
                $('div.your_email').addClass('hide');
                $('#conect-email-step1').hide();
                $('#conect-email-step2').hide();
                $("#selectmultiple").removeAttr('required', '');
            }
        }
        if (id === 'autoresponder') {
            if (document.getElementById('chk_autoresponder').checked) {
                $(".autoresponder_option").removeClass('hide');
                $("#autoresponder").attr('required', '');
                $("#ddl_autoresponder_list").attr('required', '');
            } else {
                $(".autoresponder_option").addClass('hide');
                $("#autoresponder").removeAttr('required');
                $("#ddl_autoresponder_list").removeAttr('required');
            }
        }
        if(id === 'emailreporting'){
        	if (document.getElementById('chk_emailreporting').checked) {
                $("#emailreporting_option").removeClass('hide');
                $("#emailreporting_email").attr('required', '');
                $("#rf_daily").iCheck('check');
            } else {
                $("#emailreporting_option").addClass('hide');
                $('#emailreporting_email').tagsinput('add', '{{$platform_user_email}}');
                $("#emailreporting_email").removeAttr('required');
            }
        }
        if(id === 'languageoption'){
        	if (document.getElementById('chk_language_option').checked) {
        		$(".ddl_language_option").removeClass('hide');
            }else{
            	$(".ddl_language_option").addClass('hide');
            }
        }
    }
    function addnew() {
        $('#rootwizard').find('input:text, input:password, input:file, select, textarea').val('');
        $('#face_log').bootstrapSwitch('state', false, true);
        $('#app-facebook-hidden').attr('value', 0);
        $('#face_like').bootstrapSwitch('state', false, true);
        $('#app-facebook-like-hidden').attr('value', 0);
        $('#show-linked').bootstrapSwitch('state', false, true);
        $('#app-linkedin-hidden').attr('value', 0);
        $('#show-google').bootstrapSwitch('state', false, true);
        $('#app-google-hidden').attr('value', 0);
        $('#show-twitter').bootstrapSwitch('state', false, true);
        $('#app-twitter-hidden').attr('value', 0);
        $('#chk_instagram').bootstrapSwitch('state', false, true);
        $('#app-instagram-hidden').val(0);
        $('#instagram_option').addClass('hide');
        $('#conect-instagram-step1').hide();
        $('#conect-instagram-step2').hide();
        $('#chk_vkontakte').bootstrapSwitch('state', false, true);
        $('#app-vkontakte-hidden').val(0);
        $('#vkontakte_option').addClass('hide');
        $('#conect-vkontakte-step1').hide();
        $('#conect-vkontakte-step2').hide();
        $('#show-email').bootstrapSwitch('state', false, true);
        $('#check-editor3').bootstrapSwitch('state', false, true);
        $('#check-editor4').bootstrapSwitch('state', false, true);
        $('#check-editor5').bootstrapSwitch('state', false, true);
        $('#check-editor6').bootstrapSwitch('state', false, true);
        $('#chk_autoresponder').bootstrapSwitch('state', false, true);
        $('#chk_facebook_like').bootstrapSwitch('state', false, true);
		$('#div_facebook_like_option').addClass('hide');
        $("#facebook_page").removeAttr("required");        
        $('#id-campaign').val(0);
        $("#language").val('en');
        $("#language").trigger("change");
        $("#textcolor").colorpicker('setValue', '#FFFFFF');
        $('.content-html').css('color', '#FFFFFF');
        $('.terms_text').css('color', '#000000');
        $("#backgroundcolor").colorpicker('setValue', '#FFFFFF');
        $('.content-html').css('background-color', '#FFFFFF');
        $('#backgroundcolor-hidden').attr('value', '#FFFFFF');
        $('#backgroundimage-hidden').attr('value', '');
        $('.content-html').css('background-image', '');
        $('#chk_emailreporting').bootstrapSwitch('state', false, true);
        $('#emailreporting_option').addClass('hide');
        $('#chk_tripadvisor').bootstrapSwitch('state', false, true);
        $('#tripadvisor_option').addClass('hide');
        $("#selectmultiple").select2("data", '');
        $('#content-header').html('');
        $('#content-footer').html('');
        $('#facebook_like_box').html('');
        $('#chk_language_option').bootstrapSwitch('state', false, true);
		$(".ddl_language_option").addClass('hide');
        $("#redirection_time_of_thankyou_page").val(5);
        $('#add_new_campaign').modal('show');
        
    }
    function editcampaign(id) {
        var data = 'id=' + id+'&_token='+'{{csrf_token()}}';
        $('#id-campaign').val(id);
        $("#preloader").removeClass('hide');
        $.ajax({
            type: 'POST',
            cache: false,
            url: '{{url("ajax/RetrieveCampaignById")}}',
            data: data,
            success: function (data) {
            	$("#preloader").addClass('hide');
            	
                var getData = $.parseJSON(data);
                var directory = '{{url("uploads/campaign_background_images/")}}';
                $('#campaign-name').val(getData.name);
                $('#ssid-name').val(getData.ssid);
                $('#textcolor').attr('value', getData.textcolor);
                $('#backgroundcolor').attr('value', getData.backgroundcolor);
                $('#success_login_url').val(getData.successloginurl);
				$("#editor1").editable("setHTML", getData.headerhtml, true);
				$('#content-header').html(getData.headerhtml);
				$("#editor2").editable("setHTML", getData.footer, true);
                $('#content-footer').html(getData.footer);
                $('.backgroundiphone').css('color', getData.textcolor);
                $('.content-html').css('color', getData.textcolor);
                $('.terms_text').css('color', getData.textcolor);
                $('.content-html').css('background-color', getData.backgroundcolor);
                $('.content-html').css('background-image', "url('" + getData.backgroundimage + "')");
                $('#backgroundimage-hidden').attr('value', getData.backgroundimage);
                $('#backgroundcolor-hidden').attr('value', getData.backgroundcolor);
                if (getData.appfbid > 0) {
                    $('#face_log').bootstrapSwitch('state', true, true);
                    $('#app-facebook-hidden').attr('value', 1);
                    $('#app-facebook').val(getData.appfbid);
                } else {
                    $('#face_log').bootstrapSwitch('state', false, true);
                    $('#app-facebook-hidden').attr('value', 0);
                }
                if (getData.appflid > 0) {
                    $('#face_like').bootstrapSwitch('state', true, true);
                    $('#app-facebook-like-hidden').attr('value', 1);
                    $('#app-facebook-like').val(getData.appflid);
                } else {
                    $('#face_like').bootstrapSwitch('state', false, true);
                    $('#app-facebook-like-hidden').attr('value', 0);
                }
                if (getData.applid > 0) {
                    $('#show-linked').bootstrapSwitch('state', true, true);
                    $('#app-linkedin-hidden').attr('value', 1);
                    $('#app-linkedin').val(getData.applid);
                } else {
                    $('#show-linked').bootstrapSwitch('state', false, true);
                    $('#app-linkedin-hidden').attr('value', 0);
                }
                if (getData.appgid > 0) {
                    $('#show-google').bootstrapSwitch('state', true, true);
                    $('#app-google-hidden').attr('value', 1);
                    $('#app-google').val(getData.appgid);
                } else {
                    $('#show-google').bootstrapSwitch('state', false, true);
                    $('#app-google-hidden').attr('value', 0);
                }
                if (getData.appigid > 0) {
                    $('#chk_instagram').bootstrapSwitch('state', true, true);
                    $('#app-instagram-hidden').attr('value', 1);
                    $('#app-instagram').val(getData.appigid);
                    $('#instagram_option').removeClass('hide');
                    $('#conect-instagram-step1').show();
                    $('#conect-instagram-step2').show();
                } else {
                    $('#chk_instagram').bootstrapSwitch('state', false, true);
                    $('#app-instagram-hidden').attr('value', 0);
                    $('#instagram_option').addClass('hide');
                    $('#conect-instagram-step1').hide();
                    $('#conect-instagram-step2').hide();
                }
                if (getData.appvkid > 0) {
                    $('#chk_vkontakte').bootstrapSwitch('state', true, true);
                    $('#app-vkontakte-hidden').attr('value', 1);
                    $('#app-vkontakte').val(getData.appvkid);
                    $('#vkontakte_option').removeClass('hide');
                    $('#conect-vkontakte-step1').show();
                    $('#conect-vkontakte-step2').show();
                } else {
                    $('#chk_vkontakte').bootstrapSwitch('state', false, true);
                    $('#app-vkontakte-hidden').attr('value', 0);
                    $('#vkontakte_option').addClass('hide');
                    $('#conect-vkontakte-step1').hide();
                    $('#conect-vkontakte-step2').hide();
                }
                if (getData.apptid > 0) {
                    $('#show-twitter').bootstrapSwitch('state', true, true);
                    $('#app-twitter-hidden').attr('value', 1);
                    $('#app-twitter').val(getData.apptid);
                } else {
                    $('#show-twitter').bootstrapSwitch('state', false, true);
                    $('#app-twitter-hidden').attr('value', 0);
                }
                var splitemail = getData.fieldsemail.split(';');
                if ((getData.fieldsemail).length > 0) {
                    $('#show-email').bootstrapSwitch('state', true, true);
                    $('#selectmultiple').select2("val", splitemail);
                } else {
                    $('#show-email').bootstrapSwitch('state', false, true);
                }
                if (getData.checkthankyoupage === '1') {
                    $('#check-editor4').bootstrapSwitch('state', true, true);
					$("#editor4").editable("setHTML", getData.thankyoupage, true);
					$('#content-html3').html(getData.thankyoupage);
					$('#txarea_conersion_tracking_code').val(getData.conversion_tracking_code);
					$("#redirection_time_of_thankyou_page").val(getData.redirection_time/1000);
                } else {
                    $('#check-editor4').bootstrapSwitch('state', false, true);
                    $('#content-html3').html('');
                    $('#txarea_conersion_tracking_code').val('');
                    $("#redirection_time_of_thankyou_page").val(5);
                }
                if (getData.customterm === '') {
                    $('#chk_standard_terms_privacy').bootstrapSwitch('state', false, true);
                    $("#panel_standard_terms_privacy").attr('data-collapsed', '1');
                    $("#txarea_standard_terms_privacy").editable("setHTML", '', true);
                } else {
                    $('#chk_standard_terms_privacy').bootstrapSwitch('state', true, true);
                    $("#txarea_standard_terms_privacy").editable("setHTML", getData.customterm, true);
                    $("#panel_standard_terms_privacy").attr('data-collapsed', '0');
                }
                if (getData.autoemail === 'false') {
                    $('#check-editor5').bootstrapSwitch('state', false, true);
                    $("#sender_name").removeAttr('required');
                    $("#sender_email").removeAttr('required');
                    $("#subject").removeAttr('required');
                    $("#message").removeAttr('required');
                } else {
                	$("#sender_name").attr('required','');
                    $("#sender_email").attr('required','');
                    $("#subject").attr('required','');
                    $("#message").attr('required','');
                    $('#check-editor5').bootstrapSwitch('state', true, true);
                    $("#sender_name").val(getData.sender_name);
                    $("#sender_email").val(getData.sender_email);
                    $("#subject").val(getData.subject);
					$("#editor5").editable("setHTML", getData.message, true);
                }
                if (getData.autopost === 'true') {
                    $('#check-editor6').bootstrapSwitch('state', true, true);
                    $("#post_name").val(getData.post_name);
                    $("#post_link").val(getData.post_link);
                    $("#post_caption").val(getData.post_caption);
                    var post_description = getData.post_description;
                    $("#post_description").val(post_description);
                    $("#current_char_length").text(post_description.length);
                    $("#post_name").attr('required', '');
                    $("#post_description").attr('required', '');
                } else {
                    $('#check-editor6').bootstrapSwitch('state', false, true);
                    $("#post_name").removeAttr('required');
                    $("#post_description").removeAttr('required');
                }
                if (getData.facebook_like === 'true') {
                    $('#chk_facebook_like').bootstrapSwitch('state', true, true);
                    $('#div_facebook_like_option').removeClass('hide');
                    $("#facebook_page").val(getData.facebook_page);
                    $("#facebook_page").attr('required', '');
                    show_facebook_like_box(getData.facebook_page);
                } else {
                    $('#chk_facebook_like').bootstrapSwitch('state', false, true);
                    $('#div_facebook_like_option').addClass('hide');
                    $("#facebook_page").removeAttr('required');
                }
                
                if(getData.autoresponder === 'true'){
                    var autoresponder_api = getData.autoresponder_api;
                    var autoresponder_list = getData.autoresponder_list;
                    GetAutoresponderList(autoresponder_api, autoresponder_list);
                    $("#autoresponder").val(autoresponder_api);                    
                    $('#chk_autoresponder').bootstrapSwitch('state', true, true);
                    $(".autoresponder_option").removeClass('hide');
                }else{
                    $('#chk_autoresponder').bootstrapSwitch('state', false, true);
                    $(".autoresponder_option").addClass('hide');
                }
                if(getData.emailreporting === 'true'){
                    $('#chk_emailreporting').bootstrapSwitch('state', true, true);
                    $("#emailreporting_option").removeClass('hide');
                    $('#emailreporting_email').tagsinput('add', getData.emailreporting_email);
                    $("#rf_"+getData.frequency_of_report).iCheck('check');
                    $("#emailreporting_email").attr('required', '');
                }else{
                    $('#chk_emailreporting').bootstrapSwitch('state', false, true);
                    $("#emailreporting_option").addClass('hide');
                    $("#emailreporting_email").removeAttr('required');
                    $("#rf_daily").removeAttr('required');
                }
                if(getData.tripadvisor === 'true'){
                    $('#chk_tripadvisor').bootstrapSwitch('state', true, true);
                    $("#tripadvisor_option").removeClass('hide');
                    $("#tripadvisor").val(getData.tripadvisor_markup);
                }else{
                    $('#chk_tripadvisor').bootstrapSwitch('state', false, true);
                    $("#tripadvisor_option").addClass('hide');
                }
                if(getData.second_layer === 'true'){
                    $('#chk_layer').bootstrapSwitch('state', true, true);
                    $("#layer_options").removeClass('hide');
                    $("#layer_color").val('rgb('+getData.layer_rgb+')');
                    $("#opacity_slider").slider("value", getData.opacity * 10);
                    $("#opacity_slider .ui-label").text(getData.opacity * 10);
                    $("#opacity_slider_value").val(getData.opacity * 10);
                    $("#radius_slider").slider("value", getData.radius);
                    $("#radius_slider .ui-label").text(getData.radius);
                    $("#radius_slider_value").val(getData.radius);
                    $(".second_layer").attr('style', 'background-color:rgba('+getData.layer_rgb+', '+getData.opacity+'); border-radius:'+getData.radius+'px;');
                }else{
                	$('#chk_layer').bootstrapSwitch('state', false, true);
                    $("#layer_options").addClass('hide');
                    /*$("#layer_color").val('rgb('+getData.layer_rgb+')');
                    $("#opacity_slider").slider("value", getData.opacity * 10);
                    $("#opacity_slider .ui-label").text(getData.opacity * 10);
                    $("#opacity_slider_value").val(getData.opacity * 10);*/
                    $(".second_layer").removeAttr('style');
                }                
                if(getData.show_lang_option === 'true'){
                	$('#chk_language_option').bootstrapSwitch('state', true, true);
                    $(".ddl_language_option").removeClass('hide');
                }else{
                	$('#chk_language_option').bootstrapSwitch('state', false, true);
                	$(".ddl_language_option").addClass('hide');
                }
                
                $('#analytics_header_script').val(getData.analytics_header_script);
                $('#analytics_footer_script').val(getData.analytics_footer_script);
                
                $('#language').val(getData.lang_code);
                GetLanguageDetails(getData.lang_code);
                
                $('#add_new_campaign').modal('show');
            }
        });
    }
    function CheckFacebookPage() {
        var page = $("#facebook_page").val();
        $('#error_msg').text('');
        $('#error_msg').removeAttr('style');
        if (page) {
            $.ajax({
                url: '{{url("ajax/CheckFacebookPage")}}',
                type: 'POST',
                data: {page: page},
                success: function (output) {
                    if (output === "") {
                        $('#error_msg').text('Invalid Facebook Page');
                        $('#error_msg').attr("style", "color:red; font-style:italic;");
                    }
                }
            });
        }
    }
    function GetAutoresponderList(value, selected_list_id) {
        $("#preloader").removeClass('hide');
        $.ajax({
            url: '{{url("ajax/GetAutoresponderList")}}',
            type: 'POST',
            data: {value: value, selected_list_id:selected_list_id},
            success: function (output) {
                $("#preloader").addClass('hide');
                $("#ddl_autoresponder_list").html(output);
            }
        });
    }
    function cloneCampaign(id, flug){
        if(id !== '' && flug === 'true'){
	    	$("#preloader").removeClass('hide');
	        $.ajax({
	            url: '{{url("ajax/CloneCampaign")}}',
	            type: 'POST',
	            data: {id:id},
	            success:function(output){
	            	$("#preloader").addClass('hide');
	            	$("#btn_clone_campaign").removeAttr('onclick');
	            	$("#clone_campaign_alert_modal").modal('hide');
	            	if(output === 'true'){
	            		getAllCampaigns();
	                }
	            	$("body").scrollTop(0);
	            }
	        });
        }else{
        	$("#btn_clone_campaign").attr('onclick', "javascript:cloneCampaign("+id+", 'true')");
            $("#clone_campaign_alert_modal").modal('show');
        }
    }
</script>

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/dropzone/ajaxupload.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.nicescroll.min.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.bootstrap.wizard.min.js'}}"></script>

<!-- Include Editor style. -->

<link href="{{$assets_dir.'/js/froala_editor/css/froala_editor.min.css'}}" rel="stylesheet" type="text/css" />
<link href="{{$assets_dir.'/js/froala_editor/css/froala_style.min.css'}}" rel="stylesheet" type="text/css" />

<!-- Include JS files. -->
<script src="{{$assets_dir.'/js/froala_editor/js/froala_editor.min.js'}}"></script>
<script src="{{$assets_dir.'/js/froala_editor/js/plugins/media_manager.min.js'}}"></script>
<script src="{{$assets_dir.'/js/froala_editor/js/plugins/font_family.min.js'}}"></script>
<script src="{{$assets_dir.'/js/froala_editor/js/plugins/font_size.min.js'}}"></script>
<script src="{{$assets_dir.'/js/froala_editor/js/plugins/colors.min.js'}}"></script>
<script src="{{$assets_dir.'/js/froala_editor/js/plugins/tables.min.js'}}"></script>
<script>
  $.Editable.DEFAULTS.key = '8SXSJ1LHAFJVCXCLS==';
</script>

<!-- Include IE8 JS. -->
<!--[if lt IE 9]>
      <script src="{{$assets_dir.'/js/froala_editor/js/froala_editor_ie8.min.js'}}"></script>
  <![endif]-->

  <!-- Initialize the editor. -->
  <script>
      $(function() {
          $('.richeditor').editable({
			imageUploadURL: '{{url("ajax/editorsimageupload")}}',
			imagesLoadURL: '{{url("ajax/editorsmedia")}}',
			imageDeleteURL: '{{url("ajax/editorsimgdel")}}',
			buttons: ["bold", "italic", "underline", "fontFamily", "fontSize", "color", "align", "createLink", "insertImage", "table", "undo", "redo", "html"],
			inlineMode: false
		  }).on('editable.afterRemoveImage', function (e, editor, $img) {
			// Set the image source to the image delete params.
			editor.options.imageDeleteParams = {src: $img.attr('src')};
			// Make the delete request.
			editor.deleteImage($img);
		  }).on('editable.imageDeleteSuccess', function (e, editor, data) { 
			alert("Image deleted.");
		  })
		  // Catch image delete error.
		  .on('editable.imageDeleteError', function (e, editor, error) { 
			alert("Unable to delete this image now.");
		  });
	});
  
  </script>
  <script src="{{$assets_dir.'/js/fileinput.js'}}"></script>
  
  <!-- Bootstrap Tag Input Style -->
  <link href="{{$assets_dir.'/js/bootstrap_tag_input/bootstrap-tagsinput.css'}}" rel="stylesheet" type="text/css" />  
  
  <!-- Bootstrap Tag Input Script -->
  <script src="{{$assets_dir.'/js/bootstrap_tag_input/bootstrap-tagsinput.min.js'}}"></script>
@endsection