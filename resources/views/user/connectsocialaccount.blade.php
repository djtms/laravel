@extends('template.layout')
@section('content')
<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->	
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')
        <hr /> 
        <div class="row">
            <div class="col-sm-5">
                <h2 style="margin-top:0px;"><i class="entypo-share"></i>Social Integrations</h2> 
            </div>
            <div class="col-sm-7 text-right">
                <a href="javascript:connectnew();" class="btn btn-red btn-icon icon-left btn-lg hidden-print">
                    Create New App
                    <i class="entypo-plus-circled"></i>
                </a>
            </div>
        </div>	        
           {!! Session::get('message') !!}
           {{Session::forget('message')}}        
         <!-- /* if ($_SESSION['USER_TYPE'] != '3'): ?>
          <div class="row">
          <div class="col-md-12 search-box">
          <form name="frm-socialap-search" action="" method="POST">
          <div class="input-group">
          <input type="text" class="form-control input-lg" placeholder="Search your social app here by app name, full name or app username" name="socialap_search_value" value="echo $this->socialap_search_value>
          <div class="input-group-btn">
          <button class="btn btn-info btn-lg" name="search_socialap" type="submit">Search</button>
          </div>
          </div>
          </form>
          </div>
          </div>
          <!--//endif; */-->
        <br/>

        <!--        <div class="row">
                    <div class="col-md-10">
                        <h3>Connected Apps</h3>
                    </div>
                </div>-->
        <div class="row"> 
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-responsive table-bordered datatable dataTable" id="conten-social">                     
                        <thead>
                            <tr>
                                <th>App Type</th>
                                <th>App Name</th>
                                <th># of Connections</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>					
                        <tbody> 
                            @foreach($app_info as $info)                                                                                                       
                                    <tr>
                                    	<td class='text-center'>                                     	                                          		
                                    			@if($info->type == '1')
                                    				<i class="fa fa-facebook-square social-icon"></i>
                                    			@endif
                                    			@if($info->type == '2')
                                    				<i class="fa fa-twitter-square social-icon"></i>
                                    			@endif
                                    			@if($info->type == '3')
                                    				<i class="fa fa-google-plus-square social-icon"></i>
                                    			@endif
                                    			@if($info->type == '4')
                                    				<i class="fa fa-linkedin-square social-icon"></i>
                                    			@endif
                                    			@if($info->type == '6')
                                    				<i class="fa fa-instagram social-icon"></i>
                                    			@endif
                                    	</td>
                                        <td id="app_name_{{$info->id}}">{{$info->app_name}}</td>
                                        
                                        <td><label class="label label-info">{{$info->connections}}</label></td>
                                        <td>{{date('d M, Y', strtotime($info->date_added))}}</td>
                                        <td>
                                            <a href="javascript:editbutton({{$info->id}})" class="btn btn-blue btn-sm" title="Edit this app"><i class="entypo-pencil"></i></a> 
                                            <a id="{{$info->id}}" class="btn btn-red btn-sm remove_app" title="Remove this app"><i class="entypo-trash"></i></a> 
                                            @if ($info->is_default != 0)
                                                <a href="javascript:editdefault({{$info->id.','.$info->type}})" class="btn btn-blue buttondefault btn-sm" id="button-default{{$info->id}}">Default</a>
                                            @else
                                                <a href="javascript:editdefault({{$info->id.','.$info->type}});" class="btn btn-blue buttondefault btn-sm" id="button-default{{$info->id}}">Choose Default</a>
                                           @endif
                                            <img id="al_social_app_{{$info->id}}" src="{{$assets_dir.'/images/ajax-loader.gif'}}" class="hide"/>
                                        </td>
                                    </tr>
                              @endforeach				
                        </tbody>
                    </table>
                </div>
            </div>
        </div>		
        {!! $footer !!}
    </div>
    <!-- //$this->loadViewFile("modules/dashboard/view/chat.php"); -->
</div>

