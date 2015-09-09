<!DOCTYPE html>
<html lang="en">
    <head>    	
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="MyWiFi Admin Dashboard" />
        <meta name="author" content="" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{$title}}</title>
        <link rel="shortcut icon"  href="{{Session::get('FAVICON')}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/bootstrap-min.css'}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/font-icons/font-awesome/css/font-awesome.min.css'}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/font-icons/entypo/css/entypo.css'}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/bootstrap-switch.min.css'}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/custom.css'}}">
        <link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>

		
		@if($controller != 'landingpage')		
		<link rel="stylesheet" href="{{$assets_dir.'/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css'}}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">    
              
        <link rel="stylesheet" href="{{$assets_dir.'/js/select2/select2-bootstrap.css'}}">
		<link rel="stylesheet" href="{{$assets_dir.'/js/select2/select2.css'}}">
		<link rel="stylesheet" href="{{$assets_dir.'/js/rickshaw/rickshaw.min.css'}}">
		<link rel="stylesheet" href="{{$assets_dir.'/js/icheck/skins/minimal/_all.css'}}">
		<link rel="stylesheet" href="{{$assets_dir.'/css/animate.css'}}">
        @endif
        
        <link rel="stylesheet" href="{{$assets_dir.'/css/neon-core.css'}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/neon-theme.css'}}">
        <link rel="stylesheet" href="{{$assets_dir.'/css/neon-forms.css'}}">        
        <link rel="stylesheet" href="{{$assets_dir.'/css/bootstrap-social.css'}}">
        
        <!-- Load campaign css -->
        @if($controller == 'campaign')        
        <link rel="stylesheet" href="{{$assets_dir.'/css/campaign_css.css'}}">
        @endif

        <script src="{{$assets_dir.'/js/jquery-1.11.0.min.js'}}"></script>
        
        <!-- Campaign Header Analytics Script --> 
        @if($controller == 'landingpage')   
        {!! Session::get('analytics_header_script')?Session::get('analytics_header_script'):''!!}    
        @endif        
        <!-- End -->
        @if($controller == 'location')
        <link rel="stylesheet" href="{{$assets_dir.'/css/location_css.css'}}">
        @endif
        @if($controller == 'user' && $action == 'editprofile')
        <style>
		.plan-details {
			background-color: #EBEBEB;
		}
		</style>
        @endif
        <link rel="stylesheet" href="{{$assets_dir.'/css/location_css.css'}}">
        
        @if($controller == 'report')
	        <style>
			.DTTT_button_copy div,.DTTT_button_csv div,.DTTT_button_xls div,.DTTT_button_pdf div,.DTTT_button_print div
				{
				z-index: 6000 !important;
				height: 30px;
				width: 50px;
			}
			
			.DTTT_button_copy embed,.DTTT_button_pdf embed,.DTTT_button_xls embed,.DTTT_button_csv embed
				{
				height: 30px;
				width: 50px;
			}
			
			#reportrange.btn-xs {
				padding: 5px;
			}
			
			.scrollable [class^="entypo-"]:before,.scrollable [class*=" entypo-"]:before
				{
				margin-left: 0 !important;
				margin-right: 0 !important;
			}
			
			.media-icon {
				font-size: 18px;
				line-height: 18px;
				background-color: #FFFFFF;
				position: absolute;
				bottom: 0;
				right: 26px;
				z-index: 999;
			}
			
			#reportrange {
				float:right;
			}
			</style>
        @endif     
        @if($controller == 'subuser')
        	<style>
		    .form-wizard {
		        margin-top: 10px;
		    }
		    #add_new_sub_user {
		        margin-bottom:10px;
		    }
		
		    .subusertbl label {
		        margin-bottom: 5px;
				margin-right:2px;
		    }
		
		    /*.checkbox-replace .cb-wrapper + label{
		        top: -8px;
		    }
		    */
		    .table td .label {
		        margin-bottom:5px;
		    }
		
		 </style>
        @endif
        @if($controller == 'user')
        	<style>
			#conten-social .social-icon {
				font-size:28px;
			}
			
			#conten-social .entypo-facebook-circled {
				color:#47639E;
			}
			
			#conten-social .entypo-twitter-circled {
				color:#00ABF0;
			}
			
			#conten-social .entypo-gplus-circled {
				color:#C0382A;
			}
			
			#conten-social .entypo-linkedin-circled {
				color:#017EB4 ;
			}
		
		</style>
        @endif  
        @if($controller == 'location' && $action == 'overview')
        	<style>

			    #edit_location .modal-header, #edit_location .modal-footer {
			        padding-top:8px;
			        padding-bottom:8px;
			    }
				
				#edit_location .modal-body h4 {
					font-size:18px;
					margin-top:0px;
				}
			
			    #edit_location .modal-body{
			        padding-top:5px;
			        padding-bottom:5px;
			    }
			
			    #edit_location .modal-body h3 {
			        margin-top:0px!important;
			    }
			
			    #edit_location .modal-footer {
			        margin-top:0px;
			    }
				
				#edit_location .modal-header .form-group, #edit_location .modal-content .form-group{
					margin-bottom:8px!important;
				}
			
			    .form-wizard {
			        margin-top: 5px;
			    }
				
				#edit_location .control-label {
					margin-bottom:3px;
				}
				
			    .campaigns-part{
			        margin-bottom:15px;
			    }
			    .modal-backdrop{ z-index:8;}
			
			    #edit_location, #add_new_device, #add_new_campaign, #edit_schedule{ z-index:10;}
			
			    #my_tab ul.nav-tabs li a {
			        font-size:16px;
			    }
			
			    #my_tab .tab-content {
			        margin-top:10px;
			    }
			
			    .panel-body .thumbnail {
			        border:none!important;
			        margin-bottom:0px!important;
			        padding:0px!important;
			    }
			
			    .panel-body .thumbnail .caption {
			        padding: 0px;
			    }
			
			    .backgroundiphone{
			        position: relative; 
			        height: 399px;
			        width: 192px;
			        background-image: url({{$assets_dir.'/images/iphone-small.png'}});
			        background-position:center center;
			        background-repeat:no-repeat;
			        margin:0 auto;
			    }
			
			    .backgroundiphone .backgroundiphone-wrapper {
			        position: absolute;
			        top:67.5px;
			        left:18.49px;
			        width:91%;
			    }
			    .backgroundiphone .backgroundiphone-content {
			        height:60%!important;
			        width:99%!important;
			    }
			
			    .backgroundiphone iframe {
			        height:264px;
			        width:89%;
			        border:none;
			    }
			
			    .custom-font-size{
			        font-size:35px;
			    }
			
			    .landing-page h3 {
			        text-align:center;
			        padding-left:10px;
			        margin-top:2px;
			    }
			
			    .select2-container {
			        left: 0px!important;
			        top: 0px!important;
			        width:100%!important;
			    }
			
			    .media-icon{
			        font-size: 18px;
			        line-height:18px;
			        background-color:#FFFFFF;
			        position: absolute;
			        bottom:0;
			        right:0px;
			        z-index:999;
			    }
			
			    .devices .panel{
			        min-height:238px;
			    }
			
			    @media (min-width:1024px) { 
			
			        .extra-col {
			            width:12.001%!important;
			        }
			
			        .extra-col-big {
			            width:87.99%!important;
			        }
			
			    }
			
			</style>
        @endif
        @if($controller == 'options')
         <link href="{{$assets_dir.'/js/froala_editor/css/froala_editor.min.css'}}" rel="stylesheet" type="text/css">
         <link href="{{$assets_dir.'/js/froala_editor/css/froala_style.min.css'}}" rel="stylesheet" type="text/css">
        @endif 
    </head>
    <body class="page-body">
	    <div class="progress page_loading_status_bar">
	  		<div class="progress-bar progress-bar-warning progress-bar-striped"></div>
		</div>
	    <!-- BEGAIN PRELOADER -->
	    <div id="preloader" class="hide">
	      <div id="status">&nbsp;</div>
	    </div>
	    <!-- END PRELOADER -->	    
		<div id="modal_delete_confirmation" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body text-center"></div>
					<div class="modal-footer text-center"></div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		@yield('content')	
		<!-- Common librarys -->
		<script
			src="{{$assets_dir.'/js/bootstrap.min.js'}}"></script>
		<script
			src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
		<script
			src="{{$assets_dir.'/js/bootstrap-switch.min.js'}}"></script>
		<script
			src="{{$assets_dir.'/js/jquery.nicescroll.min.js'}}"></script>
		<script
			src="{{$assets_dir.'/js/bootstrap-datepicker.js'}}"></script>
		
		@if($controller != 'landingpage')		
		<!-- Bottom Scripts -->
		<script src="https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
		<script src="{{$assets_dir.'/js/gsap/main-gsap.js'}}"></script>
		<script src="{{$assets_dir.'/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/select2/select2.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/joinable.js'}}"></script>
		
		<script src="{{$assets_dir.'/js/neon-api.js'}}"></script>		
		<script src="{{$assets_dir.'/js/jquery.sparkline.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/rickshaw/vendor/d3.v3.js'}}"></script>
		<script src="{{$assets_dir.'/js/rickshaw/rickshaw.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/raphael-min.js'}}"></script>
		<script src="{{$assets_dir.'/js/morris.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/prettify.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/toastr.js'}}"></script>
		<script src="{{$assets_dir.'/js/neon-chat.js'}}"></script>
		<script src="{{$assets_dir.'/js/resizeable.js'}}"></script>
		<script src="{{$assets_dir.'/js/neon-custom.js'}}"></script>
		<script src="{{$assets_dir.'/js/neon-demo.js'}}"></script>
		<script src="{{$assets_dir.'/js/jquery.inputmask.bundle.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/bootstrap-timepicker.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/bootstrap-colorpicker.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/daterangepicker/moment.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/daterangepicker/daterangepicker.js'}}"></script>
		<script src="{{$assets_dir.'/js/tocify/jquery.tocify.min.js'}}"></script>
		<script src="{{$assets_dir.'/js/jquery.cookie.js'}}"></script>
		<script src="{{$assets_dir.'/js/icheck/icheck.min.js'}}"></script>
		<script type="text/javascript">
			$(document).ready(function () {
			        
			        $(".boots-switch").bootstrapSwitch();
			        $('.mac').keydown(function (e) {
			        	var code = e.keyCode || e.which;
			        	var valid_code = [8, 9, 13, 16, 17, 18, 20, 27, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 65, 66, 67, 68, 69, 70, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 116];
			        	if(code !== ""){
				        	if($.inArray( code, valid_code ) == -1){
				        		$('.msg_duplicate_mac').text('MAC address must be characters 0 to 9, and letters from A to F');
				        		$(".next").addClass('hide');
				        		return false;        		
				            }else{            	
				            	$('.msg_duplicate_mac').text('');
				            	$(".next").removeClass('hide');
				            }
			        	}
			        });				
				@if (Session::get('USER_TYPE') && Session::get('USER_TYPE') == '2' && Session::get('END_TOUR') == "")
			            if (!$.cookie("current_class")) {
			                $.cookie("current_class", "first-tour");
			            }
			            var objSteps = steps_object();
			            var current_class = $.cookie("current_class");
			            var data = objSteps[current_class];
			            $.cookie("next_class", data.next_class);
			            $.cookie("prev_class", data.prev_class);
			            generate_tour_window(current_class, data.title, data.content, data.placement, data.next_class, data.prev_class);
				@endif
			
				$(".page_loading_status_bar div").animate({ width: "100%" }, 2000 );
			
				setTimeout(function() { $(".page_loading_status_bar").fadeOut(2000); }, 4000);
				//show_loading_bar(100);
			
				$('input.icheck').iCheck({
					checkboxClass: 'icheckbox_minimal',
					radioClass: 'iradio_minimal'
				});
			
			});
			
			function statisticsDeleteConfirmation(type, identifier){
				var msg = "All social statistics will be deleted for forever for this "+type+".<br>Are you sure you want to delete?";
				var footer_actions = "<button type='button' class='btn btn-default' data-dismiss='modal'>No</button>"+
									 "<button type='button' onclick='javascript:deleteStatistics(&#39;"+type+"&#39;, &#39;"+identifier+"&#39;);' class='btn btn-red'>Yes</button>";
				 $("#modal_delete_confirmation .modal-body").html(msg);
				 $("#modal_delete_confirmation .modal-footer").html(footer_actions);
				 $("#modal_delete_confirmation").modal('show');
			}
			
			function deleteStatistics(type, identifier){
				$("#preloader").removeClass('hide');
				$.ajax({
					url: '{{url("ajax/ResetAllStatistics")}}',
					type: 'POST',
					data: {'type':type, 'identifier':identifier},
					success:function(output){
						$("#preloader").addClass('hide');
						$("#modal_delete_confirmation .modal-body").html(output);
						$("#modal_delete_confirmation .modal-footer").html("<button type='button' class='btn btn-info' data-dismiss='modal'>Ok</button>");
						$("#modal_delete_confirmation").modal('show');
					}
				});
			}
			
			    function checkDuplicateMac(data){
			    	var mac = data.toUpperCase();
			        var regex = /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/;
			        var device_id = $("[name=device_id]").val();
			        if(mac !== ""){
				        if (regex.test(mac)) {
				            $(".al_duplicate_mac").removeClass('hide');
				            $.ajax({
				                url: '{{url("ajax/CheckDuplicateMac")}}',
				                data: {mac: mac, device_id:device_id},
				                type: 'post',
				                success: function (result) {
				                    $(".al_duplicate_mac").addClass('hide');
				                    if (result === "") {
				                        $('.msg_duplicate_mac').text('');
				                        $(".next").removeClass('hide');
				                        $("#btn_update_device").removeClass('hide');
				                    } else {
				                        $('.msg_duplicate_mac').text(result);
				                        $(".next").addClass('hide');
				                        $("#btn_update_device").addClass('hide');
				                    }
				                }
				            });
				        }else{
				        	$('.msg_duplicate_mac').text('MAC address must be characters 0 to 9, and letters from A to F');
				    		$(".next").addClass('hide');
				    		return false;  
				        }
			        }else{
			        	$('.msg_duplicate_mac').text('Invalid Request.');
			    		$(".next").addClass('hide');
			    		return false; 
			        }
			    }
			
			    function steps_object() {
			        var update = "<a href='#profile_update'><b>Update</b></a>";
			        var steps =
		                '{'
		                + '"first-tour":{"title":"<b>Welcome to MyWiFi</b>","content":"Before you get started please take a moment to go through our tour to help get you up and running quickly.","placement":"bottom first","next_link":"<?php echo url('user/editprofile') ?>","next_class":"second-tour","prev_link":"javascript:void(0);","prev_class":""},'
		                + '"second-tour":{"title":"<b>Set Your Time Zone</b>","content":"Please set your local time zone and hit the ' + update + ' buttton to ensure MyWiFi has the proper time settings for your account.","placement":"top","next_link":"<?php echo url('location/view') ?>","next_class":"third-tour","prev_link":"<?php echo url('dashboard/view') ?>","prev_class":"first-tour"},'
		                + '"third-tour":{"title":"<b>Add Your Location</b>","content":"You must first setup a Location before you add your Device or create any Campaigns.","placement":"bottom","next_link":"<?php echo url('device/view') ?>","next_class":"fourth-tour","prev_link":"<?php echo url('user/editprofile') ?>","prev_class":"second-tour"},'
		                + '"fourth-tour":{"title":"<b>Add Your Device</b>","content":"Add your devices to your account in the Devices section. Be sure you follow the steps to install our custom router firmware.","placement":"bottom","next_link":"<?php echo url('campaign/view') ?>","next_class":"fifth-tour","prev_link":"<?php echo url('location/view') ?>","prev_class":"third-tour"},'
		                + '"fifth-tour":{"title":"<b>Create Your Campaign</b>","content":"Use the Campaign setup wizard to create your WiFi landing page. Be sure to first create a custom app if you intend to offer Social Media as a login option.","placement":"bottom","next_link":"<?php echo url('report/analytic') ?>","next_class":"sixth-tour","prev_link":"<?php echo url('device/view') ?>","prev_class":"fourth-tour"},'
		                + '"sixth-tour":{"title":"<b>View Campaign Analyttics</b>","content":"Once you have created your first Campaign you can view Analytics and data and sort it by Location or Device.","placement":"top","next_link":"<?php echo url('user/editprofile') ?>","next_class":"seventh-tour","prev_link":"<?php echo url('campaign/view') ?>","prev_class":"fifth-tour"},'
		                + '"seventh-tour":{"title":"<b>Manage Your Billing</b>","content":"Add more devices to your platform and manage your billing information under your profile.","placement":"bottom","next_link":"<?php echo url('dashboard/view') ?>","next_class":"last-tour","prev_link":"<?php echo url('report/analytic') ?>","prev_class":"sixth-tour"},'
		                + '"last-tour":{"title":"<b>Congratulations!</b>","content":"You are now ready to start using MyWiFi be sure to visit support.mywifi.com to view our knowledge base.","placement":"bottom","next_link":"javascript:void(0);","next_class":"","prev_link":"<?php echo url('user/editprofile') ?>","prev_class":"seventh-tour"}'
		                + '}';
			        return jQuery.parseJSON(steps);
			    }
			
			    function next_tour() {
			        var objSteps = steps_object();
			        var data = objSteps[$.cookie("current_class")];
			        $.cookie("current_class", $.cookie("next_class"));
			
			        if (data.next_link !== "") {
			            window.location = data.next_link;
			        } else {
			
			        }
			    }
			
			    function previous_tour() {
			        var objSteps = steps_object();
			        var data = objSteps[$.cookie("current_class")];
			        $.cookie("current_class", $.cookie("prev_class"));
			        if (data.next_link !== "") {
			            window.location = data.prev_link;
			        } else {
			
			        }
			    }
			
			    function end_tour() {
			        $('.' + $.cookie("current_class")).popover('destroy');
			        $.removeCookie("current_class");
			        $.cookie("next_class");
			        $.cookie("prev_class");
			
			        $.ajax({
			            url: '{{url('ajax/EndTour')}}',
			            tye: 'POST',
			            data: '',
			            complete: function (output) {
			            }
			        });
			
			    }
			
			    function generate_tour_window(element, title, content, placement, next_class, prev_class) {
			
			        var next_disable = "";
			        var prev_disable = "";
			        if (next_class === "") {
			            next_disable = "disabled";
			        }
			        if (prev_class === "") {
			            prev_disable = "disabled";
			        }
			
			
			        $('.' + element).popover({
			            html: true,
			            title: title,
			            content: content,
			            template: '<div class="popover">'
			                    + '<div class="arrow"></div>'
			                    + '<h3 class="popover-title"></h3>'
			                    + '<div class="popover-content"></div>'
			                    + '<div class="popover-navigation">'
			                    + '<div class="btn-group">'
			                    + '<a class="btn btn-sm btn-info ' + prev_disable + ' " href="javascript:previous_tour();">« Prev</a>'
			                    + '<a class="btn btn-sm btn-info ' + next_disable + '" href="javascript:next_tour();">Next »</a>'
			                    + '</div>'
			                    + '<a class="btn btn-sm btn-info  pull-right" href="javascript:end_tour();">End tour</a>'
			                    + '</div>'
			                    + '</div>',
			            placement: placement
			        }).popover('show');
			    }
			
			
			
			</script>
		
		@if(Session::get('USER_ID') && Session::get('USER_TYPE') != '3')
		<script>
			            window.intercomSettings = {
			                // TODO: The current logged in user's full name
			                name: "{{Session::get('FULL_NAME')}}",
			                // TODO: The current logged in user's email address.
			                email: "{{Session::get('EMAIL_ADDRESS')}}",
			                // TODO: The current logged in user's sign-up date as a Unix timestamp.
			                created_at: '{{Session::get('USER_CREATED_AT')}}',
			                app_id: "xd2zv02e"
			            };
			            (function () {
			                var w = window;
			                var ic = w.Intercom;
			                if (typeof ic === "function") {
			                    ic('reattach_activator');
			                    ic('update', intercomSettings);
			                } else {
			                    var d = document;
			                    var i = function () {
			                        i.c(arguments)
			                    };
			                    i.q = [];
			                    i.c = function (args) {
			                        i.q.push(args)
			                    };
			                    w.Intercom = i;
			                    function l() {
			                        var s = d.createElement('script');
			                        s.type = 'text/javascript';
			                        s.async = true;
			                        s.src = 'https://widget.intercom.io/widget/xd2zv02e';
			                        var x = d.getElementsByTagName('script')[0];
			                        x.parentNode.insertBefore(s, x);
			                    }
			                    if (w.attachEvent) {
			                        w.attachEvent('onload', l);
			                    } else {
			                        w.addEventListener('load', l, false);
			                    }
			                }
			            })();
			
			            
			            
			            (function (i, s, o, g, r, a, m) {
			                i['GoogleAnalyticsObject'] = r;
			                i[r] = i[r] || function () {
			                    (i[r].q = i[r].q || []).push(arguments)
			                }, i[r].l = 1 * new Date();
			                a = s.createElement(o),
			                        m = s.getElementsByTagName(o)[0];
			                a.async = 1;
			                a.src = g;
			                m.parentNode.insertBefore(a, m)
			            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
			
			            ga('create', 'UA-59493203-1', 'auto');
			            ga('send', 'pageview');
			
			        </script>
		@endif
		@if(Session::get('USER_ID') && Session::get('USER_ID') != "")		
		<input type="hidden" id="mytime"
			value="{{ base64_encode(time())}}" />
		<input type="hidden" id="rqu"
			value="{{Request::server("REQUEST_URI")}}">
		<script type="text/javascript">
			var check_user_session_time = function() {
				var current_server_time = $("#mytime").val();
				var rqu = $("#rqu").val();
				  $.ajax({
					  url:'{{url('ajax/Logoutuseraftersessiontimeout')}}',
					  type:'POST',
					  data:{current_server_time:current_server_time, rqu:rqu},
					  success:function(output){
						  output = output.trim('\n');
						  if(output != ''){
							  location.href = output;
							  }
						  }
					});
				};
			
				var interval = 1000 * 60 * 1; // where X is your every X minutes
			
				setInterval(check_user_session_time, interval);
	   </script>
		@endif
		
	@endif
		<style>
		.datepicker {
			z-index: 1151 !important;
		}
		</style>
		
		<!-- Campaign Footer Analytics Script -->
		@if($controller == 'landingpage' && !Session::get('USER_ID'))		
		{!! Session::get('analytics_footer_script')?Session::get('analytics_footer_script'):'' !!}		
		@endif
		<!-- End -->
		
		<!-- Campaign Footer Conversion Tracking Code -->
		@if($controller == 'landingpage' && $action == 'thankyoupage')
		{!! Session::get('conversion_tracking_code')?Session::get('conversion_tracking_code'):'' !!}		
		@endif
		<!-- End -->
	</body>
</html>
		