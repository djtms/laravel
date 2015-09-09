@extends('template.layout')
@section('content')
<div class="page-container">	
     @include('template.sidebarmenu', $menudata)
    <div class="main-content">
		<div id="my-element"></div>
        @include('template.headermenu')
        <hr />
		<div class="row">
			<div class="col-md-12">
				<h2 style="margin-top: 0px;">
					<i class="fa fa-cubes"></i> Affiliate Dashboard
				</h2>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-md-12">
				@if($affiliate_dashboard_url != "")
				<iframe id="iframe_affiliate_dashboard" height="2500px" src="{{$affiliate_dashboard_url}}" width="100%" frameBorder="0"></iframe>
				@else
				<div class="page-error-404">
					<div class="error-symbol">
						<i class="entypo-attention"></i>
					</div>
					<div class="error-text">
						<h2>404</h2>
						<p>Nothing found!</p>
					</div>
				</div>
				@endif
			</div>
		</div>
		<br />
        {!! $footer !!}
    </div>
	<!-- /.main-content-->
</div>
<script type="text/javascript">
  $(document).ready(function(){
	  //console.log('This is iframe height: '+$('#iframe_affiliate_dashboard').find("body").height());
	  //$("#iframe_affiliate_dashboard").attr('style', iframe.$("body").outerHeight());
	  /*iframe.$(".toggle_div").bind("change", function () {
	      $("#iframe_affiliate_dashboard").css({
	          height: iframe.$("body").outerHeight()
	      });
	  });*/
	  //console.log('This is iframe height: '+$('#iframe_affiliate_dashboard').contents().find("body").height());
	  //$("#iframe_affiliate_dashboard").height( $(#iframe_affiliate_dashboard).contents().find("body").height() );
	  //var iframe = document.getElementById("iframe_affiliate_dashboard").contentWindow;
      /*var iFrameID = document.getElementById('iframe_affiliate_dashboard');
      
      if(iFrameID) {
            iFrameID.height = "";
            iFrameID.height = iFrameID.contentWindow.document.body.height + "px";
            console.log('This is iframe height: '+iFrameID.height);
      }  */
  });
</script>   
@endsection