@extends('template.layout')
@section('content')
<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr />
        <div class="row">
            <div class="col-md-4"><h2 style="margin-top:0px;"><i class="entypo-tag"></i> Branding</h2></div>			
            <div class="col-md-8"></div>
        </div>
        <br>        
            {!! Session::get('SESSION_MESSAGE') !!}
            {{Session::forget('SESSION_MESSAGE')}}
                <form class="validate" action="{{url('options/savebranding')}}" method="POST" enctype="multipart/form-data">                    
                        	
					<div class = "row">
						<div class="col-md-6">
							<label for="site_logo">Logo</label>
							<input type="hidden" name="hdn_logo_name" id="hdn_logo_name" value="{{$logo}}"/>
							<div>
							<div class="fileinput fileinput-{{($options['logo'] == '')? 'new' : 'exists'}}" data-provides="fileinput">
								<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;" data-trigger="fileinput">
									<img src="{{$assets_dir.'/images/200x150.gif'}}" alt="...">
								</div>
								<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px">
									
									{!! $options['logo'] == "" ? "" : "<img src='" . $options['logo'] . "'/>"!!}
									
								</div>
								<div>
									<span class="btn btn-white btn-file">
										<span class="fileinput-new">Select image</span>
										<span class="fileinput-exists">Change</span>
										<input type="file" name="site_logo" accept="image/*" onchange="javascript:removelogoname('logo');">
									</span>
									<a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
								</div>
							</div>
							</div>
						</div>
						<div class="col-md-6">
							<label for="favicon">Favicon</label>
							<input type="hidden" name="hdn_favicon_name" id="hdn_favicon_name" value="{{$options['favicon']}}"/>
							<div>
							<div class="fileinput fileinput-{{$options['favicon'] == "" ? "new" : "exists"}}" data-provides="fileinput">
								<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;" data-trigger="fileinput">
									<img src="{{$assets_dir.'/images/16x16.gif'}}" alt="...">
								</div>
								<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px">
									{!! $options['favicon'] == "" ? "" : "<img src='".$options['favicon'] . "'/>" !!}								
								</div>
								<div>
									<span class="btn btn-white btn-file">
										<span class="fileinput-new">Select Icon</span>
										<span class="fileinput-exists">Change</span>
										<input type="file" name="favicon" accept="image/ico" onchange="javascript:removelogoname('favicon');">
									</span>
									<a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
								</div>
							</div>
							</div>
						</div>
					</div>
					@if(Session::get('USER_TYPE') != '1')					
					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="subdomain">Personalize your application's URL</label>
									<div class="input-group">
										<input type="text" class="form-control" value="{{$sub_domain}}" id="subdomain" name="subdomain" placeholder="Sub Domain">
										<div class="input-group-addon">.mywifi.io</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="custom_domain">Custom Domain</label>
									<div class="input-group">
										<div class="input-group-addon">https://</div>
										<input type="text" class="form-control" value="{{$custom_domain}}" id="custom_domain" name="custom_domain" maxlength='255' placeholder="example.com">
									</div>
								</div>
							</div>
						</div>
					@endif					
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="site_title">Site Title</label>
								<input type="text" class="form-control" name="site_title" value="{{$options['site_title']}}" placeholder="Site Title">
							</div>	
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="app_secret_key">App Secret Key</label>
								<div class="input-group">
									<input id="app_secret_key" name="app_secret_key" value="{{$options['app_secret_key']}}" type="text" class="form-control" placeholder="App Secret Key">
									<span class="input-group-btn">
										<button class="btn btn-warning" onclick="javascript:GenerateAppSecretKey();" type="button"><i class="fa fa-refresh"></i> {{$options['app_secret_key'] != "" ? "Regenerate" : "Generate"}}</button>
									</span>
								</div>
							</div>	
						</div>						
					</div>
					
					@if(Session::get('USER_TYPE') == '1')
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="logoff_time">Logoff Time</label>
								<div class="input-group">								
									<input id="logoff_time" name="logoff_time" value="{{($options['logoff_time'] == '' || $options['logoff_time'] == 0?10 : $options['logoff_time']) / 60}}" type="text" class="form-control">
									<span class="input-group-addon">
										in minute
									</span>
								</div>
							</div>	
						</div>
					</div>
					@endif
					
					<div class="row">
						<div class="col-md-6">
							<label for="menu_background_color">Menu Background Color</label>
							<div class="input-group">
								<input type="text" class="form-control colorpicker" data-format="hex" name="menu_background_color" value="{{$options['menu_background_color']}}" />
								<div class="input-group-addon">
									<i class="color-preview"></i>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<label for="menu_background_hover_color">Menu Background Hover Color</label>
							<div class="input-group">
								<input type="text" class="form-control colorpicker" data-format="hex" name="menu_background_hover_color" value="{{$options['menu_background_hover_color']}}" />
								<div class="input-group-addon">
									<i class="color-preview"></i>
								</div>
							</div>
						</div>
					</div>	
					<div class="row">	 
						<div class="col-md-6">
							<label for="menu_text_color">Menu Text Color</label>
							<div class="input-group">
								<input type="text" class="form-control colorpicker" data-format="hex" name="menu_text_color" value="{{$options['menu_text_color']}}" />
								<div class="input-group-addon">
									<i class="color-preview"></i>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<label for="menu_text_hover_color">Menu Text Hover Color</label>
							<div class="input-group">
								<input type="text" class="form-control colorpicker" data-format="hex" name="menu_text_hover_color" value="{{$options['menu_text_hover_color']}}" />
								<div class="input-group-addon">
									<i class="color-preview"></i>
								</div>
							</div>
						</div>
					</div>	
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="max_bandwidth">Maximum Bandwidth Per User (in kbps)</label>
								<div class="input-spinner">
									<button type="button" class="btn btn-default" data-step="-64">-</button>
									<input type="text" name="max_bandwidth" class="form-control" value="{{$options['max_bandwidth']}}" data-min="64" data-max="25600" />
									<button type="button" class="btn btn-default" data-step="64">+</button>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="session_time_limit">Session time limit (in hours)</label>
								<div class="input-spinner">
									<button type="button" class="btn btn-default" data-step="-1">-</button>
									<input type="text" name="session_time_limit" class="form-control" value="{{$options['session_time_limit'] / 3600}}" data-min="1" data-max="24" />
									<button type="button" class="btn btn-default" data-step="1">+</button>
								</div>
							</div>
						</div>
					</div>                            
                    
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="privacy_policy">Privacy & Policy</label>
								<textarea class="form-control richeditor" id="privacy_policy" name="privacy_policy">{{$options['privacy_policy']}}</textarea>
							</div>	
						</div>
						<div class="col-md-6">
							<div class="form-group">
							<label for="terms_condition">Terms Condition</label>
							<textarea class="form-control richeditor" id="terms_condition" name="terms_condition">{{$options['terms_condition']}}</textarea>
							</div>
						</div>                            
                    </div>
					@if(Session::get('USER_TYPE') == '1')					
					<!-- Campaign's Standard Terms & Condition-->
					<div class="row">
						<div class="col-md-12">
                            <div class="form-group">
                                <label for="standard_terms_privacy">Standard Terms of Service and Privacy </label>
                                <textarea class="form-control richeditor" id="standard_terms_privacy" name="standard_terms_privacy">{{$options['standard_terms_privacy']}}</textarea>
                            </div>
						</div>
					</div>		
                    @endif				
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="footer">Footer</label>
								<textarea class="form-control richeditor" id="footer" name="footer">{!! $footer !!}</textarea>
							</div>
						</div>
					</div>	
					@if(Session::get('USER_TYPE') == '1')                    
                    <div class="row">
                    	<div class="col-md-12">
                    		<h2>Message</h2>
                    	</div>
                    </div>
                    <div class="row">
                    	<div class="col-md-6">
							<div class="form-group">
								<label for="footer">No campaign</label>
								<textarea class="form-control richeditor" id="no_campaign" name="no_campaign">{{$options['no_campaign']}}</textarea>
							</div>
                    	</div>
                    	<div class="col-md-6">
							<div class="form-group">
								<label for="footer">Device not active</label>
								<textarea class="form-control richeditor" id="device_not_active" name="device_not_active">{{$options['device_not_active']}}</textarea>
							</div>
                    	</div>
                    </div>
                    <div class="row">
                    	<div class="col-md-6">
							<div class="form-group">
								<label for="footer">Thank You Message (Recurly)</label>
								<textarea class="form-control richeditor" id="recurly_thankyou_message" name="recurly_thankyou_message">{{$options['recurly_thankyou_message']}}</textarea>
							</div>                            
                    	</div>
                    	<div class="col-md-6">
                    		<div class="form-group">
								<label for="footer">Conversion Tracking Code</label>
								<textarea class="form-control" rows="21" id="conversion_tracking_code" name="conversion_tracking_code">{{$options['conversion_tracking_code']}}</textarea>
							
                            </div>
                    	</div>
                    </div>
                    <div class="row">
                    	<div class="col-md-12">
                    		<div class="form-group">
								<label for="footer">Members Area</label>
								<textarea class="form-control richeditor" id="members_area_content" name="members_area_content">{{$options['members_area_content']}}</textarea>
							
                            </div>
                    	</div>
                    </div>					
                    @endif
                    <div class="row">
	                    <div class="col-md-12">
	                        <div class="form-group">
	                        	<button id="btn_update" class="btn btn-info btn-lg btn-block" type="submit"><i class="fa fa-save"></i> Save</button>
	                        	<input type="hidden" name="hdn_site_logo" value="{{$logo}}"/>
	                        	<input type="hidden" name="hdn_site_favicon" value="{{$options['favicon']}}"/>
	                        </div>
	                    </div>
                    </div>
                </form>
         {!! $footer !!}
    </div>