<div class="modal custom-width" id="popup" >
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Connect A New Social App</h3>
            </div>			
            <div class="modal-body">
                <div class="row text-center">
                    <div class="col-md-15 col-sm-3">
                        <a href="javascript:showaddnew(1);">
                            <i class="fa fa-facebook-square" style="font-size: 600%; color: #47639e;"></i>
                        </a>								
                        <p> Connect new Facebook Account </p>
                    </div>							
                    <div class="col-md-15 col-sm-3">
                        <a href="javascript:showaddnew(2);">
                            <i class="fa fa-twitter-square" style="font-size: 600%; color: #00abf0;"></i>
                        </a>								
                        <p> Connect new Twitter Account </p>
                    </div>							
                    <div class="col-md-15 col-sm-3">
                        <a href="javascript:showaddnew(3);">
                            <i class="fa fa-google-plus-square" style="font-size: 600%; color: #c0382a;"></i>
                        </a>								
                        <p> Connect new Google+ Account </p>
                    </div>							
                    <div class="col-md-15 col-sm-3">
                        <a href="javascript:showaddnew(4);">
                            <i class="fa fa-linkedin-square" style="font-size: 600%; color: #017eb4;"></i>
                        </a>								
                        <p> Connect new LinkedIn Account </p>
                    </div>
                    <div class="col-md-15 col-sm-3">
                        <a href="javascript:showaddnew(6);">
                            <i class="fa fa-instagram" style="font-size: 600%; color: #3f729b;"></i>
                        </a>								
                        <p> Connect new Instagram Account </p>
                    </div>
                </div>
            </div>			
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal custom-width" id="popup3" >
    <div class="modal-dialog" style="width: 70%;">
        <form id="form1" method="post" action="{{url('user/createsocialapp')}}" class="validate">
            <div class="modal-content">		
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title">Connect New<span  id="hsocial">  </span> Account</h3>
                </div>			
                <div class="modal-body">
                    <div class="row">
                        <div id="message" class="col-md-12"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="field-1" class="col-md-2 control-label text-right">App Name</label>							
                                <div class="col-md-10">
                                    <input type="text" class="form-control" id="facebookappname" name="facebookappname" data-validate="required">
                                </div>
                            </div>
                        </div>
                    </div>	
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="field-1" class="col-md-2 control-label text-right">App ID</label>							
                                <div class="col-md-10">
                                    <input type="text"  class="form-control test-app" id="facebookappid" name="facebookappid" data-validate="required">
                                    <p >Please enter your <span class="text-danger" id="socialid"> </span>here. If you dont't have one, you can create it <span class="social-link"></span>(please refer to video training for more information).</p>

                                </div>							
                            </div>						
                        </div>
                    </div>				

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="field-1" class="col-md-2 control-label text-right">App Secret</label>							
                                <div class="col-md-10">
                                    <input type="text" class="form-control test-app" name="facebookappsecrect" id="facebookappsecrect" data-validate="required" >
                                    <p >Please enter your <span class="text-danger" id="social-secrect"></span> here. If you don't have one, you can create it<span class="social-link"></span> (please refer to video traning for more).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>			
                <div class="modal-footer">
                    <input type="hidden" value="" name="facebookid" id="facebookid"/>
                    <input type="hidden" value="1" name="apptype" id="apptype"/>
                    <input type="hidden" value="" id="hdn_current_app_id"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <!--<button data-dismiss="modal" onclick="$('#deleteConfirm').modal('show');" class="btn btn-red">Delete</button>	-->
                    <button id="test_app" type="button" name="test_app" onclick="javascript:TestApp();" class="btn btn-red btn-icon icon-left"><i class="entypo-arrows-ccw"></i> Test App <img src="{{$assets_dir.'/images/ajax-loader.gif'}}" id="al_test_app" class="hide" alt="ajax-loader"/></button>
                    <button id="add_new_app" type="submit" name="submitsocial" class="btn btn-blue hide">Add</button>
                </div>			
            </div>
        </form>
    </div>
</div>
<div class="modal" id="modal-14">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Error</h4>
            </div>

            <div class="modal-body">
                <span id="resulterror" class="text-danger"> </span>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>				
            </div>
        </div>
    </div>
</div>
<div class="modal custom-width text-center" id="deleteConfirm" >
    <div class="modal-dialog" style="width: 35%; ">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <br /><br />

                <button data-dismiss="modal" id="delete" class="btn btn-red" onclick="deleteSocialAccount(22);
                        $('#deleteSuccess').modal('show');">Delete</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>
<div class="modal custom-width text-center" id="deleteSuccess" >
    <div class="modal-dialog" style="width: 35%; ">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Social Account Successfully Removed</h5>
                </br>
                </br>
                <button type="button" class="btn btn-info" data-dismiss="modal" onclick="changeSuccess()">OK</button>

            </div>

        </div>
    </div>
