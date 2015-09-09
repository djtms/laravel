@extends('template.layout')

@section('content')
@if (Request::has('res') && (Request::input('res') != 'notyet')) {
	{{--*/$url = (Request::has('userurl') && Request::input('userurl') != "") ? Request::input('userurl') : "https://www.google.com"/*--}}
	<script type="text/javascript">
      location.href = {{$url}}
	</script>    
@endif
{{--*/$agree_btn_text = explode('/', $language['agree_btn_text'])/*--}}
<style>
html, body {
    height:auto;
}
body {
	height: 100%;
	background-image: url('{{$campaign->background_image != "" ? $campaign->background_image : "" }}');
	background-color: {{$campaign->background_color}};
	background-position: center center;
    background-repeat: no-repeat;
    background-attachment:fixed;
	-webkit-background-size:cover;           /* Safari 3.0 */
	-moz-background-size:cover;           /* Gecko 1.9.2 (Firefox 3.6) */
	-o-background-size:cover;           /* Opera 9.5 */
	background-size:cover;
}

.landingpage-bg {
	background-color: {{$campaign_meta['layer_bg_color']}};
	height:100%; 
	max-width:440px;
	margin-top:25px;
	margin-bottom:25px;
	-webkit-border-radius: 15px;
	-moz-border-radius: 15px;
	border-radius: 15px;
}

.landingpage {
	margin: 10px auto 0;
	max-width: 290px;
	font-size: 16px !important;
	text-align: center;
	padding-top:5px;
}
	
.conect-social-icon {
	margin-bottom: 10px;
}

#landingpage_header img, #landingpage_footer img {
	width: 100% !important;
}

#landingpage_terms_condition {
	font-size: 12px;
}

#landingpage_footer h1 {
	font-size: 16px;
}

#landingpage_header iframe, #landingpage_footer iframe {
	width:100%;
	height:100%;
}

.input-box {
	margin-top:15px;
	clear:both;
}

label.error{
	color: red;
	font-size: 12px;
	font-style: italic;
}

.gender {
	position:relative;
}

.gender label.error{
	position: absolute;
	left:-79px;
	top:20px;
}

.language {
	padding:5px;
}

#landingpage_footer {
	margin-bottom:15px;
	display:block;
	clear:both;
}

@media ( max-width : 580px) {

    .landingpage-bg {
		min-height:264px;
		height:auto;
		margin-top:0px;
		margin-bottom:0px;
		-webkit-border-radius:0px;
		-moz-border-radius:0px;
		border-radius:0px;
	}
	
	.landingpage {
		max-width: 210px;
		padding-bottom:67px;
	}
	
	.btn-social.btn-custom-social {
		padding-left: 38px;
		font-size: 13px;
		line-height: 1.6;
		padding-bottom: 5px;
		padding-right: 15px;
		padding-top: 5px;
	}
	
	.btn-social.btn-custom-social > *:first-child {
		font-size: 1.5em;
		line-height: 32px;
		width: 34px;
	}
  
}

@media ( min-width : 581px) and (max-width: 767px){
	
    .landingpage-bg {
		margin-top:25px;
		margin-bottom:25px;
	}
	
	.landingpage {
		max-width: 270px;
		padding-bottom:60px;
	}
	
	.btn-social.btn-custom-social {
		padding-left:56px;
		line-height: 1.4;
		padding-bottom: 8px;
		padding-right: 12px;
		padding-top: 8px;
		vertical-align: middle;
	}
	
	.conect-social-icon .btn {
		font-size: 17px;
	}
	
	.btn-social.btn-custom-social > *:first-child{
		font-size: 1.8em;
		line-height: 38px;
		width:44px;
	}
	
	select.input-custome {
		height: 38px;
		line-height: 38px;
	}
	
	.input-custome {
		font-size: 13px;
		padding-bottom: 10px;
		padding-left: 16px;
		padding-right: 16px;
		padding-top: 10px;
		margin-bottom: 20px;
		margin-top: 20px;	
	}
  
}

 /* Small devices (tablets, 768px and up) */