</div>

<script>
    function CheckSubdomain(element) {
        var value = element.value;
        var current_subdomain_title = '{{$subdomain_title}}';
        $("#subdomain_error").text('');
        $("#btn_update").removeClass('disabled');
        if (value !== current_subdomain_title) {
            if (value !== "") {
                var validrules = /[^a-zA-Z0-9\-]/;
                if (validrules.test(value)) {
                    $("#subdomain_error").text('Given characters are not allowed.');
                    $("#btn_update").addClass('disabled');
                } else {
                    $("#al_subdomain").removeClass('hide');
                    $.ajax({
                        url: '{{url("ajax/CheckSubdomain")}}',
                        type: 'POST',
                        data: {subdomain: value},
                        complete: function (output) {
                            $("#al_subdomain").addClass('hide');
                            if ($.trim(output.responseText) !== "") {
                                $("#btn_update").addClass('disabled');
                                $("#subdomain_error").html(output.responseText);
                            }
                        }
                    });
                }
            }
        }
    }
    function GenerateAppSecretKey() {
        $("#app_secret_key").val(Math.random().toString(36).substring(2).toUpperCase());
    }

    function removelogoname(name){
        $("#hdn_"+name+"_name").val('');
    }
</script>

<script src="{{$assets_dir.'/js/fileinput.js'}}"></script>
<script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
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
			imageUploadURL: '{{url('ajax/editorsimageupload')}}',
			imagesLoadURL: '{{url('ajax/editorsmedia')}}',
			imageDeleteURL: '{{url('ajax/editorsimgdel')}}',
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
 @endsection