</div>
<div class="modal" id="social_app_alert_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{url('user/deleteapp')}}">
                <div id="modal_body" class="modal-body" style="font-weight:bold; color: black;"></div>
                <div id="modal_footer" class="modal-footer text-center"></div>
            </form>
        </div>
    </div>
</div>
<!--  //if (!isset($this->notice)) $this->notice = 'true'; -->
<!--<input type="hidden" value="//echo $this->notice;" id="notice-result" /> -->

<script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>

<script type="text/javascript">
                    $(document).ready(function ($) {
                        //showerror();
                        $('#conten-social').DataTable({
                            "processing": true,
                            "serverSide": false,
                            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            "autoWidth": false,
                            //"ajax": "{{url("ajax/GenerateOverview")}}",
                            "language": {
                                "sEmptyTable": "<span class='text-center' style='color:#ff002a;font-weight:bold;'>No data available in table</span>",
                                "paginate": {
                                    "previous": "<i class='entypo-left-open'></i>",
                                    "next": "<i class='entypo-right-open'></i>"
                                }
                            },
                            "dom": "<'row'<'col-sm-3 col-left'l><'col-sm-9 col-right'<'export-data'T>f>r>t<'row'<'col-sm-3 col-left'i><'col-sm-9 col-right'p>>",
                            "tableTools": {
                                "sSwfPath": "{{$assets_dir.'/js/datatables/extensions/table_tools/swf/copy_csv_xls_pdf.swf'}}"
                            },
                            "columnDefs": [
                                {"orderable": false, "targets": 2},
                                {"orderable": false, "targets": 4}
                            ]
                        });
                        $(".remove_app").click(function () {
                            var id = $(this).attr('id');
                            $("#modal_title").html('Delete Confirmation');
                            $("#modal_body").html('<i class="entypo-attention"></i> Are you sure, you want to delete this App?');
                            $("#modal_footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                                    '<button type="submit" class="btn btn-red">Yes</button><input type="hidden" name="app_id" value="' + id + '"/>');
                            $("#social_app_alert_modal").modal('show');
                        });

                        $(".test-app").keydown(function () {
                            $('#add_new_app').addClass('hide');
                            $("#test_app").removeClass('hide');
                            $("#message").html('');
                        });
                    });
                    /*function showerror()
                     {
                     var name = $('#notice-result').val();
                     if (name !== 'true')
                     {
                     if (name !== '')
                     {
                     $('#resulterror').html('Authentication failed!');
                     $('#modal-14').modal('show');
                     
                     } else {
                     $('#resulterror').html(name);
                     $('#modal-14').modal('show');
                     }
                     }
                     }*/
                    function updatesocial()
                    { //<a href="javascript:updatesocial();"class="btn btn-blue btn-icon">Add</a>
                        var facebookappid = $('#facebookappid').val();
                        var facebookappsecrect = $('#facebookappsecrect').val();
                        var facebookappname = $('#facebookappname').val();
                        var facebookid = $('#facebookid').val();
                        var apptype = $('#apptype').val();
                        var data = 'facebookappid=' + facebookappid + '&facebookappsecrect=' + facebookappsecrect + '&facebookappname=' + facebookappname + '&apptype=' + apptype + '&facebookid=' + facebookid;
                        var result = $.ajax({
                            type: 'POST',
                            url: '{{url("ajax/Updatesocial")}}',
                            data: data,
                            async: false
                        }).responseText;
                        //alert(result);
                        if (result !== '0')
                        {
                            $('#result-error').html('Error!');

                        } else
                        {
                            $('#popup3').modal('hide');
                            $('#result-error').html('');
                        }

                    }
                    function connectnew() {
                        $('#popup').modal('show');
                    }
                    function editdefault(id, type_id) {
                        $("#al_social_app_" + id).removeClass('hide');
                        var data = 'id=' + id + '&type_id=' + type_id;
                        $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '{{url("ajax/SetDefault")}}',
                            data: data,
                            success: function (data) {
                                $("#al_social_app_" + id).addClass('hide');
                                console.log(data);
                                console.log(1);
                                if (parseInt(data) === 1) {
                                    location.reload(true);
                                }
                            }
                        });
                    }
                    function showaddnew(id)
                    {
                        $('#result-error').html('');
                        $('#add_new_app').text('Add');
                        $("#test_app").removeClass('hide');
                        $("#add_new_app").addClass('hide');
                        if (id === 1) {
                            $('#hsocial').html(' Facebook');
                            $('#socialid').html('Facebook Application ID ');
                            $('.social-link').html('<a href="https://developers.facebook.com/"><span style="color:red;"> here</span></a> ');
                            $('#social-secrect').html('  Facebook Secret Key');
                            $('#facebookid').attr('value', '');
                            $('#facebookappid').val('');
                            $('#facebookappname').val('');
                            $('#facebookappsecrect').val('');
                            $('#apptype').attr('value', id);
                            $('#popup3').modal('show');
                            $('#popup').modal('hide');
                        }
                        else if (id === 2) {
                            $('#hsocial').html(' Twitter');
                            $('#socialid').html('Twitter Application ID ');
                            $('.social-link').html('<a href="https://dev.twitter.com/"><span style="color:red;"> here</span></a> ');
                            $('#social-secrect').html('  Twitter Secret Key');
                            $('#facebookid').attr('value', '');
                            $('#facebookappid').val('');
                            $('#facebookappname').val('');
                            $('#facebookappsecrect').val('');
                            $('#apptype').attr('value', id);
                            $('#popup3').modal('show');
                            $('#popup').modal('hide');
                        } else if (id === 3) {
                            $('#hsocial').html(' Google+');
                            $('#socialid').html('Google+Application ID ');
                            $('.social-link').html('<a href="https://console.developers.google.com/"><span style="color:red;"> here</span></a> ');
                            $('#social-secrect').html('  Google+ Secret Key');
                            $('#facebookid').attr('value', '');
                            $('#facebookappid').val('');
                            $('#facebookappname').val('');
                            $('#facebookappsecrect').val('');
                            $('#apptype').attr('value', id);
                            $('#popup3').modal('show');
                            $('#popup').modal('hide');
                        } else if (id === 4) {
                            $('#hsocial').html(' LinkedIn');
                            $('#socialid').html('LinkedIn Application ID ');
                            $('.social-link').html('<a href="https://developer.linkedin.com/"><span style="color:red;"> here</span></a> ');
                            $('#social-secrect').html('  LinkedIn Secret Key');
                            $('#facebookid').attr('value', '');
                            $('#facebookappid').val('');
                            $('#facebookappname').val('');
                            $('#facebookappsecrect').val('');
                            $('#apptype').attr('value', id);
                            $('#popup3').modal('show');
                            $('#popup').modal('hide');
                        }else if (id === 6) {
                            $('#hsocial').html(' Instagram');
                            $('#socialid').html('Instagram Application ID ');
                            $('.social-link').html('<a href="https://instagram.com/developer/register/"><span style="color:red;"> here</span></a> ');
                            $('#social-secrect').html('  Instagram Secret Key');
                            $('#facebookid').attr('value', '');
                            $('#facebookappid').val('');
                            $('#facebookappname').val('');
                            $('#facebookappsecrect').val('');
                            $('#apptype').attr('value', id);
                            $('#popup3').modal('show');
                            $('#popup').modal('hide');
                        }
                    }
                    function editbutton(id) {
                        $('#result-error').html('');
                        var data = 'id=' + id;
                        $("#al_social_app_" + id).removeClass('hide');
                        $.ajax({
                            type: 'POST',
                            cache: false,
                            url: '{{url("ajax/RetriveAppInfoById")}}',
                            data: data,
                            success: function (data) {

                                $('#add_new_app').addClass('hide');
                                $("#test_app").removeClass('hide');
                                $("#message").html('');

                                var getData = $.parseJSON(data);
                                $('#facebookid').attr('value', getData.id);
                                $('#facebookappid').val(getData.appid);
                                $('#facebookappname').val(getData.name);
                                $('#facebookappsecrect').val(getData.appsecrect);
                                $('#apptype').attr('value', getData.type);
                                $("#delete").attr('onclick', 'deleteSocialApp(' + id + '); $("#deleteSuccess").modal("show");');
                                $("div#deleteConfirm h4.modal-title").text("Are you sure you want to delete " + getData.name + "?");
                                $('#hdn_current_app_id').val(id);
                                $('#add_new_app').text('Update');
                                if (getData.type === '1') {
                                    $('#hsocial').html(' Facebook');
                                    $('#socialid').html('Facebook Application ID ');
                                    $('.social-link').html('<a href="https://developers.facebook.com/"><span style="color:red;"> here</span></a> ');
                                    $('#social-secrect').html('  Facebook Secret Key');
                                } else if (getData.type === '2') {
                                    $('#hsocial').html(' Twitter');
                                    $('#socialid').html('Twitter Application ID ');
                                    $('.social-link').html('<a href="https://dev.twitter.com/"><span style="color:red;"> here</span></a> ');
                                    $('#social-secrect').html('  Twitter Secret Key');
                                } else if (getData.type === '3') {
                                    $('#hsocial').html(' Google+');
                                    $('#socialid').html('Google+ Application ID ');
                                    $('.social-link').html('<a href="https://console.developers.google.com/"><span style="color:red;"> here</span></a> ');
                                    $('#social-secrect').html('  Google+ Secret Key');
                                } else if (getData.type === '4') {
                                    $('#hsocial').html(' LinkedIn');
                                    $('#socialid').html('LinkedIn Application ID ');
                                    $('.social-link').html('<a href="https://developer.linkedin.com/"><span style="color:red;"> here</span></a> ');
                                    $('#social-secrect').html('  LinkedIn Secret Key');
                                }
                                else if (getData.type === '6') {
                                    $('#hsocial').html(' Instagram');
                                    $('#socialid').html('Instagram Application ID ');
                                    $('.social-link').html('<a href="https://instagram.com/developer/register/"><span style="color:red;"> here</span></a> ');
                                    $('#social-secrect').html('  Instagram Secret Key');
                                }
                                $('#popup3').modal('show');
                                $("#al_social_app_" + id).addClass('hide');
                            }
                        });

                    }