@media ( min-width : 768px) and (max-width: 992px) {
	
	.landingpage-bg {
		margin-top:25px;
		margin-bottom:25px
	}
	
	.landingpage {
		max-width: 270px;
		padding-bottom:60px;
	}
	
	#landingpage_footer h1 {
		font-size: 18px;
	}
	#landingpage_terms_condition .agree {
		text-align: right;
	}
	
	.btn-social.btn-custom-social {
		padding-left:56px;
		line-height: 1.4;
		padding-bottom: 8px;
		padding-right: 12px;
		padding-top: 8px;
		vertical-align: middle;
	}
	
	.conect-social-icon .btn {
		font-size: 17px;
	}
	
	.btn-social.btn-custom-social > *:first-child{
		font-size: 1.8em;
		line-height: 38px;
		width:44px;
	}
	
	select.input-custome {
		height: 38px;
		line-height: 38px;
	}
	
	.input-custome {
		font-size: 13px;
		padding-bottom: 10px;
		padding-left: 16px;
		padding-right: 16px;
		padding-top: 10px;
		margin-bottom: 20px;
		margin-top: 20px;	
	}
	
}

@media ( min-width : 1024px) {
	
	.btn-social.btn-custom-social {
		padding-left: 60px;
		line-height: 1.33;
		padding-bottom: 14px;
		padding-top: 14px;
		padding-right:16px;
		font-size: 18px;
	}
	
	.btn-social.btn-custom-social > *:first-child {
		font-size: 2em;
		line-height: 52px;
		width: 50px;
	}
	
	
	select.input-custome {
		height: 41px;
		line-height: 41px;
	}
	
	.input-custome {
		font-size: 15px;
		padding-bottom: 10px;
		padding-left: 16px;
		padding-right: 16px;
		padding-top: 10px;
		margin-bottom: 30px;
		margin-top: 30px;	
	}
		 
</style>
<div class="container landingpage-bg" style="font-size: 14px; color: {{$campaign->text_color}};">
    <div class="landingpage">
    @if ($campaign_meta['lang_option'] == 'true')
    	<div class="row">
            <div class="language">
	            <form id="frm_change_lang" method="POST">
	                <select id="ddl_language" name="language" class="form-control input-sm input-custome">
	                	@foreach ($languages as $lang)
	                	<option {{$lang['lang_code'] == Session::get('lang_code') ? "selected='selected'" : ""}} value="{{$lang['lang_code']}}">{{$lang['lang']}}</option>
	                	@endforeach
	                </select>
	            </form>
            </div>
        </div>
       @endif
        @if ($campaign->header_html != null)
            <div id="landingpage_header" class="row">
                <div style="padding: 0 5px;">
                   {!! $campaign->header_html !!}
                </div>
            </div>
            <div style="padding-top: 25px;"></div>
       @endif
        <div id="landingpage_terms_condition" class="row">
            <div class="col-sm-4" style="padding: 0 5px; margin-bottom: 5px;">
            	<div class="switch-box" style="white-space:nowrap; text-overflow:ellipsis; overflow: hidden">
                	<input id="chk_terms_condition" type="checkbox" class="boots-switch"
                     data-on-color="success" data-size="mini" data-on-text="{{$agree_btn_text[0]}}"
                     data-off-text="{{$agree_btn_text[1] }}" />
                </div>
            </div>
            <div class="col-sm-8 text-center agree" style="padding: 0 5px; margin-bottom: 5px; font-size: 14px;">
                <a style="color: {{$campaign->text_color}};" href="javascript:showCustomTerm();">{{$language['agree']}}</a>
            </div>

        </div>
        <div id="landingpage_body" class="row" style="margin-top: 20px;">
            <div class="conect-social" style="padding: 0 5px;">
	            @if(in_array(1, $apptype))
	            <div class="conect-social-icon">
		            <a id="facebook" class="btn btn-block btn-custom-social btn-social btn-facebook social_link" href="{{url('sociallogin/facebook')}}"> <i class="fa fa-facebook"></i>
{                      {{$language['facebook']}}
					</a>
	            </div>
	           @endif
	            @if(in_array(2, $apptype))
	            <div class="conect-social-icon">
	            	<a id="twitter" class="btn btn-block btn-custom-social btn-social btn-twitter social_link" href="{{url('sociallogin/twitter')}}"><i class="fa fa-twitter"></i> 
	                   {{$language['twitter']}}
					</a>
				</div>
	            @endif
	            @if(in_array(3, $apptype))
	            <div class="conect-social-icon">
	            	<a id="google" class="btn btn-block btn-custom-social btn-social btn-google-plus social_link" href="{{url('sociallogin/googleplus')}}"><i class="fa fa-google-plus"></i> 
                       {{$language['google']}}
					</a>
				</div>
	            @endif
	            @if(in_array(4, $apptype))
	            <div class="conect-social-icon">
	            	<a id="linkedin" class="btn btn-block btn-custom-social btn-social btn-linkedin social_link" href="{{url('sociallogin/linkedin')}}"> <i class="fa fa-linkedin"></i> 
                       {{$language['linkedin']}}
	                </a>
	            </div>
				@endif
	            @if(in_array(6, $apptype))
	            <div class="conect-social-icon">
	            	<a id="instagram" class="btn btn-block btn-custom-social btn-social btn-instagram social_link" href="{{url('sociallogin/instagram')}}"> <i class="fa fa-instagram" style='color: #fff;'></i> {{$language['instagram']}}</a>
				</div>
	            @endif
	            @if(in_array(7, $apptype))
	            <div class="conect-social-icon hide">
	            	<a id="vkontakte" class="btn btn-block btn-custom-social btn-social btn-vk social_link" href="{{('sociallogin/vkontakte')}}"> <i class="fa fa-vk" style='color: #fff;'></i>  Connect With Vkontakte</a>
				</div>
	            @endif
	            @if($campaign->fields_email != "")
	            <div class="conect-social-icon">
							<a id="custom-email" class="btn btn-block btn-custom-social btn-social btn-openid social_link" href="javascript:show_user_registration_modal();"> <i class="fa fa-envelope-o"></i> {{$language['email']}}</a>
						</div>
	            @endif
	            <div style="padding-top: 25px;"></div>
            </div>
        </div>
         @if ($campaign->footer_html != null)
            <div id="landingpage_footer" class="row">
                <div style="padding: 0 5px;">
                   {!! $campaign->footer_html !!}getFieldsEmail
                </div>
            </div>
        @endif
    </div><!-- End landingpage -->

    @if ($campaign->fields_email)
        {{--*/$fields = explode(';', $campaign->fields_email)/*--}}
        <!-- Custom user create Modal -->
        <div class="modal fade" id="div_user_registration">
            <div class="modal-dialog">
                <div class="row">
                    <div class="col-md-12">
                        <form name="frm_user_regrstration" id="frm_user_regrstration" method="POST" action="">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h3 class="modal-title">{{$language['custom_login_modal_title']}}</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="row hide" id="div_confirmation">
                                        <div class="alert fade in" id="div_confirmation_msg"></div>
                                    </div>
                                    <p class="text-center" style="color:red;font-weight: bold; font-style: italic"><i class="fa fa-info-circle"></i> {{$language['require_text']}}</p>
                                    @if (in_array('Name', $fields))
                                        <div class="form-group">
                                            <label for="name" class="control-label">{{$language['element_name']}}</label>
                                            <input type="text" placeholder="{{$language['placeholder_name']}}" name="user_name" class="form-control" required>
                                        </div>
                                    @endif
                                    @if (in_array('Email', $fields))
                                        <div class="form-group">
                                            <label for="email" class="control-label">{{$language['element_email']}}</label>
                                            <div class="form-group">
                                                <input type="email" placeholder="{{$language['placeholder_email']}}" name="user_email" class="form-control" required>
                                            </div>
                                        </div>
                                    @endif
                                    @if (in_array('Phone Number', $fields))
                                        <div class="form-group">
                                            <label for="user_phone" class="control-label">{{$language['element_phone']}}</label>
                                            <input type="text" placeholder="{{$language['element_phone']}}" name="user_phone" class="form-control" required>
                                        </div>
                                    @endif
                                    @if (in_array('Gender', $fields))
                                        <div class="form-group gender">
                                            <div class="row">
                                                <label for="gender" class="col-sm-2 control-label">{{$language['element_gender']}}</label>
                                                <div class="col-sm-10">
                                                    <label class=""> <input type="radio" value="male" id="rb_male" name="gender" required> {{$language['gender_type']['male']}}</label>
                                                    <label class=""> <input type="radio" value="female" id="rb_female" name="gender"> {{$language['gender_type']['female']}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (in_array('City', $fields))
                                        <div class="form-group">
                                            <label for="user_city" class="control-label">{{ $language['element_city']}}</label>
                                            <input type="text" placeholder="{{$language['placeholder_city']}}" name="user_city" class="form-control" required>
                                        </div>
                                    @endif
                                    @if (in_array('Country', $fields))
                                        <div class="form-group">
                                            <label for="user_country" class="control-label">{{$language['element_country']}}</label>
                                            <input type="text" placeholder="{{$language['placeholder_country']}}" name="user_country" class="form-control" required>
                                        </div>
                                    @endif
                                    @if (in_array('Year Born', $fields))
                                        <div class="form-group">
                                            <label for="user_dob" class="control-label">{{$language['element_dob']}}</label>
                                            <input type="text" placeholder="{{$language['placeholder_dob'] }}" name="user_dob" class="form-control datepicker" data-format="yyyy-mm-dd" required>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" value="{{Request::has('called') ? Request::input('called') : ""}}" name="hdn_device_mac" /> 
                                    <input type="hidden" value="{{Request::has('mac') ? Request::input('mac') : "" }}" name="hdn_client_mac" /> 
                                    <input type="hidden" value="{{Session::get('info.location_id') ? Session::get('info.location_id') : "" }}" name="hdn_location_id" /> 
                                    <input type="hidden" value="{{Request::has('useruel') ? Request::input('useruel') : "" }}" name="hdn_useruel" id="hdn_useruel" />
                                    <button id="btn_connect" type="submit" class="btn btn-info"><i class="fa fa-globe"></i> {{$language['connect_button']}} <img id="al_custom_login" class="hide" src="{{$assets_dir.'/images/ajax-loader.gif'}}" alt="ajax-loader"/></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Terms and condition Modal -->
    <div class="modal fade" id="custom_term_modal" tabindex="-1"
         role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">{{$language['custom_terms_condition']}}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             {{$campaign->custom_term != "" ? $campaign->custom_term : $standard_terms_privacy}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{$language['close']}}</button>
                </div>
            </div>
        </div>
    </div><!-- End Terms and condition Modal -->

    <div class="modal fade text-center" id="termsconditionalert">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{$language['agree_error_message']}}</h4>
                    <br>
                    <button type="button" class="btn btn-green" data-dismiss="modal">{{ $language['ok']}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Terms and error Modal -->
</div><!-- End Landingpage-bg -->
<script type="text/javascript">
    $(document).ready(function () {
        $('.social_link').click(function () {
            var switch_status = $('#chk_terms_condition').bootstrapSwitch('state');
            if (switch_status === true) {
                return true;
            } else {
                $("#termsconditionalert").modal('show');
                return false;
            }
        });

        $("html").niceScroll();
        //$("#frm_user_regrstration").validate();
        $("#frm_user_regrstration").validate({
            submitHandler: function (form) {
                save_user_data();
            }
        });
        $(".boots-switch").bootstrapSwitch();
        $(".page_loading_status_bar div").animate({ width: "100%" }, 2000 );
    	setTimeout(function() { $(".page_loading_status_bar").fadeOut(2000); }, 4000);

    	$(".datepicker").datepicker({format:'yyyy-mm-dd'});

    	jQuery.extend(jQuery.validator.messages, {
    	    required: "{{$language['field_required']}}",
    	    email: "{{$language['invalid_email']}}"
    	});

    	$("#ddl_language").change(function(){
        	$("#frm_change_lang").submit();
        });
    	
    });

    function disabler(event) {
        event.preventDefault();
        return false;
    }

    function show_user_registration_modal() {
        $("#div_user_registration").modal('show');
    }

    function check_email(email) {
        $("#btn_connect").removeClass('disabled');
        $("#error_msg").html('');
        var emailValidator = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        if (emailValidator.test(email) && email !== "") {
            $("#al_email_exist").removeClass('hide');
            $.ajax({
                url: '{{url("ajax/CheckEmailInSocialUserTable")}}',
                type: 'POST',
                data: {email: email},
                complete: function (output) {
                    $("#al_email_exist").addClass('hide');
                    if (output.responseText !== "") {
                        $("#error_msg").html(output.responseText);
                        $("#btn_connect").addClass('disabled');
                    }
                }
            });
        }
    }

    function save_user_data() {
        var data = $('#frm_user_regrstration').serialize();
        $("#al_custom_login").removeClass('hide');
        $.ajax({
            type: 'POST',
            cache: false,
            url: '{{url("ajax/SaveUserData")}}',
            data: data,
            success: function (result) {
                $("#al_custom_login").addClass('hide');
                var obj = jQuery.parseJSON(result);
                if (parseInt(obj.status) === 1 && obj.url !== "") {
                    $("#div_confirmation_msg").html('<div class="alert alert-success alert-dismissible fade in"><i class="entypo-info-circled"></i> {{$language['operation_success']}}</div>');
                    window.setTimeout(function () {
                        window.location.href = obj.url;
                    }, 1500);
                } else {
                    $("#div_confirmation_msg").html('<div class="alert alert-danger alert-dismissible fade in"><i class="entypo-cancel-circled"></i> {{$language['operation_failed']}}</div>');
                }
                $("#div_confirmation").removeClass('hide');
            }
        });
    }
    function close_modal() {
        $("#div_confirmation_msg").empty();
        $("#div_confirmation").addClass('hide');
    }

    function showCustomTerm() {
        $("#custom_term_modal").modal('show');
    }
</script>
@endsection('content')