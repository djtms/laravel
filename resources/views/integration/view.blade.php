@extends('template.layout')
@section('content')
<div class="page-container">
    @include('template.sidebarmenu', $menudata)
    <div class="main-content">
        @include('template.headermenu')    
        <hr />	
        <div class="row">
            <div class="col-md-4"><h2 style="margin-top:0px;"><i class="entypo-flash"></i>API Settings</h2></div>			
            <div class="col-md-8 text-right"></div>
        </div>
		{!! Session::get('SESSION_MESSAGE') !!}
		{{Session::forget('SESSION_MESSAGE')}}
        <div class="row">
            <div class="col-md-12">
               <div class="col-md-6">
                <div class="panel panel-default panel-shadow" data-collapsed="0" >
                    <div class="panel-heading">
                        <div class="panel-title">GetResponse</div>
                        <div class="panel-options">
                            
                        </div>
                    </div>
                    <div class="panel-body">
                        <img src="{{$assets_dir.'/images/autoresponder/getresponse.png'}}" height="60" style="margin-top: 10px; margin-bottom: 20px;" alt="img_getresponse"/>
                        <br/>
                        <form name="frm-getresponse" class="validate" method="POST" action="{{url('integration/savegetresponseapiinformation')}}">
                            <div class="form-group">
                                <label class="control-label">API Key</label>
                                <input type="text" name="gp_api_key" class="form-control" value="{{$getresponse['api_key']}}" placeholder="Your GetResponse API Key" required=""/>
                            </div>
                            <button type="submit" name="save" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            @if($getresponse['api_key'] != '')
                            <a href="javascript:deleteConfiration('getresponse');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Remove</a>
                            @endif
                        </form>
                    </div>
                </div>
               </div>

               <div class="col-md-6">
                <div class="panel panel-default panel-shadow" data-collapsed="0" >
                    <div class="panel-heading">
                        <div class="panel-title">MailChimp</div>
                        <div class="panel-options">
                            
                        </div>
                    </div>
                    <div class="panel-body">
                        <img src="{{$assets_dir.'/images/autoresponder/mailchimp.jpg'}}" height="60" style="margin-top: 10px; margin-bottom: 20px;" alt="img_mailchimp"/>
                        <br/>
                        <form name="frm-mailchimp" class="validate" method="POST" action="{{url('integration/savemailchimpapiinformation')}}">
                            <div class="form-group">
                                <label class="control-label">API Key</label>
                                <input type="text" name="mc_api_key" class="form-control" value="{{$mailchimp['api_key']}}" placeholder="Your MailChimp API Key" required=""/>
                            </div>
                            <button type="submit" name="save" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            @if($mailchimp['api_key'] != "")
                            <a href="javascript:deleteConfiration('mailchimp');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Remove</a>
                            @endif
                        </form>
                    </div>
                </div>
                </div>
               <div class="col-md-6">
                <div class="panel panel-default panel-shadow" data-collapsed="0" >
                    <div class="panel-heading">
                        <div class="panel-title">iContact</div>
                        <div class="panel-options">
                            
                        </div>
                    </div>
                    <div class="panel-body">
                        <img src="{{$assets_dir.'/images/autoresponder/icontact.png'}}" height="30" style="margin-top: 10px; margin-bottom: 20px;" alt="img_icontact"/>
                        <br/>
                        <form name="frm-icontact" class="validate" method="POST" action="{{url('integration/saveicontactapiinformation')}}">
                        	<div class="row">
                                <div class="form-group col-sm-4">
                                    <label class="control-label">API Key</label>
                                    <input type="text" name="ic_api_key" class="form-control" value="{{$icontact['api_key']}}" placeholder="Your iContact API Key" required=""/>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="control-label">API Username</label>
                                    <input type="text" name="ic_username" class="form-control" value="{{$icontact['username']}}" placeholder="Your iContact Username" required=""/>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="control-label">API Password</label>
                                    <input type="password" name="ic_password" class="form-control" value="{{$icontact['password']}}" placeholder="Your iContact Password" required=""/>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            @if($icontact['api_key'] != "")
                            <a href="javascript:deleteConfiration('icontact');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Remove</a>
                            @endif
                        </form>
                    </div>
                </div>
                </div>

                <div class="col-md-6">
                <div class="panel panel-default panel-shadow" data-collapsed="0" >
                    <div class="panel-heading">
                        <div class="panel-title">SendReach</div>
                        <div class="panel-options">
                            
                        </div>
                    </div>
                    <div class="panel-body">
                        <img src="{{$assets_dir.'/images/autoresponder/sendreach.png'}}" height="60" style="margin-top: 10px; margin-bottom: 20px;" alt="img_sendreach"/>
                        <br/>
                        <form name="frm-mailchimp" class="validate" method="POST" action="{{url('integration/savesendreachapiinformation')}}">
                        	<div class="row">
                                <div class="form-group col-sm-4">
                                    <label class="control-label">API Key</label>
                                    <input type="text" name="sr_api_key" class="form-control" value="{{$sendreach['api_key']}}" placeholder="Your SendReach API Key" required=""/>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="control-label">Secret</label>
                                    <input type="text" name="sr_secret" class="form-control" value="{{$sendreach['secret']}}" placeholder="Your SendReach Secret" required=""/>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="control-label">User ID</label>
                                    <input type="text" name="sr_userid" class="form-control" value="{{$sendreach['userid']}}" placeholder="Your SendReach User ID" required=""/>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            @if($sendreach['api_key'] != "")
                            <a href="javascript:deleteConfiration('sendreach');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Remove</a>
                            @endif
                        </form>
                    </div>
                </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-6">
                <div class="panel panel-default panel-shadow" data-collapsed="0" >
                    <div class="panel-heading">
                        <div class="panel-title">ActiveCamaign</div>
                        <div class="panel-options">
                            
                        </div>
                    </div>
                    <div class="panel-body">
                        <img src="{{$assets_dir.'/images/autoresponder/activecampaign.png'}}" height="60" style="margin-top: 10px; margin-bottom: 20px;" alt="img_activecampaign"/>
                        <br/>
                        <form name="frm-activecampaign" class="validate" method="POST" action="{{url('integration/saveactivecampaignapiinformation')}}">
                        	<div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="control-label">API Key</label>
                                    <input type="text" name="ac_api_key" class="form-control" value="{{$activecampaign['api_key']}}" placeholder="Your ActiveCampaign API Key" required=""/>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="control-label">URL</label>
                                    <input type="text" name="ac_url" class="form-control" value="{{$activecampaign['url']}}" placeholder="Your ActiveCampaign URL" required=""/>
                                </div>
                            </div>
                            <button type="submit" name="save" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            @if($activecampaign['api_key'] != "")
                            <a href="javascript:deleteConfiration('activecampaign');" class="btn btn-danger"><i class="fa fa-trash-o"></i> Remove</a>
                            @endif
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
       {!!  $footer !!}
    </div><!-- .main-content -->
</div><!-- .page-container -->
<div class="modal" id="modal_delete_api_settings">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{url('integration/deleteintegration')}}">
                <div id="modal_body" class="modal-body" style="font-weight: bold; color: black;">
                	<i class="entypo-attention"></i> Are you sure you want to delete this API Settings?
                </div>
                <div class="modal-footer text-center">
                	<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        			<button type="submit" class="btn btn-danger">Yes</button>
        			<input type="hidden" id="hdn_integration" name="hdn_integration" value=""/>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{$assets_dir.'/js/jquery.validate.min.js'}}"></script>
<script>
$(document).ready(function(){
	
});
function deleteConfiration(param){
	$("#hdn_integration").val(param);
	$("#modal_delete_api_settings").modal('show');
}
</script>
@endsection