//Delete Social App
                    function deleteSocialApp(id) {
                        $.post(
                                '{{url("ajax/DeleteSocialApp")}}',
                                {
                                    submit: "delete",
                                    id: id
                                },
                        function (data, status) {
                            if (status === 'success') {
                            } else {
                                alert("Delete " + status + "!");
                            }
                        },
                                "json"
                                );
                    }

                    function changeSuccess() {
                        location.reload();
                    }

                    function TestApp() {
                        $("#al_test_app").removeClass('hide');
                        var appname = $("#facebookappname").val();
                        var appid = $("#facebookappid").val();
                        var appsecret = $("#facebookappsecrect").val();
                        var apptype = $("#apptype").val();

                        if (appname !== "" && appid !== "" && appsecret !== "") {
                            var ajax_url = "";
                            if (apptype === '1') {
                                ajax_url = '{{url("ajax/CheckFacebookApp")}}';
                            } else if (apptype === '2') {
                                ajax_url = '{{url("ajax/CheckTwitterApp")}}';
                            } else if (apptype === '3') {
                                ajax_url = '{{url("ajax/CheckGoogleApp")}}';
                            } else if (apptype === '4') {
                                ajax_url = '{{url("ajax/CheckLinkedinApp")}}';
                            }else if(apptype === '6'){
                            	ajax_url = '{{url("ajax/CheckInstagramApp")}}';
                            }

                            $.ajax({
                                url: ajax_url,
                                data: {appid: appid, appsecret: appsecret},
                                type: 'post',
                                complete: function (result) {
                                    $("#al_test_app").addClass('hide');
                                    var output = result.responseText.trim('\n');                                    
                                    if (output === 'valid') {
                                        $("#message").html('');
                                        $("#test_app").addClass('hide');
                                        $("#add_new_app").removeClass('hide');
                                    } else {
                                        var msg = '<div class="alert alert-danger alert-dismissible fade in"><i class="entypo-cancel-circled"></i> Your provided app info is not valid.</div>';
                                        $("#message").html(msg);
                                        $("#test_app").removeClass('hide');
                                        $("#add_new_app").addClass('hide');
                                    }
                                }
                            });
                        }else{
                        	$("#al_test_app").addClass('hide');
                        }
                    }
</script>

<link rel="stylesheet" href="{{$assets_dir.'/js/datatables/css/dataTables.bootstrap.css'}}">

<!-- Bottom Scripts -->
<script src="{{$assets_dir.'/js/datatables/js/jquery.dataTables.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/extensions/table_tools/js/dataTables.tableTools.min.js'}}"></script>
<script src="{{$assets_dir.'/js/datatables/js/dataTables.bootstrap.js'}}"></script>
@endsection